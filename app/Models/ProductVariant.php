<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    use SoftDeletes;

    protected $fillable = ['product_id', 'sku', 'price', 'original_price', 'image', 'options'];

    protected $casts = [
        'options' => 'array',
        'price' => 'decimal:2',
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
        $options = implode(' / ', $this->options ?? []);
        return $this->product->name . ' - ' . $options;
    }
}
