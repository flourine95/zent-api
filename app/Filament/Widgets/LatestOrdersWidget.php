<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestOrdersWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Order::query()
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label(__('resources.orders.fields.order_number'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer_name')
                    ->label(__('resources.orders.fields.customer_name'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label(__('resources.orders.fields.total_amount'))
                    ->money('VND')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('resources.orders.fields.status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'processing' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('resources.orders.fields.created_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->heading(__('resources.orders.widgets.latest_orders'));
    }
}
