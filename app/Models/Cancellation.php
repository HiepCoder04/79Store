<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cancellation extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id',
        'user_id',
        'reason',
        'status',
        'admin_note',
    ];

    /**
     * Người yêu cầu hủy (User)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Đơn hàng bị hủy (Order)
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
