<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;
use App\Models\ProductVariant;
use App\Models\Pot;

class CartController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $voucher = null;
        $errorMessage = null;

        $cart = Cart::with('items.productVariant.product.galleries', 'items.pot')
            ->where('user_id', $user->id)
            ->first();

        $items = $cart ? $cart->items : collect();
        $cartTotal = $items->sum(fn($item) => $item->productVariant->price * $item->quantity);
        $finalTotal = $cartTotal;
        $discount = 0;

        $voucherId = session('applied_voucher');

        if ($voucherId) {
            $voucher = \App\Models\Voucher::find($voucherId);

            if (
                $voucher &&
                $voucher->is_active &&
                now()->between($voucher->start_date, $voucher->end_date)
            ) {
                if ($cartTotal >= $voucher->min_order_amount) {
                    $discount = $cartTotal * ($voucher->discount_percent / 100);
                    if ($voucher->max_discount && $discount > $voucher->max_discount) {
                        $discount = $voucher->max_discount;
                    }

                    $finalTotal = $cartTotal - $discount;
                } else {
                    $errorMessage = 'Đơn hàng chưa đạt ' . number_format($voucher->min_order_amount, 0, ',', '.') . 'đ để áp dụng mã ' . $voucher->code;
                    session()->forget('applied_voucher');
                    $voucher = null;
                }
            }
        }

        return view('client.users.cart', compact(
            'items',
            'cartTotal',
            'finalTotal',
            'discount',
            'voucher',
            'errorMessage'
        ));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'pot'        => 'required|numeric|exists:pots,id',
            'height'     => 'required|string', // ✅ Bắt buộc có chiều cao
            'quantity'   => 'required|integer|min:1'
        ]);

        $variant = ProductVariant::where('product_id', $request->product_id)
            ->where('height', $request->height) // ✅ THÊM ĐIỀU KIỆN CHIỀU CAO
            ->first();

        if (!$variant) {
            return back()->with('error', 'Không tìm thấy biến thể phù hợp.');
        }

        if ($variant->stock_quantity < $request->quantity) {
            return back()->with('error', 'Số lượng vượt quá tồn kho.');
        }
        $potId = $request->pot;
        $pot = Pot::find($potId);

        if (!$pot || $pot->quantity <= 0) {
            return back()->with('error', 'Chậu đã hết hàng hoặc không tồn tại.');
        }

        // Kiểm tra chậu này có liên kết với biến thể cây không
        $isValidPot = $variant->pots()->where('pots.id', $potId)->exists();

        if (!$isValidPot) {
            return back()->with('error', 'Chậu không phù hợp với biến thể cây.');
        }

        $user = Auth::user();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        $item = CartItem::where('cart_id', $cart->id)
            ->where('product_variant_id', $variant->id)
            ->where('pot_id', $potId)
            ->first();

        if ($item) {
            $item->quantity += $request->quantity;
            $item->save();
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_variant_id' => $variant->id,
                'pot_id'             => $potId,
                'quantity' => $request->quantity
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Đã thêm vào giỏ hàng!');
    }

    public function addAjax(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'height'     => 'required|string',
            'pot'        => 'required|numeric|exists:pots,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $variant = ProductVariant::where('product_id', $request->product_id)
            ->where('height', $request->height)
            ->first();

        if (!$variant) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy biến thể phù hợp.'], 404);
        }

        if ($variant->stock_quantity < $request->quantity) {
            return response()->json(['success' => false, 'message' => 'Số lượng vượt quá tồn kho cây.'], 400);
        }

        $potId = $request->pot;
        $pot = Pot::find($potId);

        if (!$pot || $pot->quantity <= 0) {
            return response()->json(['success' => false, 'message' => 'Chậu đã hết hàng hoặc không tồn tại.'], 400);
        }

        $isValidPot = $variant->pots()->where('pots.id', $potId)->exists();

        if (!$isValidPot) {
            return response()->json(['success' => false, 'message' => 'Chậu không phù hợp với biến thể cây.'], 400);
        }

        $user = Auth::user();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        $item = CartItem::where('cart_id', $cart->id)
            ->where('product_variant_id', $variant->id)
            ->where('pot_id', $potId)
            ->first();

        if ($item) {
            $item->quantity += $request->quantity;
            $item->save();
        } else {
            CartItem::create([
                'cart_id'            => $cart->id,
                'product_variant_id' => $variant->id,
                'pot_id'             => $potId,
                'quantity'           => $request->quantity
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Đã thêm vào giỏ hàng!']);
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
            return response()->json(['error' => 'Số lượng vượt quá tồn kho'], 400);
        }

        $item->quantity = $request->quantity;
        $item->save();

        $cartItems = $item->cart->items;
        $cartTotal = $cartItems->sum(fn($i) => $i->productVariant->price * $i->quantity);
        $finalTotal = $cartTotal;

        return response()->json([
            'itemSubtotalFormatted' => number_format($item->productVariant->price * $item->quantity, 0, ',', '.') . 'đ',
            'cartTotalFormatted' => number_format($cartTotal, 0, ',', '.') . 'đ',
            'finalTotalFormatted' => number_format($finalTotal, 0, ',', '.') . 'đ',
        ]);
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
