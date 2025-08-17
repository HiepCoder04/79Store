<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pot extends Model
{
     protected $fillable = ['name','price','quantity'];

    public function productVariants()
    {
        return $this->belongsToMany(ProductVariant::class);
    }
    public function returnRequests()
    {
        return $this->hasMany(ReturnRequest::class, 'pot_id');
    }
}
