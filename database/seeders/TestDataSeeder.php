<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create test user
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        // Create categories
        $electronics = Category::create([
            'name' => 'Electronics',
            'slug' => 'electronics',
            'description' => 'Electronic devices and gadgets',
            'is_visible' => true,
        ]);

        $laptops = Category::create([
            'parent_id' => $electronics->id,
            'name' => 'Laptops',
            'slug' => 'laptops',
            'description' => 'Laptop computers',
            'is_visible' => true,
        ]);

        $phones = Category::create([
            'parent_id' => $electronics->id,
            'name' => 'Smartphones',
            'slug' => 'smartphones',
            'description' => 'Mobile phones',
            'is_visible' => true,
        ]);

        $fashion = Category::create([
            'name' => 'Fashion',
            'slug' => 'fashion',
            'description' => 'Clothing and accessories',
            'is_visible' => true,
        ]);

        // Create products
        $laptop = Product::create([
            'category_id' => $laptops->id,
            'name' => 'MacBook Pro 16"',
            'slug' => 'macbook-pro-16',
            'description' => 'Powerful laptop for professionals',
            'is_active' => true,
        ]);

        $phone = Product::create([
            'category_id' => $phones->id,
            'name' => 'iPhone 15 Pro',
            'slug' => 'iphone-15-pro',
            'description' => 'Latest iPhone with advanced features',
            'is_active' => true,
        ]);

        $tshirt = Product::create([
            'category_id' => $fashion->id,
            'name' => 'Cotton T-Shirt',
            'slug' => 'cotton-tshirt',
            'description' => 'Comfortable cotton t-shirt',
            'is_active' => true,
        ]);

        // Create product variants
        $laptopVariant1 = ProductVariant::create([
            'product_id' => $laptop->id,
            'sku' => 'MBP16-M3-16GB-512GB',
            'price' => 2499.00,
            'original_price' => 2799.00,
            'options' => [
                ['attribute' => 'Chip', 'value' => 'M3 Pro'],
                ['attribute' => 'RAM', 'value' => '16GB'],
                ['attribute' => 'Storage', 'value' => '512GB'],
            ],
        ]);

        $laptopVariant2 = ProductVariant::create([
            'product_id' => $laptop->id,
            'sku' => 'MBP16-M3-32GB-1TB',
            'price' => 3299.00,
            'original_price' => 3599.00,
            'options' => [
                ['attribute' => 'Chip', 'value' => 'M3 Max'],
                ['attribute' => 'RAM', 'value' => '32GB'],
                ['attribute' => 'Storage', 'value' => '1TB'],
            ],
        ]);

        $phoneVariant1 = ProductVariant::create([
            'product_id' => $phone->id,
            'sku' => 'IP15P-256GB-BLACK',
            'price' => 999.00,
            'original_price' => 1099.00,
            'options' => [
                ['attribute' => 'Storage', 'value' => '256GB'],
                ['attribute' => 'Color', 'value' => 'Black Titanium'],
            ],
        ]);

        $phoneVariant2 = ProductVariant::create([
            'product_id' => $phone->id,
            'sku' => 'IP15P-512GB-BLUE',
            'price' => 1199.00,
            'options' => [
                ['attribute' => 'Storage', 'value' => '512GB'],
                ['attribute' => 'Color', 'value' => 'Blue Titanium'],
            ],
        ]);

        $tshirtVariant = ProductVariant::create([
            'product_id' => $tshirt->id,
            'sku' => 'TSHIRT-M-BLACK',
            'price' => 29.99,
            'original_price' => 39.99,
            'options' => [
                ['attribute' => 'Size', 'value' => 'M'],
                ['attribute' => 'Color', 'value' => 'Black'],
            ],
        ]);

        // Create addresses for user
        Address::create([
            'user_id' => $user->id,
            'label' => 'Home',
            'recipient_name' => 'Test User',
            'phone' => '0123456789',
            'address_line_1' => '123 Nguyen Hue Street',
            'address_line_2' => 'Apartment 5B',
            'city' => 'Ho Chi Minh',
            'state' => 'HCM',
            'postal_code' => '700000',
            'country' => 'VN',
            'is_default' => true,
        ]);

        Address::create([
            'user_id' => $user->id,
            'label' => 'Office',
            'recipient_name' => 'Test User',
            'phone' => '0987654321',
            'address_line_1' => '456 Le Loi Boulevard',
            'city' => 'Ho Chi Minh',
            'state' => 'HCM',
            'postal_code' => '700000',
            'country' => 'VN',
            'is_default' => false,
        ]);

        // Create cart with items
        $cart = Cart::create(['user_id' => $user->id]);

        CartItem::create([
            'cart_id' => $cart->id,
            'product_variant_id' => $laptopVariant1->id,
            'quantity' => 1,
        ]);

        CartItem::create([
            'cart_id' => $cart->id,
            'product_variant_id' => $phoneVariant1->id,
            'quantity' => 2,
        ]);

        // Create wishlist
        Wishlist::create([
            'user_id' => $user->id,
            'product_id' => $laptop->id,
        ]);

        Wishlist::create([
            'user_id' => $user->id,
            'product_id' => $tshirt->id,
        ]);

        $this->command->info('✅ Test data created successfully!');
        $this->command->info('📧 Email: test@example.com');
        $this->command->info('🔑 Password: password');
    }
}
