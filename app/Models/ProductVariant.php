<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'price', 'stock_quantity','height'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
public function pots()
{
    return $this->belongsToMany(Pot::class, 'pot_product_variant', 'product_variant_id', 'pot_id');
}

    public function returnRequests()
    {
        return $this->hasMany(ReturnRequest::class, 'product_variant_id');
    }
}
