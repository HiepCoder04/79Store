<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
class CartController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $cart = Cart::with('items.productVariant.product')
            ->where('user_id', $user->id)
            ->first();

        $items = $cart ? $cart->items : collect();
        $total = $items->sum(fn($item) => $item->productVariant->price * $item->quantity);

        return view('client.users.cart', compact('items', 'total'));
    }
}
