<?php

namespace App\Filament\Resources\Products\Tables;

use App\Filament\Exports\ProductExporter;
use App\Filament\Imports\ProductImporter;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\ImportAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('thumbnail')
                    ->label('Hình ảnh')
                    ->disk('public')
                    ->visibility('public')
                    ->size(60)
                    ->circular()
                    ->defaultImageUrl(url('/images/placeholder.svg'))
                    ->toggleable(),

                TextColumn::make('category.name')
                    ->label('Danh mục')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($record) => $record->category?->name ?? '-'),

                TextColumn::make('name')
                    ->label('Tên sản phẩm')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(fn ($record) => $record->slug),

                TextColumn::make('variants_count')
                    ->label('Biến thể')
                    ->counts('variants')
                    ->badge()
                    ->color('info')
                    ->alignCenter(),

                TextColumn::make('description')
                    ->label('Mô tả')
                    ->html()
                    ->limit(100)
                    ->formatStateUsing(fn ($record) => $record->formatted_description)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('specs')
                    ->label('Thông số kỹ thuật')
                    ->formatStateUsing(function ($state) {
                        if (empty($state)) {
                            return '-';
                        }

                        return collect($state)
                            ->map(fn ($value, $key) => "{$key}: {$value}")
                            ->take(3)
                            ->join(', ');
                    })
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Đã sao chép slug')
                    ->badge()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_active')
                    ->label('Kích hoạt')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Cập nhật')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Danh mục')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                SelectFilter::make('is_active')
                    ->label('Trạng thái')
                    ->options([
                        '1' => 'Kích hoạt',
                        '0' => 'Không kích hoạt',
                    ])
                    ->native(false),

                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->headerActions([
                ImportAction::make()
                    ->importer(ProductImporter::class)
                    ->label('Import sản phẩm')
                    ->color('success')
                    ->icon('heroicon-o-arrow-down-tray'),
                ExportAction::make()
                    ->exporter(ProductExporter::class)
                    ->label('Export sản phẩm')
                    ->color('warning')
                    ->icon('heroicon-o-arrow-up-tray'),
            ])
            ->toolbarActions([
                ActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Chưa có sản phẩm nào')
            ->emptyStateDescription('Bắt đầu bằng cách tạo sản phẩm đầu tiên của bạn.')
            ->emptyStateIcon('heroicon-o-cube')
            ->defaultSort('created_at', 'desc');
    }
}
