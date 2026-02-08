<?php

namespace App\Filament\Resources\Inventories\Schemas;

use App\Models\ProductVariant;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class InventoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin tồn kho')
                    ->schema([
                        Select::make('warehouse_id')
                            ->label('Kho')
                            ->relationship('warehouse', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpanFull(),

                        Select::make('product_variant_id')
                            ->label('Biến thể sản phẩm')
                            ->relationship('productVariant', 'sku')
                            ->getOptionLabelFromRecordUsing(fn (ProductVariant $record) => $record->full_name)
                            ->searchable(['sku'])
                            ->preload()
                            ->required()
                            ->columnSpanFull(),

                        TextInput::make('quantity')
                            ->label('Số lượng')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->suffix('sản phẩm')
                            ->helperText('Số lượng tồn kho hiện tại'),

                        TextInput::make('shelf_location')
                            ->label('Vị trí kệ')
                            ->maxLength(255)
                            ->placeholder('Ví dụ: A-01-05')
                            ->helperText('Vị trí lưu trữ trong kho'),
                    ])
                    ->columns(2),
            ]);
    }
}
