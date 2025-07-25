<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'title',
        'description',
        'event_type',
        'start_date',
        'end_date',
        'discount_percent',
        'max_discount',
        'min_order_amount',
        'is_active',
    ];
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_vouchers')
            ->withPivot(['is_used', 'used_at'])
            ->withTimestamps();
    }
}
