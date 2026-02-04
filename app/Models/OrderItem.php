<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = ['order_id', 'product_variant_id', 'warehouse_id', 'quantity', 'price', 'product_snapshot'];

    protected $casts = [
        'product_snapshot' => 'array',
        'price' => 'decimal:2',
    ];
}
