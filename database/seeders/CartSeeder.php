<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Database\Seeder;

class CartSeeder extends Seeder
{
    public function run(): void
    {
        $demoUser = User::where('email', 'demo@example.com')->first();

        if (! $demoUser) {
            return;
        }

        $variants = ProductVariant::inRandomOrder()->limit(2)->get();

        if ($variants->count() < 2) {
            return;
        }

        $cart = Cart::create(['user_id' => $demoUser->id]);

        CartItem::create([
            'cart_id' => $cart->id,
            'product_variant_id' => $variants[0]->id,
            'quantity' => 1,
        ]);

        CartItem::create([
            'cart_id' => $cart->id,
            'product_variant_id' => $variants[1]->id,
            'quantity' => 2,
        ]);
    }
}
