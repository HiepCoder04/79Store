<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserAddress;

class UserAddressSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            UserAddress::create([
                'user_id' => $i, // Giả sử đã có user_id từ 1–10
                'name' => 'Người nhận ' . $i,
                'phone' => '09000000' . $i,
                'address' => 'Địa chỉ số ' . $i,
            ]);
        }
    }
}
