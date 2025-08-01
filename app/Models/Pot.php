<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pot extends Model
{
     protected $fillable = ['name'];

    public function productVariants()
    {
        return $this->belongsToMany(ProductVariant::class);
    }
}
