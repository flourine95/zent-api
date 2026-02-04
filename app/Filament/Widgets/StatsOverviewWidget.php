<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use App\Models\Order;
use App\Models\Post;
use App\Models\Product;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make(__('resources.users.label'), User::count())
                ->description(__('resources.users.stats_description'))
                ->descriptionIcon('heroicon-m-users')
                ->color('success')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3]),

            Stat::make(__('resources.products.label'), Product::count())
                ->description(__('resources.products.stats_description'))
                ->descriptionIcon('heroicon-m-cube')
                ->color('info')
                ->chart([3, 5, 7, 4, 6, 8, 7, 9]),

            Stat::make(__('resources.orders.label'), Order::count())
                ->description(__('resources.orders.stats_description'))
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('warning')
                ->chart([5, 7, 4, 8, 6, 9, 7, 10]),

            Stat::make(__('resources.posts.label'), Post::count())
                ->description(__('resources.posts.stats_description'))
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),

            Stat::make(__('resources.categories.label'), Category::count())
                ->description(__('resources.categories.stats_description'))
                ->descriptionIcon('heroicon-m-folder')
                ->color('gray'),
        ];
    }
}
