<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Voucher;

class VoucherSeeder extends Seeder
{
    public function run(): void
    {
        $voucher = Voucher::firstOrNew(['code' => 'GIAM10']);

        $voucher->fill([
            'description' => 'Giảm 10% cho đơn hàng từ 100.000đ',
            'event_type' => 'discount 10%',
            'start_date' => now()->subDay(),
            'end_date' => now()->addDays(5),
            'discount_percent' => 10.00,
            'max_discount' => 50000,
            'min_order_amount' => 100000,
            'is_active' => true,
        ]);

        $voucher->save();
    }
}
