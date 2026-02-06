<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    use SoftDeletes;

    protected $fillable = ['product_id', 'sku', 'price', 'original_price', 'images', 'options'];

    protected $casts = [
        'options' => 'array',
        'images' => 'array',
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }

    public function getFullNameAttribute()
    {
        if (empty($this->options)) {
            return $this->product->name.' - '.$this->sku;
        }

        $optionsString = collect($this->options)
            ->map(fn ($value, $key) => "{$key}: {$value}")
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
