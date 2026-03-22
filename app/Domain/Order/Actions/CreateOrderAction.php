<?php

namespace App\Domain\Order\Actions;

use App\Domain\Address\Exceptions\AddressNotFoundException;
use App\Domain\Address\Repositories\AddressRepositoryInterface;
use App\Domain\Cart\Repositories\CartRepositoryInterface;
use App\Domain\Inventory\Repositories\InventoryRepositoryInterface;
use App\Domain\Order\DataTransferObjects\CreateOrderData;
use App\Domain\Order\Exceptions\InvalidOrderException;
use App\Domain\Order\Repositories\OrderRepositoryInterface;

final readonly class CreateOrderAction
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private CartRepositoryInterface $cartRepository,
        private AddressRepositoryInterface $addressRepository,
        private InventoryRepositoryInterface $inventoryRepository,
    ) {}

    /**
     * @throws InvalidOrderException
     * @throws AddressNotFoundException
     */
    public function execute(CreateOrderData $data): array
    {
        // Resolve shipping address
        $address = $data->addressId !== null
            ? $this->addressRepository->findById($data->addressId)
            : $this->addressRepository->getDefaultByUserId($data->userId);

        if ($address === null) {
            if ($data->addressId !== null) {
                throw AddressNotFoundException::withId($data->addressId);
            }
            throw InvalidOrderException::noShippingAddress();
        }

        // Load cart with items
        $cart = $this->cartRepository->getByUserIdWithItems($data->userId);

        if (empty($cart['items'])) {
            throw InvalidOrderException::emptyCart();
        }

        // Build order items from cart, resolving warehouse per variant
        $orderItems = [];
        $totalAmount = 0;

        foreach ($cart['items'] as $cartItem) {
            $variant = $cartItem['product_variant'];
            $quantity = $cartItem['quantity'];
            $price = (float) $variant['price'];
            $subtotal = $price * $quantity;

            $warehouseId = $this->inventoryRepository->findAvailableWarehouseForVariant(
                $variant['id'],
                $quantity
            );

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

        $orderData = [
            'user_id' => $data->userId,
            'code' => $data->code,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'total_amount' => $totalAmount,
            'shipping_address' => $address,
            'billing_address' => $address,
            'notes' => $data->notes,
        ];

        $order = $this->orderRepository->createWithReservations($orderData, $orderItems);

        // Clear cart after successful order
        $this->cartRepository->clearCart($data->userId);

        return $order;
    }
}
