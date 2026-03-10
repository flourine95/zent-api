<?php

namespace Database\Seeders;

use App\Infrastructure\Models\Order;
use App\Infrastructure\Models\OrderItem;
use App\Infrastructure\Models\ProductVariant;
use App\Infrastructure\Models\User;
use App\Infrastructure\Models\Warehouse;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $customer = User::where('email', 'customer@example.com')->first();
        $warehouse = Warehouse::first();

        if (! $customer || ! $warehouse) {
            return;
        }

        $variants = ProductVariant::with('product')->inRandomOrder()->limit(2)->get();

        if ($variants->count() < 2) {
            return;
        }

        $order1 = Order::create([
            'user_id' => $customer->id,
            'code' => 'ORD-'.strtoupper(fake()->bothify('???###')),
            'status' => 'completed',
            'payment_status' => 'paid',
            'total_amount' => $variants[0]->price,
            'note' => 'Giao hàng giờ hành chính',
        ]);

        OrderItem::create([
            'order_id' => $order1->id,
            'product_variant_id' => $variants[0]->id,
            'warehouse_id' => $warehouse->id,
            'quantity' => 1,
            'price' => $variants[0]->price,
            'product_snapshot' => [
                'name' => $variants[0]->product->name,
                'sku' => $variants[0]->sku,
                'options' => $variants[0]->options,
            ],
        ]);

        $order2 = Order::create([
            'user_id' => $customer->id,
            'code' => 'ORD-'.strtoupper(fake()->bothify('???###')),
            'status' => 'processing',
            'payment_status' => 'paid',
            'total_amount' => $variants[1]->price * 2,
            'note' => null,
        ]);

        OrderItem::create([
            'order_id' => $order2->id,
            'product_variant_id' => $variants[1]->id,
            'warehouse_id' => $warehouse->id,
            'quantity' => 2,
            'price' => $variants[1]->price,
            'product_snapshot' => [
                'name' => $variants[1]->product->name,
                'sku' => $variants[1]->sku,
                'options' => $variants[1]->options,
            ],
        ]);
    }
}
