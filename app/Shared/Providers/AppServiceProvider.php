<?php

namespace App\Shared\Providers;

use App\Domain\Banner\Observers\BannerObserver;
use App\Domain\Category\Observers\CategoryObserver;
use App\Domain\Config\Observers\SettingObserver;
use App\Domain\Product\Observers\ProductObserver;
use App\Infrastructure\Models\Banner;
use App\Infrastructure\Models\Category;
use App\Infrastructure\Models\Product;
use App\Infrastructure\Models\Setting;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register observers
        Category::observe(CategoryObserver::class);
        Product::observe(ProductObserver::class);
        Banner::observe(BannerObserver::class);
        Setting::observe(SettingObserver::class);

        // Implicitly grant "Super Admin" role all permissions
        // This works in the app by using gate-related functions like auth()->user->can() and @can()
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });
    }
}
