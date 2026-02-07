<?php

namespace App\Filament\Exports;

use App\Models\Product;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class ProductExporter extends Exporter
{
    protected static ?string $model = Product::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),

            ExportColumn::make('category.name')
                ->label('Danh mục'),

            ExportColumn::make('name')
                ->label('Tên sản phẩm'),

            ExportColumn::make('slug')
                ->label('Slug'),

            ExportColumn::make('description')
                ->label('Mô tả')
                ->formatStateUsing(fn ($state) => strip_tags($state)),

            ExportColumn::make('thumbnail')
                ->label('Hình ảnh'),

            ExportColumn::make('is_active')
                ->label('Kích hoạt')
                ->formatStateUsing(fn ($state) => $state ? 'true' : 'false'),

            ExportColumn::make('specs')
                ->label('Thông số kỹ thuật')
                ->formatStateUsing(fn ($state) => json_encode($state, JSON_UNESCAPED_UNICODE)),

            ExportColumn::make('variants_count')
                ->label('Số biến thể')
                ->counts('variants'),

            ExportColumn::make('created_at')
                ->label('Ngày tạo'),

            ExportColumn::make('updated_at')
                ->label('Cập nhật'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Export sản phẩm hoàn tất: '.Number::format($export->successful_rows).' sản phẩm đã được export.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' sản phẩm export thất bại.';
        }

        return $body;
    }
}
