<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\UserAddress;
use App\Models\Voucher;
use App\Models\UserVoucher;

class CheckoutController extends Controller
{
    public function index(Request $request)
{
    $user = Auth::user();
    $selectedIds = collect(explode(',', $request->get('selected', '')))
        ->filter(fn($id) => is_numeric($id))
        ->map(fn($id) => (int) $id)
        ->unique()
        ->values();

    $cart = Cart::with(['items.productVariant.product.galleries'])->where('user_id', $user->id)->first();

    if (!$cart || $cart->items->isEmpty()) {
        return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống.');
    }

    // Nếu có danh sách được chọn thì lọc lại item
    if ($selectedIds->isNotEmpty()) {
        $cart->setRelation('items', $cart->items->whereIn('id', $selectedIds));
    }

    if ($cart->items->isEmpty()) {
        return redirect()->route('cart.index')->with('error', 'Vui lòng chọn ít nhất một sản phẩm để thanh toán.');
    }

    $addresses = UserAddress::where('user_id', $user->id)->get();

    // Voucher (áp dụng nếu có trong session)
    $voucher = null;
    $discount = 0;
    $cartTotal = $cart->items->sum(fn($item) => $item->productVariant->price * $item->quantity);
    $finalTotal = $cartTotal;

    $voucherId = session('applied_voucher');
    if ($voucherId) {
        $voucher = Voucher::find($voucherId);
        if ($voucher && $voucher->is_active && now()->between($voucher->start_date, $voucher->end_date)) {
            if ($cartTotal >= $voucher->min_order_amount) {
                $discount = $cartTotal * ($voucher->discount_percent / 100);
                if ($voucher->max_discount && $discount > $voucher->max_discount) {
                    $discount = $voucher->max_discount;
                }
                $finalTotal = $cartTotal - $discount;
            }
        }
    }

    return view('client.users.Checkout', compact('cart', 'addresses', 'user', 'voucher', 'discount', 'cartTotal', 'finalTotal'));
}

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'payment_method' => 'required|string',
            'shipping_method' => 'nullable|string',
            'note' => 'nullable|string',
            'new_address' => 'nullable|string|max:255',
            'address_id' => 'nullable|exists:user_addresses,id',
        ]);

        $cart = Cart::with('items.productVariant.product')->where('user_id', $user->id)->first();
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->back()->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        DB::beginTransaction();

        try {
            if ($request->filled('new_address')) {
                $existingAddress = UserAddress::where('user_id', $user->id)->where('address', $request->new_address)->first();
                if ($existingAddress) {
                    if ($request->has('set_default')) {
                        UserAddress::where('user_id', $user->id)->where('id', '!=', $existingAddress->id)->update(['is_default' => 0]);
                        $existingAddress->is_default = 1;
                        $existingAddress->save();
                    }
                    $addressId = $existingAddress->id;
                } else {
                    if ($request->has('set_default')) {
                        UserAddress::where('user_id', $user->id)->update(['is_default' => 0]);
                    }
                    $newAddress = UserAddress::create([
                        'user_id' => $user->id,
                        'address' => $request->new_address,
                        'is_default' => $request->has('set_default') ? 1 : 0,
                    ]);
                    $addressId = $newAddress->id;
                }
            } elseif ($request->filled('address_id')) {
                $addressId = $request->address_id;
            } else {
                return back()->with('error', 'Vui lòng chọn hoặc nhập địa chỉ giao hàng.');
            }

            $totalBefore = $cart->items->sum(fn($item) => $item->productVariant->price * $item->quantity);

            $discount = 0;
            $voucherId = session('applied_voucher');
            if ($voucherId) {
                $voucher = Voucher::find($voucherId);
                if ($voucher && $voucher->is_active && now()->between($voucher->start_date, $voucher->end_date)) {
                    if ($totalBefore >= $voucher->min_order_amount) {
                        $discount = $totalBefore * ($voucher->discount_percent / 100);
                        if ($voucher->max_discount && $discount > $voucher->max_discount) {
                            $discount = $voucher->max_discount;
                        }

                        // Ghi nhận là đã dùng mã
                        UserVoucher::updateOrCreate([
                            'user_id' => $user->id,
                            'voucher_id' => $voucher->id
                        ], [
                            'is_used' => true,
                            'used_at' => now()
                        ]);
                    }
                }
            }

            $finalTotal = $totalBefore - $discount;

            $orderStatus = 'pending';
            $paymentStatus = 'unpaid';
            if ($request->payment_method == 'vnpay' && isset($request->payment_status) && $request->payment_status == 'paid') {
                $paymentStatus = 'paid';
                $orderStatus = 'confirmed';
            }

            $order = Order::create([
                'user_id' => $user->id,
                'address_id' => $addressId,
                'name' => $request->name,
                'phone' => $request->phone,
                'note' => $request->note,
                'payment_method' => $request->payment_method,
                'payment_status' => $paymentStatus,
                'shipping_method' => $request->shipping_method ?? 'Giao hàng tiêu chuẩn',
                'total_before_discount' => $totalBefore,
                'discount_amount' => $discount,
                'total_after_discount' => $finalTotal,
                'status' => $orderStatus,
                'sale_channel' => 'website',
            ]);

            foreach ($cart->items as $item) {
                $variant = $item->productVariant;
    $product = $variant->product;


                OrderDetail::create([
                   'order_id' => $order->id,
        'product_id' => $product->id,
        'product_variant_id' => $variant->id,
        'product_name' => $product->name,
        'variant_name' => $variant->size . ' / ' . $variant->pot,
        'product_height' => $variant->height,         
        'product_pot' => $variant->pot,               
        'price' => $variant->price,
        'quantity' => $item->quantity,
        'total_price' => $variant->price * $item->quantity,
                ]);
            }

            $images = $cart->items->map(function ($item) {
                $gallery = $item->productVariant->product->galleries->first();
                return $gallery ? $gallery->image : 'assets/img/bg-img/default.jpg';
            })->filter()->toArray();

            $cart->items()->delete();
            $cart->delete();

            session()->forget('applied_voucher');

            DB::commit();

            session()->flash('order_total', $finalTotal);
            session()->flash('order_id', $order->id);
            session()->flash('order_items', $images);
            return redirect()->route('checkout.thankyou');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Đã xảy ra lỗi khi đặt hàng. Vui lòng thử lại!');
        }
    }

    public function thankYou()
    {
        return view('client.users.thank_you');
    }
}
