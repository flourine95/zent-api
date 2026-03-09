<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shipment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_id',
        'provider_id',
        'provider_order_id',
        'tracking_number',
        'label_id',
        'status',
        'provider_status',
        'status_note',
        'fee',
        'insurance_fee',
        'cod_amount',
        'declared_value',
        'weight',
        'customer_info',
        'pickup_info',
        'estimated_pickup_at',
        'estimated_delivery_at',
        'actual_pickup_at',
        'actual_delivery_at',
        'provider_metadata',
        'products',
        'is_freeship',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'customer_info' => 'array',
            'pickup_info' => 'array',
            'provider_metadata' => 'array',
            'products' => 'array',
            'is_freeship' => 'boolean',
            'estimated_pickup_at' => 'datetime',
            'estimated_delivery_at' => 'datetime',
            'actual_pickup_at' => 'datetime',
            'actual_delivery_at' => 'datetime',
        ];
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(ShippingProvider::class, 'provider_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(ShipmentStatusHistory::class);
    }

    // Standardized status constants
    public const STATUS_PENDING = 'pending';

    public const STATUS_CONFIRMED = 'confirmed';

    public const STATUS_PICKING = 'picking';

    public const STATUS_PICKED = 'picked';

    public const STATUS_IN_TRANSIT = 'in_transit';

    public const STATUS_DELIVERING = 'delivering';

    public const STATUS_DELIVERED = 'delivered';

    public const STATUS_RETURNING = 'returning';

    public const STATUS_RETURNED = 'returned';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_LOST = 'lost';

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Chờ xử lý',
            self::STATUS_CONFIRMED => 'Đã xác nhận',
            self::STATUS_PICKING => 'Đang lấy hàng',
            self::STATUS_PICKED => 'Đã lấy hàng',
            self::STATUS_IN_TRANSIT => 'Đang vận chuyển',
            self::STATUS_DELIVERING => 'Đang giao hàng',
            self::STATUS_DELIVERED => 'Đã giao hàng',
            self::STATUS_RETURNING => 'Đang hoàn trả',
            self::STATUS_RETURNED => 'Đã hoàn trả',
            self::STATUS_CANCELLED => 'Đã hủy',
            self::STATUS_LOST => 'Thất lạc',
            default => 'Không xác định',
        };
    }

    public function isDelivered(): bool
    {
        return $this->status === self::STATUS_DELIVERED;
    }

    public function isCancellable(): bool
    {
        return in_array($this->status, [
            self::STATUS_PENDING,
            self::STATUS_CONFIRMED,
            self::STATUS_PICKING,
        ]);
    }

    public function isInTransit(): bool
    {
        return in_array($this->status, [
            self::STATUS_PICKED,
            self::STATUS_IN_TRANSIT,
            self::STATUS_DELIVERING,
        ]);
    }

    public function updateStatus(string $status, ?string $providerStatus = null, ?string $note = null): void
    {
        $this->update([
            'status' => $status,
            'provider_status' => $providerStatus ?? $this->provider_status,
            'status_note' => $note,
        ]);

        $this->statusHistories()->create([
            'status' => $status,
            'provider_status' => $providerStatus,
            'note' => $note,
            'created_at' => now(),
        ]);
    }
}
