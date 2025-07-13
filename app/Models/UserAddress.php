<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'address',
        'is_default',
    ];

    public function getFullAddressAttribute()
    {
        return $this->address;
    }

    // Địa chỉ thuộc về user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
