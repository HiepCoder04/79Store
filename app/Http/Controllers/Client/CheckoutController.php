<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Cart;
use App\Models\OrderDetail;
use App\Models\UserAddress;

class CheckoutController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $cart = Cart::with('items.productVariant.product')
            ->where('user_id', $user->id)
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        $addresses = UserAddress::where('user_id', $user->id)->get();

        return view('client.users.Checkout', compact('cart', 'addresses', 'user'));
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
                $existingAddress = UserAddress::where('user_id', $user->id)
                    ->where('address', $request->new_address)
                    ->first();

                if ($existingAddress) {
                    if ($request->has('set_default')) {
                        UserAddress::where('user_id', $user->id)
                            ->where('id', '!=', $existingAddress->id)
                            ->update(['is_default' => 0]);
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

            $totalBefore = $cart->items->sum(function ($item) {
                return $item->productVariant->price * $item->quantity;
            });

            $orderStatus = 'pending';
            $paymentStatus = 'unpaid';

            if ($request->payment_method == 'vnpay' && $request->payment_status == 'paid') {
                $paymentStatus = 'paid';
                $orderStatus = 'confirmed';
            }

            $order = OrderDetail::create([
                'user_id' => $user->id,
                'address_id' => $addressId,
                'name' => $request->name,
                'phone' => $request->phone,
                'note' => $request->note,
                'payment_method' => $request->payment_method,
                'payment_status' => $paymentStatus,
                'shipping_method' => $request->shipping_method ?? 'Giao hàng tiêu chuẩn',
                'total_before_discount' => $totalBefore,
                'discount_amount' => 0,
                'total_after_discount' => $totalBefore,
                'status' => $orderStatus,
                'sale_channel' => 'website',
            ]);

            foreach ($cart->items as $item) {
                DB::table('order_detail_items')->insert([
                    'order_id' => $order->id,
                    'product_id' => $item->productVariant->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'product_name' => $item->productVariant->product->name,
                    'variant_name' => $item->productVariant->size . ' / ' . $item->productVariant->pot,
                    'price' => $item->productVariant->price,
                    'quantity' => $item->quantity,
                    'total_price' => $item->productVariant->price * $item->quantity,
                ]);
            }

            $images = $cart->items->map(function ($item) {
                $gallery = $item->productVariant->product->galleries->first();
                return $gallery ? $gallery->image : 'assets/img/bg-img/default.jpg';
            })->filter()->toArray();

            $cart->items()->delete();
            $cart->delete();

            DB::commit();

            session()->flash('order_total', $totalBefore);
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
