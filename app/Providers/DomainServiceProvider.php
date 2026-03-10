<?php

namespace App\Providers;

use App\Domain\Banner\Repositories\BannerRepositoryInterface;
use App\Domain\Category\Repositories\CategoryRepositoryInterface;
use App\Domain\Inventory\Repositories\InventoryRepositoryInterface;
use App\Domain\Order\Repositories\OrderRepositoryInterface;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Infrastructure\Repositories\EloquentBannerRepository;
use App\Infrastructure\Repositories\EloquentCategoryRepository;
use App\Infrastructure\Repositories\EloquentInventoryRepository;
use App\Infrastructure\Repositories\EloquentOrderRepository;
use App\Infrastructure\Repositories\EloquentProductRepository;
use App\Infrastructure\Repositories\EloquentUserRepository;
use Illuminate\Support\ServiceProvider;

class DomainServiceProvider extends ServiceProvider
{
    public array $bindings = [
        BannerRepositoryInterface::class => EloquentBannerRepository::class,
        CategoryRepositoryInterface::class => EloquentCategoryRepository::class,
        InventoryRepositoryInterface::class => EloquentInventoryRepository::class,
        OrderRepositoryInterface::class => EloquentOrderRepository::class,
        ProductRepositoryInterface::class => EloquentProductRepository::class,
        UserRepositoryInterface::class => EloquentUserRepository::class,
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
