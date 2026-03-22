<?php

namespace App\Shared\Providers;

use App\Domain\Address\Repositories\AddressRepositoryInterface;
use App\Domain\Banner\Repositories\BannerRepositoryInterface;
use App\Domain\Cart\Repositories\CartRepositoryInterface;
use App\Domain\Category\Repositories\CategoryRepositoryInterface;
use App\Domain\Config\Repositories\ConfigRepositoryInterface;
use App\Domain\Inventory\Repositories\InventoryRepositoryInterface;
use App\Domain\Inventory\Services\InventoryCacheServiceInterface;
use App\Domain\Notification\Repositories\NotificationRepositoryInterface;
use App\Domain\Order\Repositories\OrderRepositoryInterface;
use App\Domain\Order\Services\OrderDispatchServiceInterface;
use App\Domain\Order\Services\OrderNotificationServiceInterface;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Domain\ProductVariant\Repositories\ProductVariantRepositoryInterface;
use App\Domain\Shipping\Repositories\ShipmentRepositoryInterface;
use App\Domain\Shipping\Repositories\ShippingRepositoryInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\Warehouse\Repositories\WarehouseRepositoryInterface;
use App\Domain\Wishlist\Repositories\WishlistRepositoryInterface;
use App\Infrastructure\Repositories\EloquentAddressRepository;
use App\Infrastructure\Repositories\EloquentBannerRepository;
use App\Infrastructure\Repositories\EloquentCartRepository;
use App\Infrastructure\Repositories\EloquentCategoryRepository;
use App\Infrastructure\Repositories\EloquentConfigRepository;
use App\Infrastructure\Repositories\EloquentInventoryRepository;
use App\Infrastructure\Repositories\EloquentNotificationRepository;
use App\Infrastructure\Repositories\EloquentOrderRepository;
use App\Infrastructure\Repositories\EloquentProductRepository;
use App\Infrastructure\Repositories\EloquentProductVariantRepository;
use App\Infrastructure\Repositories\EloquentShipmentRepository;
use App\Infrastructure\Repositories\EloquentShippingRepository;
use App\Infrastructure\Repositories\EloquentUserRepository;
use App\Infrastructure\Repositories\EloquentWarehouseRepository;
use App\Infrastructure\Repositories\EloquentWishlistRepository;
use App\Infrastructure\Services\InventoryCacheService;
use App\Infrastructure\Services\OrderDispatchService;
use App\Infrastructure\Services\OrderNotificationService;
use Illuminate\Support\ServiceProvider;

class DomainServiceProvider extends ServiceProvider
{
    public array $bindings = [
        AddressRepositoryInterface::class => EloquentAddressRepository::class,
        BannerRepositoryInterface::class => EloquentBannerRepository::class,
        CartRepositoryInterface::class => EloquentCartRepository::class,
        CategoryRepositoryInterface::class => EloquentCategoryRepository::class,
        ConfigRepositoryInterface::class => EloquentConfigRepository::class,
        InventoryRepositoryInterface::class => EloquentInventoryRepository::class,
        InventoryCacheServiceInterface::class => InventoryCacheService::class,
        NotificationRepositoryInterface::class => EloquentNotificationRepository::class,
        OrderRepositoryInterface::class => EloquentOrderRepository::class,
        OrderDispatchServiceInterface::class => OrderDispatchService::class,
        OrderNotificationServiceInterface::class => OrderNotificationService::class,
        ProductRepositoryInterface::class => EloquentProductRepository::class,
        ProductVariantRepositoryInterface::class => EloquentProductVariantRepository::class,
        ShipmentRepositoryInterface::class => EloquentShipmentRepository::class,
        ShippingRepositoryInterface::class => EloquentShippingRepository::class,
        UserRepositoryInterface::class => EloquentUserRepository::class,
        WarehouseRepositoryInterface::class => EloquentWarehouseRepository::class,
        WishlistRepositoryInterface::class => EloquentWishlistRepository::class,
    ];

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        //
    }
}
