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

}