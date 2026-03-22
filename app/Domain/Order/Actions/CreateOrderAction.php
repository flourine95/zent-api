<?php

namespace App\Domain\Order\Actions;

use App\Domain\Address\Exceptions\AddressNotFoundException;
use App\Domain\Address\Exceptions\UnauthorizedAddressAccessException;
use App\Domain\Address\Repositories\AddressRepositoryInterface;
use App\Domain\Cart\Repositories\CartRepositoryInterface;
use App\Domain\Inventory\Exceptions\InsufficientStockException;
use App\Domain\Inventory\Repositories\InventoryRepositoryInterface;
use App\Domain\Inventory\Services\InventoryCacheServiceInterface;
use App\Domain\Order\DataTransferObjects\CreateOrderData;
use App\Domain\Order\Exceptions\InvalidOrderException;
use App\Domain\Order\Services\OrderDispatchServiceInterface;

final readonly class CreateOrderAction
{
    public function __construct(
        private CartRepositoryInterface $cartRepository,
        private AddressRepositoryInterface $addressRepository,
        private InventoryRepositoryInterface $inventoryRepository,
        private InventoryCacheServiceInterface $inventoryCache,
        private OrderDispatchServiceInterface $orderDispatch,
    ) {}

    /**
     * @throws InvalidOrderException
     * @throws AddressNotFoundException
     * @throws UnauthorizedAddressAccessException
     * @throws InsufficientStockException
     */
    public function execute(CreateOrderData $data): array
    {
        // Resolve shipping address
        $shippingAddress = $data->addressId !== null
            ? $this->addressRepository->findById($data->addressId)
            : $this->addressRepository->getDefaultByUserId($data->userId);

        if ($shippingAddress === null) {
            if ($data->addressId !== null) {
                throw AddressNotFoundException::withId($data->addressId);
            }
            throw InvalidOrderException::noShippingAddress();
        }

        if ($data->addressId !== null && ! $this->addressRepository->belongsToUser($data->addressId, $data->userId)) {
            throw UnauthorizedAddressAccessException::message();
        }

        // Resolve billing address — fallback to shipping address if not provided
        if ($data->billingAddressId !== null) {
            if (! $this->addressRepository->belongsToUser($data->billingAddressId, $data->userId)) {
                throw UnauthorizedAddressAccessException::message();
            }

            $billingAddress = $this->addressRepository->findById($data->billingAddressId);

            if ($billingAddress === null) {
                throw AddressNotFoundException::withId($data->billingAddressId);
            }
        } else {
            $billingAddress = $shippingAddress;
        }

        // Load cart
        $cart = $this->cartRepository->getByUserIdWithItems($data->userId);

        if (empty($cart['items'])) {
            throw InvalidOrderException::emptyCart();
        }

        // Batch-resolve warehouse per variant (1 query)
        $variantQuantities = [];
        foreach ($cart['items'] as $item) {
            $variantQuantities[$item['product_variant']['id']] = $item['quantity'];
        }

        $warehouseMap = $this->inventoryRepository->findAvailableWarehousesForVariants($variantQuantities);

        // Build order items
        $orderItems = [];
        $totalAmount = 0;

        foreach ($cart['items'] as $item) {
            $variant = $item['product_variant'];
            $quantity = $item['quantity'];
            $price = (float) $variant['price'];
            $subtotal = $price * $quantity;
            $warehouseId = $warehouseMap[$variant['id']] ?? null;

            if ($warehouseId === null) {
                throw InvalidOrderException::noWarehouseAvailable($variant['id']);
            }

            $orderItems[] = [
                'product_variant_id' => $variant['id'],
                'warehouse_id' => $warehouseId,
                'quantity' => $quantity,
                'price' => $price,
                'subtotal' => $subtotal,
                'product_snapshot' => [
                    'name' => $variant['product']['name'],
                    'sku' => $variant['sku'],
                    'options' => $variant['options'],
                    'thumbnail' => $variant['product']['thumbnail'],
                ],
            ];

            $totalAmount += $subtotal;
        }

        // Atomic Redis decrement — throws InsufficientStockException if any variant is short
        $this->inventoryCache->decrementBatch($variantQuantities);

        $orderCode = 'ORD-'.strtoupper(bin2hex(random_bytes(5)));

        $orderData = [
            'user_id' => $data->userId,
            'code' => $orderCode,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'total_amount' => $totalAmount,
            'shipping_address' => $shippingAddress,
            'billing_address' => $billingAddress,
            'notes' => $data->notes,
        ];

        // Dispatch async DB write — stores snapshot for idempotency, clears cart
        $this->orderDispatch->dispatch(
            orderId: $data->orderId,
            userId: $data->userId,
            orderData: $orderData,
            items: $orderItems,
            variantQuantities: $variantQuantities,
        );

        $this->cartRepository->clearCart($data->userId);

        // Return immediately — DB write happens async
        return array_merge(['id' => $data->orderId], $orderData, ['items' => $orderItems]);
    }
}
