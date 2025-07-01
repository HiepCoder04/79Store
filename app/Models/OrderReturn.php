<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderReturn extends Model
{
    use HasFactory;

    protected $table = 'order_returns';

    protected $fillable = [
        'order_id',
        'return_status',
        'reason',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
