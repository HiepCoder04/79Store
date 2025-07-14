<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\ProductVariant;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 0; $i < 20; $i++) {
            $status = ['pending', 'confirmed', 'completed', 'canceled'][rand(0, 3)];
            $total = 0;

            $order = Order::create([
                'user_id' => rand(1, 5),
                'address_id' => rand(1, 5),
                'order_status' => $status,
                'payment_method' => 'cod',
                'shipping_method' => 'Giao hàng tiêu chuẩn',
                'total_price' => 0,
                'note' => 'Đơn hàng test #' . $i,
                'created_at' => now()->subDays(rand(0, 6))->startOfDay()->addHours(rand(0, 23)),
            ]);

            for ($j = 0; $j < rand(1, 3); $j++) {
                $price = rand(100, 500) * 1000;
                $quantity = rand(1, 3);
                $totalPrice = $price * $quantity;

                $productVariant = ProductVariant::inRandomOrder()->first();
                if (!$productVariant) continue;

                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $productVariant->product_id,
                    'product_variant_id' => $productVariant->id,
                    'product_name' => 'SP ' . Str::random(5),
                    'variant_name' => $productVariant->variant_name,
                    'price' => $price,
                    'quantity' => $quantity,
                    'total_price' => $totalPrice,
                    'user_id' => $order->user_id,
                ]);

                $total += $totalPrice;
            }

            $order->update(['total_price' => $total]);
        }
    }
}
