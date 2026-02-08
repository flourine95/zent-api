<?php

namespace App\Filament\Resources\InventoryReservations\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class InventoryReservationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin đặt trước')
                    ->schema([
                        Select::make('inventory_id')
                            ->label('Tồn kho')
                            ->relationship('inventory', 'id')
                            ->getOptionLabelFromRecordUsing(function ($record) {
                                return "{$record->warehouse->name} - {$record->productVariant->sku} ({$record->quantity} sp)";
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpanFull(),

                        Select::make('order_id')
                            ->label('Đơn hàng')
                            ->relationship('order', 'code')
                            ->searchable()
                            ->preload()
                            ->columnSpanFull(),

                        TextInput::make('quantity')
                            ->label('Số lượng đặt trước')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->default(1)
                            ->suffix('sản phẩm'),

                        Select::make('status')
                            ->label('Trạng thái')
                            ->options([
                                'pending' => 'Chờ xác nhận',
                                'confirmed' => 'Đã xác nhận',
                                'released' => 'Đã giải phóng',
                                'expired' => 'Hết hạn',
                            ])
                            ->default('pending')
                            ->required(),

                        DateTimePicker::make('expires_at')
                            ->label('Hết hạn lúc')
                            ->required()
                            ->default(now()->addHours(24))
                            ->minDate(now())
                            ->seconds(false)
                            ->columnSpanFull(),

                        Textarea::make('notes')
                            ->label('Ghi chú')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
