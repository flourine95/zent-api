<?php

namespace App\Infrastructure\Models;

use Database\Factories\InventoryFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[UseFactory(InventoryFactory::class)]
class Inventory extends Model
{
    use HasFactory;

    protected $fillable = ['warehouse_id', 'product_variant_id', 'quantity', 'shelf_location'];

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
