<?php

namespace App\Infrastructure\Models;

use Database\Factories\ProductVariantFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[UseFactory(ProductVariantFactory::class)]
class ProductVariant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['product_id', 'sku', 'price', 'original_price', 'images', 'options'];

    public function casts(): array
    {
        return [
            'options' => 'array',
            'images' => 'array',
            'price' => 'decimal:2',
            'original_price' => 'decimal:2',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class)->chaperone();
    }

    public function getFullNameAttribute()
    {
        if (empty($this->options) || ! \is_array($this->options)) {
            return $this->product->name.' - '.$this->sku;
        }

        $optionsString = collect($this->options)
            ->map(fn ($option) => ($option['attribute'] ?? '').' : '.($option['value'] ?? ''))
            ->filter()
            ->join(' / ');

        return $this->product->name.' - '.$optionsString;
    }

    /**
     * Get the main image (first image in the array)
     */
    public function getMainImageAttribute(): ?string
    {
        return $this->images[0] ?? null;
    }

    /**
     * Get all images as URLs
     */
    public function getImageUrlsAttribute(): array
    {
        if (empty($this->images)) {
            return [];
        }

        return collect($this->images)
            ->map(fn ($image) => asset("storage/{$image}"))
            ->toArray();
    }
}
