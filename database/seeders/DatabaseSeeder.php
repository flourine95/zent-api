<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            SettingsSeeder::class,
            UserSeeder::class,
            WarehouseSeeder::class,
            BannerSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            CartSeeder::class,
            WishlistSeeder::class,
            OrderSeeder::class,
        ]);
    }
}
