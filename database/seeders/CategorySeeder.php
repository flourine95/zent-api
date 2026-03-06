<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * NGUYÊN TẮC 2: Query categories để recycle cho ProductSeeder
     */
    public function run(): void
    {
        Category::factory(10)->create();
    }
}
