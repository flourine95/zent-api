<?php

namespace App\Providers;

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
        \App\Infrastructure\Models\Category::observe(\App\Observers\CategoryObserver::class);
        \App\Infrastructure\Models\Product::observe(\App\Observers\ProductObserver::class);
        \App\Infrastructure\Models\Banner::observe(\App\Observers\BannerObserver::class);
        \App\Models\Setting::observe(\App\Observers\SettingObserver::class);

        // Implicitly grant "Super Admin" role all permissions
        // This works in the app by using gate-related functions like auth()->user->can() and @can()
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });
    }
}
