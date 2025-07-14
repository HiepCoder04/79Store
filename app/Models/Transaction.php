<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';

    protected $fillable = [
        'order_id',         // vẫn giữ lại ID để tra cứu hoặc lưu log nếu cần
        'amount',
        'payment_method',
        'status',
    ];

    // Đã xoá mối quan hệ belongsTo(Order::class) vì model Order không còn tồn tại
}
