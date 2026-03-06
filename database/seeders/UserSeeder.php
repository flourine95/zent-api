<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin Long',
                'password' => bcrypt('password'),
            ]
        );

        $demoUser = User::firstOrCreate(
            ['email' => 'demo@example.com'],
            [
                'name' => 'Demo User',
                'password' => Hash::make('password'),
            ]
        );

        if ($demoUser->addresses()->count() === 0) {
            Address::factory(2)
                ->for($demoUser, 'user')
                ->sequence(
                    [
                        'label' => 'Nhà riêng',
                        'recipient_name' => 'Demo User',
                        'phone' => '0123456789',
                        'address_line_1' => '123 Nguyễn Huệ',
                        'address_line_2' => 'Căn hộ 5B',
                        'city' => 'Hồ Chí Minh',
                        'state' => 'HCM',
                        'postal_code' => '700000',
                        'country' => 'VN',
                        'is_default' => true,
                    ],
                    [
                        'label' => 'Văn phòng',
                        'recipient_name' => 'Demo User',
                        'phone' => '0987654321',
                        'address_line_1' => '456 Lê Lợi',
                        'city' => 'Hồ Chí Minh',
                        'state' => 'HCM',
                        'postal_code' => '700000',
                        'country' => 'VN',
                        'is_default' => false,
                    ]
                )
                ->create();
        }

        User::firstOrCreate(
            ['email' => 'customer@example.com'],
            [
                'name' => 'Nguyễn Văn A',
                'password' => Hash::make('password'),
            ]
        );
    }
}
