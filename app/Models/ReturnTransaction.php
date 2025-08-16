<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnTransaction extends Model
{
    protected $fillable = [
        'return_request_id','type','amount','note',
        'bank_name','bank_account_name','bank_account_number','proof_images',
        'processed_at',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
        'proof_images' => 'array',
    ];

    public function request()
    {
        return $this->belongsTo(ReturnRequest::class, 'return_request_id');
    }
}
