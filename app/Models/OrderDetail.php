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
        'variant_name',
        'price',
        'quantity',
        'total_price',
    ];

    /**
     * Ép kiểu dữ liệu để đảm bảo xử lý số chính xác
     */
    protected $casts = [
        'price' => 'float',
        'quantity' => 'integer',
        'total_price' => 'float',
    ];

    /**
     * Mối quan hệ: OrderDetail thuộc về 1 đơn hàng
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Mối quan hệ: OrderDetail thuộc về 1 sản phẩm
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Mối quan hệ: OrderDetail thuộc về 1 biến thể sản phẩm (nếu có)
     */
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
