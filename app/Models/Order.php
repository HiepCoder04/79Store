<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'orders';

    protected $fillable = [
        'user_id',
        'name',
        'address_id',
        'phone',
        'note',
        'payment_method',
        'shipping_method',
        'total_before_discount',
        'discount_amount',
        'total_after_discount',
        'status',
        'sale_channel',
    ];

    /**
     * Mối quan hệ: Order thuộc về User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mối quan hệ: Order có địa chỉ giao hàng
     */
    public function address()
    {
        return $this->belongsTo(UserAddress::class, 'address_id');
    }

    /**
     * Mối quan hệ: Order có nhiều chi tiết đơn hàng
     */
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    /**
     * Mối quan hệ: Order có 1 đơn hoàn hàng
     */
    public function returnOrder()
    {
        return $this->hasOne(OrderReturn::class);
    }

    /**
     * Mối quan hệ: Order có 1 giao dịch
     */
    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }
}
