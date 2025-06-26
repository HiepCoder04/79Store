<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;
use App\Models\ProductVariant;

class CartController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $cart = Cart::with('items.productVariant.product.galleries')
            ->where('user_id', $user->id)
            ->first();

        $items = $cart ? $cart->items : collect();
        $total = $items->sum(fn($item) => $item->productVariant->price * $item->quantity);

        return view('client.users.cart', compact('items', 'total'));
    }


    public function add(Request $request)
{
    $request->validate([
        'product_id' => 'required|exists:products,id',
        'pot'        => 'required|string',
        'quantity'   => 'required|integer|min:1'
    ]);

    $variant = ProductVariant::where('product_id', $request->product_id)
        ->where('pot', $request->pot)
        ->first();

    if (!$variant) {
        return back()->with('error', 'Không tìm thấy biến thể phù hợp.');
    }

    if ($variant->stock_quantity < $request->quantity) {
        return back()->with('error', 'Số lượng vượt quá tồn kho.');
    }

    $user = Auth::user();
    $cart = Cart::firstOrCreate(['user_id' => $user->id]);

    $item = CartItem::where('cart_id', $cart->id)
        ->where('product_variant_id', $variant->id)
        ->first();

    if ($item) {
        $item->quantity += $request->quantity;
        $item->save();
    } else {
        CartItem::create([
            'cart_id' => $cart->id,
            'product_variant_id' => $variant->id,
            'quantity' => $request->quantity
        ]);
    }

    return redirect()->back()->with('success', 'Đã thêm vào giỏ hàng!');
}



    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $item = CartItem::findOrFail($id);

        if ($item->cart->user_id !== auth()->id()) {
            abort(403);
        }

        if ($item->productVariant->stock_quantity < $request->quantity) {
            return back()->with('error', 'Số lượng vượt quá tồn kho.');
        }

        $item->quantity = $request->quantity;
        $item->save();

        return back()->with('success', 'Đã cập nhật số lượng.');
    }

    public function remove($id)
    {
        $item = CartItem::findOrFail($id);

        if ($item->cart->user_id !== auth()->id()) {
            abort(403);
        }

        $item->delete();

        return back()->with('success', 'Đã xoá khỏi giỏ hàng.');
    }
}
