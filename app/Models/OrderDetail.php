<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    protected $table = 'order_details';

    protected $fillable = [
        'order_id',             // Vẫn giữ để lưu ID đơn hàng nếu cần
        'product_id',
        'product_variant_id',
        'product_name',
        'variant_name',
        'price',
        'quantity',
        'total_price',
    ];

    /**
     * Ép kiểu dữ liệu để xử lý chính xác các trường số
     */
    protected $casts = [
        'price' => 'float',
        'quantity' => 'integer',
        'total_price' => 'float',
    ];

    /**
     * Mối quan hệ: OrderDetail thuộc về một sản phẩm
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Mối quan hệ: OrderDetail thuộc về một biến thể sản phẩm (nếu có)
     */
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    // Không còn mối quan hệ với model Order vì bảng orders đã bị xoá
}
