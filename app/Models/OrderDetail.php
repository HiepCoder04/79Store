<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;
    protected $table = 'order_details';

    protected $fillable = [
        'order_id',
        'product_id',
        'product_variant_id',
        'product_name',
        'price',
        'variant_name',
        'product_height',
        'product_pot',
        'product_price',
        'pot_price', // ✅ Thêm pot_price vào fillable
        'quantity',
        'total_price',
        'pot_id',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    // Alias để match với controller: with(['product','variant'])
    public function variant()
    {
        return $this->productVariant(); // bạn đã có productVariant()
    }

    // Nếu đã thêm cột pot_id ở order_details (bước 1.2)
    public function pot()
    {
        return $this->belongsTo(Pot::class, 'pot_id');
    }

    //duongthemqh
    public function returnRequests()
    {
        return $this->hasMany(ReturnRequest::class);
    }

    public function qtyReturned(): int
    {
        return (int) $this->returnRequests()
            ->whereIn('status', ['approved', 'refunded', 'exchanged'])
            ->sum('quantity');
    }
    public function review()
    {
        return $this->hasOne(\App\Models\Review::class, 'order_detail_id');
    }

        // ✅ THÊM METHOD MỚI: Tính riêng số lượng cây đã trả
    public function plantQtyReturned(): int
    {
        return (int) $this->returnRequests()
            ->whereIn('status', ['approved', 'refunded', 'exchanged'])
            ->sum('plant_quantity');
    }

    // ✅ THÊM METHOD MỚI: Tính riêng số lượng chậu đã trả
    public function potQtyReturned(): int
    {
        return (int) $this->returnRequests()
            ->whereIn('status', ['approved', 'refunded', 'exchanged'])
            ->sum('pot_quantity');
    }

    // ✅ THÊM METHOD MỚI: Tính số lượng còn có thể trả cho cây
    public function remainingPlantQty(): int
    {
        return max(0, $this->quantity - $this->plantQtyReturned());
    }

    // ✅ THÊM METHOD MỚI: Tính số lượng còn có thể trả cho chậu
    public function remainingPotQty(): int
    {
        // Chỉ có thể trả chậu nếu orderDetail có chậu (pot_price > 0)
        if (($this->pot_price ?? 0) <= 0) {
            return 0;
        }
        return max(0, $this->quantity - $this->potQtyReturned());
    }
}




