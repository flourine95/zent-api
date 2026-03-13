<?php

namespace App\Presentation\Filament\Resources\Banners;

use App\Infrastructure\Models\Banner;
use App\Presentation\Filament\Resources\Banners\Pages\CreateBanner;
use App\Presentation\Filament\Resources\Banners\Pages\EditBanner;
use App\Presentation\Filament\Resources\Banners\Pages\ListBanners;
use App\Presentation\Filament\Resources\Banners\Schemas\BannerForm;
use App\Presentation\Filament\Resources\Banners\Tables\BannersTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class BannerResource extends Resource
{
    protected static ?string $model = Banner::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-photo';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return 'Content Management';
    }

    public static function form(Schema $schema): Schema
    {
        return BannerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BannersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBanners::route('/'),
            'create' => CreateBanner::route('/create'),
            'edit' => EditBanner::route('/{record}/edit'),
        ];
    }
}
