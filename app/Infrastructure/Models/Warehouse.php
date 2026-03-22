<?php

namespace App\Infrastructure\Models;

use Database\Factories\WarehouseFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[UseFactory(WarehouseFactory::class)]
class Warehouse extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = ['name', 'code', 'address', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class)->chaperone();
    }
}
