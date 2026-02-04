<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $fillable = ['warehouse_id', 'product_variant_id', 'quantity', 'shelf_location'];
}
