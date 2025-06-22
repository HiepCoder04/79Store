<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
class OrderController extends Controller
{
    //
    public function index()
    {
       $orders = Order::with('orderDetails.productVariant.product')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('client.users.order', compact('orders'));
    }
}
