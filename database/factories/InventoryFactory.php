<?php

namespace Database\Factories;

use App\Infrastructure\Models\Inventory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Inventory>
 */
class InventoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'quantity' => fake()->numberBetween(10, 100),
            'shelf_location' => 'Kệ '.fake()->randomLetter().'-'.fake()->numberBetween(1, 50),
        ];
    }
}
