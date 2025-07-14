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
        // Lấy địa chỉ từ bảng user_addresses
        $addresses = UserAddress::where('user_id', $user->id)->get();

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
            // Log để debug thông tin request
            Log::info('Checkout data:', [
                'new_address' => $request->new_address,
                'address_id' => $request->address_id,
                'set_default' => $request->has('set_default')
            ]);
            
            // Kiểm tra xem có địa chỉ mới không
            if ($request->filled('new_address')) {
                Log::info('Processing new address:', ['address' => $request->new_address]);
                
                // Kiểm tra xem địa chỉ này đã tồn tại chưa
                $existingAddress = UserAddress::where('user_id', $user->id)
                    ->where('address', $request->new_address)
                    ->first();
                
                if ($existingAddress) {
                    // Nếu địa chỉ đã tồn tại và yêu cầu đặt làm mặc định
                    if ($request->has('set_default')) {
                        // Reset tất cả địa chỉ khác
                        UserAddress::where('user_id', $user->id)
                            ->where('id', '!=', $existingAddress->id)
                            ->update(['is_default' => 0]);
                        
                        // Đặt địa chỉ này làm mặc định
                        $existingAddress->is_default = 1;
                        $existingAddress->save();
                    }
                    
                    $addressId = $existingAddress->id;
                    Log::info('Using existing address record:', ['id' => $addressId]);
                } else {
                    // Nếu đánh dấu là mặc định thì gỡ mặc định cũ
                    if ($request->has('set_default')) {
                        // Cập nhật tất cả địa chỉ của user này là không mặc định
                        UserAddress::where('user_id', $user->id)->update(['is_default' => 0]);
                    }
                    
                    // Tạo địa chỉ mới
                    $newAddress = UserAddress::create([
                        'user_id' => $user->id,
                        'address' => $request->new_address,
                        'is_default' => $request->has('set_default') ? 1 : 0,
                    ]);
                    
                    $addressId = $newAddress->id;
                    Log::info('Created new address:', ['id' => $newAddress->id, 'address' => $newAddress->address]);
                }
            } elseif ($request->filled('address_id')) {
                $addressId = $request->address_id;
                Log::info('Using selected address:', ['id' => $addressId]);
            } else {
                return back()->with('error', 'Vui lòng chọn hoặc nhập địa chỉ giao hàng.');
            }

            // Tính tổng tiền
            $totalBefore = $cart->items->sum(function ($item) {
                return $item->productVariant->price * $item->quantity;
            });

            // Xác định trạng thái đơn hàng dựa trên phương thức thanh toán
            $orderStatus = 'pending';
            $paymentStatus = 'unpaid';
            
            // Nếu thanh toán VNPAY đã hoàn tất
            if ($request->payment_method == 'vnpay' && isset($request->payment_status) && $request->payment_status == 'paid') {
                $paymentStatus = 'paid';
                $orderStatus = 'confirmed'; // Đơn hàng được xác nhận nếu đã thanh toán
            }
            
            // Tạo đơn hàng
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
                'discount_amount' => 0,
                'total_after_discount' => $totalBefore,
                'status' => $orderStatus,
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