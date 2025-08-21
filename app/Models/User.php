<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\UserAddress;
use App\Traits\Filterable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Filterable;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'date_of_birth',
        'email_verified_at',
        'remember_token',
        'is_ban',
        'ban_reason',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'date_of_birth' => 'date',
        'is_ban' => 'boolean',
    ];

    // Quan hệ
    public function addresses()
    {
        return $this->hasMany(UserAddress::class);
    }
    public function defaultAddress()
    {
        return $this->hasOne(UserAddress::class)->where('is_default', 1);
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function vouchers()
    {
        return $this->belongsToMany(Voucher::class, 'user_vouchers')
            ->withPivot(['is_used', 'used_at'])
            ->withTimestamps();
    }
    public function returnRequests()
    {
        return $this->hasMany(ReturnRequest::class);
    }
    public function cancellations()
    {
        return $this->hasMany(Cancellation::class);
    }
}
