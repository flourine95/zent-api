<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [];
        $count = 100000; // Thử với 100k dòng

        $this->command->info("Đang chuẩn bị dữ liệu...");

        for ($i = 0; $i < $count; $i++) {
            $data[] = [
                'name' => Str::random(10),
                'email' => Str::random(10) . $i . '@example.com', // Đảm bảo email là duy nhất
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password mã hóa sẵn cho nhanh
            ];

            // Chèn theo từng đợt 5000 dòng để không bị tràn bộ nhớ (Memory Limit)
            if ($i % 5000 == 0) {
                DB::table('users')->insert($data);
                $data = [];
            }
        }
        DB::table('users')->insert($data); // Chèn nốt số còn lại
    }
}
