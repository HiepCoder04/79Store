<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnRequest extends Model
{
    protected $fillable = [
        'order_id','order_detail_id','user_id','product_id','product_variant_id','pot_id',
        'quantity','reason','images','status',
        'bank_name','bank_account_name','bank_account_number',
        'tracking_code','admin_note','resolved_at',
    ];

    protected $casts = [
        'images' => 'array',
        'resolved_at' => 'datetime',
    ];

    public function order()        { return $this->belongsTo(Order::class); }
    public function orderDetail()  { return $this->belongsTo(OrderDetail::class); }
    public function user()         { return $this->belongsTo(User::class); }
    public function product()      { return $this->belongsTo(Product::class); }
    public function variant()      { return $this->belongsTo(ProductVariant::class, 'product_variant_id'); }
    public function pot()          { return $this->belongsTo(Pot::class, 'pot_id'); }
    public function transactions() { return $this->hasMany(ReturnTransaction::class, 'return_request_id'); }
}
