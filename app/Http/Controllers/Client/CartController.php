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
    $voucher = null;
    $cart = Cart::with('items.productVariant.product')
        ->where('user_id', $user->id)
        ->first();

    $items = $cart ? $cart->items : collect();
    $cartTotal = $items->sum(fn($item) => $item->productVariant->price * $item->quantity);
    $finalTotal = $cartTotal;
    $discount = 0;

    $voucherId = session('applied_voucher');

    if ($voucherId) {
        $voucher = \App\Models\Voucher::find($voucherId);

        if ($voucher &&
            $voucher->is_active &&
            now()->between($voucher->start_date, $voucher->end_date) &&
            $cartTotal >= $voucher->min_order_amount) {

            // Tính giảm giá
            $discount = $cartTotal * ($voucher->discount_percent / 100);
            if ($voucher->max_discount && $discount > $voucher->max_discount) {
                $discount = $voucher->max_discount;
            }

            $finalTotal = $cartTotal - $discount;
        }
    }

    return view('client.users.cart', compact('items', 'cartTotal', 'finalTotal', 'discount', 'voucher'));
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
