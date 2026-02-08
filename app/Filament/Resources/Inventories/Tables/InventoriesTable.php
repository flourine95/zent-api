<?php

namespace App\Filament\Resources\Inventories\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class InventoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('warehouse.name')
                    ->label('Kho')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('productVariant.sku')
                    ->label('SKU')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('SKU đã được sao chép!')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('productVariant.product.name')
                    ->label('Sản phẩm')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                TextColumn::make('quantity')
                    ->label('Số lượng')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state <= 0 => 'danger',
                        $state <= 10 => 'warning',
                        $state <= 50 => 'info',
                        default => 'success',
                    })
                    ->suffix(' sp'),

                TextColumn::make('shelf_location')
                    ->label('Vị trí kệ')
                    ->searchable()
                    ->placeholder('Chưa xác định')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Cập nhật lúc')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('warehouse_id')
                    ->label('Kho')
                    ->relationship('warehouse', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('stock_status')
                    ->label('Trạng thái tồn kho')
                    ->options([
                        'out_of_stock' => 'Hết hàng',
                        'low_stock' => 'Sắp hết (≤10)',
                        'in_stock' => 'Còn hàng',
                    ])
                    ->query(function ($query, $state) {
                        return match ($state['value'] ?? null) {
                            'out_of_stock' => $query->where('quantity', '<=', 0),
                            'low_stock' => $query->where('quantity', '>', 0)->where('quantity', '<=', 10),
                            'in_stock' => $query->where('quantity', '>', 10),
                            default => $query,
                        };
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                ActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('quantity', 'asc')
            ->emptyStateHeading('Chưa có tồn kho nào')
            ->emptyStateDescription('Tạo bản ghi tồn kho để theo dõi số lượng sản phẩm trong kho.')
            ->emptyStateIcon('heroicon-o-rectangle-stack');
    }
}
