<?php

namespace App\Filament\Resources\Warehouses\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class WarehouseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Tên kho')
                    ->required()
                    ->maxLength(255),
                TextInput::make('code')
                    ->label('Mã kho')
                    ->required()
                    ->maxLength(50)
                    ->unique(ignoreRecord: true),
                TextInput::make('address')
                    ->label('Địa chỉ')
                    ->maxLength(500),
                Toggle::make('is_active')
                    ->label('Kích hoạt')
                    ->default(true)
                    ->required(),
            ]);
    }
}
