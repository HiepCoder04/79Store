<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
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


    public function add(Request $request)
    {
        $variantId = $request->input('product_variant_id');
        $quantity = $request->input('quantity', 1);

        $cart = Cart::firstOrCreate(['user_id' => auth()->id()]);

        $item = CartItem::where('cart_id', $cart->id)
            ->where('product_variant_id', $variantId)
            ->first();

        if ($item) {
            $item->increment('quantity', $quantity);
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_variant_id' => $variantId,
                'quantity' => $quantity
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Đã thêm vào giỏ hàng!');
    }

    public function remove($id)
    {
        $item = CartItem::findOrFail($id);

        if ($item->cart->user_id == auth()->id()) {
            $item->delete();
        }

        return back()->with('success', 'Đã xoá khỏi giỏ hàng.');
    }
}
