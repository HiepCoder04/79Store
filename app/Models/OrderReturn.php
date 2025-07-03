<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderReturn extends Model
{
    use HasFactory;

    protected $table = 'order_returns';

    protected $fillable = [
        'order_id',         // vẫn giữ để lưu ID đơn hàng nếu có
        'return_status',
        'reason',
    ];

    // Đã xoá quan hệ belongsTo với model Order vì model Order không còn tồn tại
}
