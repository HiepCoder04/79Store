<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function listProducts(){
        return view('admin.products.list-product');
    }
    public function thongke(){
        return view('admin.thongke.thongke');
    }
}
