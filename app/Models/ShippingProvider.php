<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShippingProvider extends Model
{
    protected $fillable = [
        'code',
        'name',
        'is_active',
        'config',
        'priority',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'config' => 'encrypted:array', // Encrypt entire config for security
            'priority' => 'integer',
        ];
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class, 'provider_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCode($query, string $code)
    {
        return $query->where('code', $code);
    }

    // Provider codes
    public const GHTK = 'ghtk';

    public const GHN = 'ghn';

    public const VIETTEL = 'viettel';

    public const JNT = 'jnt';
}
