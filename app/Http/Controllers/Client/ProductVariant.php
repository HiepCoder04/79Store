<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductVariant extends Controller
{
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
