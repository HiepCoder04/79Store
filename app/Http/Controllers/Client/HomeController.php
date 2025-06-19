<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
{
    $banners = Banner::where('is_active', 1)->latest()->get();
    return view('client.home', compact('banners'));
}
}
