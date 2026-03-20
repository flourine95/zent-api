<?php

use App\App\Address\Controllers\AddressController;
use App\App\Banner\Controllers\BannerController;
use App\App\Cart\Controllers\CartController;
use App\App\Category\Controllers\CategoryController;
use App\App\Config\Controllers\ConfigController;
use App\App\Inventory\Controllers\InventoryController;
use App\App\Notification\Controllers\NotificationController;
use App\App\Order\Controllers\OrderController;
use App\App\Product\Controllers\ProductController;
use App\App\ProductVariant\Controllers\ProductVariantController;
use App\App\Shipping\Controllers\ShipmentController;
use App\App\Shipping\Controllers\ShippingController;
use App\App\User\Controllers\AuthController;
use App\App\User\Controllers\ProfileController;
use App\App\Wishlist\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public API routes
Route::prefix('v1')->group(function () {

    // App Configuration (load on first visit)
    Route::get('/config', [ConfigController::class, 'index']);

    // Auth routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Products API (public)
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{identifier}', [ProductController::class, 'show']);
    Route::get('/products/{identifier}/variants', [ProductVariantController::class, 'index']);

    // Categories API (public)
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/tree', [CategoryController::class, 'tree']);
    Route::get('/categories/{identifier}', [CategoryController::class, 'show']);

    // Banners (public)
    Route::get('/banners', [BannerController::class, 'index']);
    Route::get('/banners/active', [BannerController::class, 'active']);
    Route::get('/banners/position/{position}', [BannerController::class, 'byPosition']);
    Route::get('/banners/{id}', [BannerController::class, 'show']);

    // Product Variants - Check inventory (public)
    Route::get('/variants/{variantId}/inventory', [ProductVariantController::class, 'checkInventory']);

    // Shipping (public)
    Route::prefix('shipping')->group(function () {
        Route::post('/calculate-fees', [ShippingController::class, 'calculateFees']);
        Route::get('/providers', [ShippingController::class, 'getProviders']);
        Route::get('/settings', [ShippingController::class, 'getSettings']);
    });

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        // Auth
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);

        // Profile
        Route::put('/profile', [ProfileController::class, 'update']);
        Route::put('/profile/password', [ProfileController::class, 'updatePassword']);

        // Addresses
        Route::get('/addresses', [AddressController::class, 'index']);
        Route::post('/addresses', [AddressController::class, 'store']);
        Route::put('/addresses/{address}', [AddressController::class, 'update']);
        Route::delete('/addresses/{address}', [AddressController::class, 'destroy']);
        Route::post('/addresses/{address}/set-default', [AddressController::class, 'setDefault']);

        // Cart
        Route::get('/cart', [CartController::class, 'index']);
        Route::post('/cart/items', [CartController::class, 'addItem']);
        Route::put('/cart/items/{itemId}', [CartController::class, 'updateItem']);
        Route::delete('/cart/items/{itemId}', [CartController::class, 'removeItem']);
        Route::delete('/cart/clear', [CartController::class, 'clear']);

        // Wishlist
        Route::get('/wishlist', [WishlistController::class, 'index']);
        Route::post('/wishlist', [WishlistController::class, 'store']);
        Route::delete('/wishlist/{productId}', [WishlistController::class, 'destroy']);
        Route::get('/wishlist/check/{productId}', [WishlistController::class, 'check']);

        // Orders
        Route::get('/orders', [OrderController::class, 'index']);
        Route::post('/orders', [OrderController::class, 'store']);
        Route::get('/orders/{order}', [OrderController::class, 'show']);
        Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel']);

        // Shipments (per order)
        Route::post('/orders/{order}/shipment', [ShipmentController::class, 'store']);
        Route::get('/orders/{order}/shipment', [ShipmentController::class, 'show']);
        Route::post('/orders/{order}/shipment/cancel', [ShipmentController::class, 'cancel']);

        // Notifications
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);

        // Admin routes
        Route::middleware('role:admin')->group(function () {
            // Products (write)
            Route::post('/products', [ProductController::class, 'store']);
            Route::put('/products/{id}', [ProductController::class, 'update']);
            Route::delete('/products/{id}', [ProductController::class, 'destroy']);

            // Categories (write)
            Route::post('/categories', [CategoryController::class, 'store']);
            Route::put('/categories/{id}', [CategoryController::class, 'update']);
            Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

            // Banners (write)
            Route::post('/banners', [BannerController::class, 'store']);
            Route::put('/banners/{id}', [BannerController::class, 'update']);
            Route::delete('/banners/{id}', [BannerController::class, 'destroy']);

            // Inventory
            Route::get('/inventory', [InventoryController::class, 'index']);
            Route::get('/inventory/low-stock', [InventoryController::class, 'lowStock']);
            Route::get('/inventory/low-stock/{threshold}', [InventoryController::class, 'lowStock']);
            Route::get('/inventory/warehouse/{warehouseId}', [InventoryController::class, 'byWarehouse']);
            Route::get('/inventory/variant/{productVariantId}', [InventoryController::class, 'byProductVariant']);
            Route::get('/inventory/{id}', [InventoryController::class, 'show']);
            Route::post('/inventory', [InventoryController::class, 'store']);
            Route::put('/inventory/{id}', [InventoryController::class, 'update']);
            Route::post('/inventory/{id}/adjust', [InventoryController::class, 'adjust']);
        });
    });
});
