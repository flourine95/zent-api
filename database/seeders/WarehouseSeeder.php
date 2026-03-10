<?php

namespace Database\Seeders;

use App\Infrastructure\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {
        // Main warehouse
        Warehouse::create([
            'name' => 'Kho Tổng TP.HCM',
            'code' => 'WH-HCM-MAIN',
            'address' => 'Số 1 Võ Văn Ngân, Thủ Đức',
            'is_active' => true,
        ]);

        // Additional warehouses
        Warehouse::factory(4)->create();
    }
}
