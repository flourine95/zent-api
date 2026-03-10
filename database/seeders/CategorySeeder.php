<?php

namespace Database\Seeders;

use App\Infrastructure\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::factory(10)->create();
    }
}
