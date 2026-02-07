<?php

namespace App\Filament\Imports;

use App\Models\Product;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class ProductImporter extends Importer
{
    protected static ?string $model = Product::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('category_id')
                ->label('ID Danh mục')
                ->requiredMapping()
                ->relationship(resolveUsing: 'id')
                ->rules(['required', 'exists:categories,id'])
                ->example('1'),

            ImportColumn::make('name')
                ->label('Tên sản phẩm')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->example('Áo thun nam'),

            ImportColumn::make('slug')
                ->label('Slug')
                ->rules(['nullable', 'max:255', 'unique:products,slug'])
                ->example('ao-thun-nam'),

            ImportColumn::make('description')
                ->label('Mô tả')
                ->rules(['nullable'])
                ->example('Áo thun chất liệu cotton cao cấp'),

            ImportColumn::make('thumbnail')
                ->label('Hình ảnh')
                ->rules(['nullable', 'max:255'])
                ->example('products/thumbnails/image.jpg'),

            ImportColumn::make('is_active')
                ->label('Kích hoạt')
                ->boolean()
                ->rules(['boolean'])
                ->example('true'),

            ImportColumn::make('specs')
                ->label('Thông số kỹ thuật (JSON)')
                ->rules(['nullable', 'json'])
                ->example('[{"name":"Chất liệu","value":"Cotton"}]'),
        ];
    }

    public function resolveRecord(): Product
    {
        // Tìm hoặc tạo mới dựa trên slug
        if (! empty($this->data['slug'])) {
            return Product::firstOrNew([
                'slug' => $this->data['slug'],
            ]);
        }

        return new Product;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Import sản phẩm hoàn tất: '.Number::format($import->successful_rows).' sản phẩm đã được import.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' sản phẩm import thất bại.';
        }

        return $body;
    }
}
