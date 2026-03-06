<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Database\Seeder;

class WishlistSeeder extends Seeder
{
    public function run(): void
    {
        $demoUser = User::where('email', 'demo@example.com')->first();

        if (! $demoUser) {
            return;
        }

        $products = Product::inRandomOrder()->limit(2)->get();

        foreach ($products as $product) {
            Wishlist::create([
                'user_id' => $demoUser->id,
                'product_id' => $product->id,
            ]);
        }
    }
}
