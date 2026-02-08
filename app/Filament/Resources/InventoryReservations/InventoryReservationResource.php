<?php

namespace App\Filament\Resources\InventoryReservations;

use App\Filament\Resources\InventoryReservations\Pages\CreateInventoryReservation;
use App\Filament\Resources\InventoryReservations\Pages\EditInventoryReservation;
use App\Filament\Resources\InventoryReservations\Pages\ListInventoryReservations;
use App\Filament\Resources\InventoryReservations\Schemas\InventoryReservationForm;
use App\Filament\Resources\InventoryReservations\Tables\InventoryReservationsTable;
use App\Models\InventoryReservation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class InventoryReservationResource extends Resource
{
    protected static ?string $model = InventoryReservation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return 'Kho hàng';
    }

    public static function getNavigationLabel(): string
    {
        return 'Đặt trước';
    }

    public static function getModelLabel(): string
    {
        return 'Đặt trước';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Đặt trước';
    }

    public static function form(Schema $schema): Schema
    {
        return InventoryReservationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InventoryReservationsTable::configure($table);
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
            'index' => ListInventoryReservations::route('/'),
            'create' => CreateInventoryReservation::route('/create'),
            'view' => Pages\ViewInventoryReservation::route('/{record}'),
            'edit' => EditInventoryReservation::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('status', 'pending')
            ->where('expires_at', '>', now())
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }
}
