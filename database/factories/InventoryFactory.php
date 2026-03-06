<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Inventory>
 */
class InventoryFactory extends Factory
{
    /**
     * NGUYÊN TẮC 1: GIỮ FACTORY SẠCH
     * - KHÔNG gọi Warehouse::factory() hoặc ProductVariant::factory()
     * - Cả 2 FK sẽ được gán qua ->recycle() từ Seeder
     */
    public function definition(): array
    {
        return [
            // KHÔNG có warehouse_id và product_variant_id
            // Sẽ được gán qua recycle() từ Seeder
            'quantity' => fake()->numberBetween(10, 100),
            'shelf_location' => 'Kệ '.fake()->randomLetter().'-'.fake()->numberBetween(1, 50),
        ];
    }
}
