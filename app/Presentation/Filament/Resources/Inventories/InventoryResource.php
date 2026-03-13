<?php

namespace App\Presentation\Filament\Resources\Inventories;

use App\Infrastructure\Models\Inventory;
use App\Presentation\Filament\Resources\Inventories\Pages\CreateInventory;
use App\Presentation\Filament\Resources\Inventories\Pages\EditInventory;
use App\Presentation\Filament\Resources\Inventories\Pages\ListInventories;
use App\Presentation\Filament\Resources\Inventories\Schemas\InventoryForm;
use App\Presentation\Filament\Resources\Inventories\Tables\InventoriesTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class InventoryResource extends Resource
{
    protected static ?string $model = Inventory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return 'Kho hàng';
    }

    public static function getNavigationLabel(): string
    {
        return 'Tồn kho';
    }

    public static function getModelLabel(): string
    {
        return 'Tồn kho';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Tồn kho';
    }

    public static function form(Schema $schema): Schema
    {
        return InventoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InventoriesTable::configure($table);
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
            'index' => ListInventories::route('/'),
            'create' => CreateInventory::route('/create'),
            'view' => Pages\ViewInventory::route('/{record}'),
            'edit' => EditInventory::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('quantity', '<=', 10)->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $lowStock = static::getModel()::where('quantity', '<=', 10)->count();

        return $lowStock > 0 ? 'warning' : null;
    }
}
