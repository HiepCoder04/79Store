<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;

class CheckoutController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Lấy giỏ hàng của user kèm sản phẩm & biến thể
        $cart = Cart::with('items.productVariant.product')
            ->where('user_id', $user->id)
            ->first();

        // Lấy danh sách địa chỉ giao hàng của user
        $addresses = $user->addresses;

        // Truyền ra view
        return view('client.users.Checkout', compact('cart', 'addresses'));
    }
    
}
