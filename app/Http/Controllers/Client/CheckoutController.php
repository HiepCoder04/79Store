<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Cart;
use App\Models\Order;
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
        $addresses = $user->addresses;

        // Thêm 'user' vào compact
        return view('client.users.Checkout', compact('cart', 'addresses', 'user'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // Validate các input cơ bản
        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'payment_method' => 'required|string',
            'shipping_method' => 'nullable|string',
            'note' => 'nullable|string',
            'new_address' => 'nullable|string|max:255',
            'address_id' => 'nullable|exists:user_addresses,id',
        ]);

        // Lấy giỏ hàng
        $cart = Cart::with('items.productVariant.product')->where('user_id', $user->id)->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->back()->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        DB::beginTransaction();

        try {
            // Nếu nhập địa chỉ mới
            if ($request->filled('new_address')) {
                // Nếu đánh dấu là mặc định thì gỡ mặc định cũ
                if ($request->has('set_default')) {
                    $user->addresses()->update(['is_default' => 0]); // sẽ chạy được nếu khai báo quan hệ đúng
                }

                $newAddress = UserAddress::create([
                    'user_id' => $user->id,
                    'address' => $request->new_address,
                    'is_default' => $request->has('set_default') ? 1 : 0,
                ]);

                $addressId = $newAddress->id;
            } elseif ($request->filled('address_id')) {
                $addressId = $request->address_id;
            } else {
                return back()->with('error', 'Vui lòng chọn hoặc nhập địa chỉ giao hàng.');
            }

            // Tính tổng tiền
            $totalBefore = $cart->items->sum(function ($item) {
                return $item->productVariant->price * $item->quantity;
            });

            // Tạo đơn hàng
            $order = Order::create([
                'user_id' => $user->id,
                'address_id' => $addressId,
                'name' => $request->name,        
                'phone' => $request->phone,       
                'note' => $request->note,
                'payment_method' => $request->payment_method,
                'shipping_method' => $request->shipping_method ?? 'Giao hàng tiêu chuẩn',
                'total_before_discount' => $totalBefore,
                'discount_amount' => 0,
                'total_after_discount' => $totalBefore,
                'status' => 'pending',
                'sale_channel' => 'website',
            ]);

            // Thêm chi tiết đơn hàng
            foreach ($cart->items as $item) {
                OrderDetail::create([
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
            })->filter()->toArray(); // filter() để loại null nếu có
            // Xóa giỏ hàng
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
