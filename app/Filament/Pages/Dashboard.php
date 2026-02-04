<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function getTitle(): string
    {
        return __('navigation.dashboard');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.dashboard');
    }

    public function getColumns(): int | array
    {
        return [
            'md' => 2,
            'xl' => 3,
        ];
    }
}
