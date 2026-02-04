<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id', 'code', 'status', 'payment_status', 'total_amount', 'note'];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
