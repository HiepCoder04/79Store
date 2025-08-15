<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderStatusMail;

class OrderController extends Controller
{
    // Hiển thị danh sách đơn hàng
    public function index(Request $request)
    {
        $query = Order::with(['orderDetails', 'user']);

        // ✅ Thêm các bộ lọc mà không thay đổi logic cũ
        // Lọc theo trạng thái đơn hàng
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Lọc theo thời gian
        if ($request->filled('date_filter')) {
            switch ($request->date_filter) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'yesterday':
                    $query->whereDate('created_at', today()->subDay());
                    break;
                case 'this_week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                    break;
                case 'last_month':
                    $query->whereMonth('created_at', now()->subMonth()->month)
                          ->whereYear('created_at', now()->subMonth()->year);
                    break;
                case 'custom':
                    if ($request->filled('start_date')) {
                        $query->whereDate('created_at', '>=', $request->start_date);
                    }
                    if ($request->filled('end_date')) {
                        $query->whereDate('created_at', '<=', $request->end_date);
                    }
                    break;
            }
        }

        // Lọc theo phương thức thanh toán
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Lọc theo trạng thái thanh toán
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Tìm kiếm nâng cao
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('id', 'like', "%{$searchTerm}%")
                  ->orWhere('name', 'like', "%{$searchTerm}%")
                  ->orWhere('phone', 'like', "%{$searchTerm}%")
                  ->orWhereHas('user', function ($userQuery) use ($searchTerm) {
                      $userQuery->where('name', 'like', "%{$searchTerm}%")
                               ->orWhere('email', 'like', "%{$searchTerm}%");
                  })
                  ->orWhereHas('orderDetails', function ($detailQuery) use ($searchTerm) {
                      $detailQuery->where('product_name', 'like', "%{$searchTerm}%");
                  });
            });
        }

        // Lọc theo giá trị đơn hàng
        if ($request->filled('amount_filter')) {
            switch ($request->amount_filter) {
                case 'under_500k':
                    $query->where('total_after_discount', '<', 500000);
                    break;
                case '500k_1m':
                    $query->whereBetween('total_after_discount', [500000, 1000000]);
                    break;
                case '1m_2m':
                    $query->whereBetween('total_after_discount', [1000000, 2000000]);
                    break;
                case '2m_5m':
                    $query->whereBetween('total_after_discount', [2000000, 5000000]);
                    break;
                case 'over_5m':
                    $query->where('total_after_discount', '>', 5000000);
                    break;
                case 'custom':
                    if ($request->filled('min_amount')) {
                        $query->where('total_after_discount', '>=', $request->min_amount);
                    }
                    if ($request->filled('max_amount')) {
                        $query->where('total_after_discount', '<=', $request->max_amount);
                    }
                    break;
            }
        }

        // Sắp xếp (thêm vào logic cũ)
        if ($request->filled('sort_by')) {
            switch ($request->sort_by) {
                case 'oldest':
                    $query->oldest();
                    break;
                case 'highest_value':
                    $query->orderBy('total_after_discount', 'desc');
                    break;
                case 'lowest_value':
                    $query->orderBy('total_after_discount', 'asc');
                    break;
                case 'status_priority':
                    $query->orderByRaw("FIELD(status, 'pending', 'confirmed', 'shipping', 'delivered', 'cancelled', 'returned')");
                    break;
                default:
                    $query->latest();
                    break;
            }
        } else {
            $query->latest(); 
        }

        $orders = $query->paginate(10)->appends($request->query());

       
        foreach ($orders as $order) {
            $order->total_amount = $order->orderDetails->sum(function ($item) {
                return $item->quantity * $item->price;
            });
        }

        return view('admin.orders.index', compact('orders'));
    }

    // Định nghĩa luồng trạng thái hợp lệ
    private $statusFlow = [
        'pending' => ['confirmed', 'cancelled'],
        'confirmed' => ['shipping', 'cancelled'],
        'shipping' => ['delivered'],
        'delivered' => ['returned'],
        'cancelled' => [], // Không thể chuyển sang trạng thái khác
        'returned' => []   // Không thể chuyển sang trạng thái khác
    ];

    // Cập nhật trạng thái đơn hàng với kiểm tra logic
    public function updateStatus(Request $request, $id)
    {
        $order = Order::with(['user', 'address'])->findOrFail($id);
        $newStatus = $request->input('status');
        $currentStatus = $order->status;

        // Kiểm tra xem trạng thái mới có hợp lệ không
        if (!$this->isValidStatusTransition($currentStatus, $newStatus)) {
            return back()->with('error', 'Không thể chuyển từ trạng thái "' . $this->getStatusLabel($currentStatus) . '" sang "' . $this->getStatusLabel($newStatus) . '".');
        }
        
    
        if ($newStatus === 'delivered' && $currentStatus !== 'delivered') {
            $order->delivered_at = now();
        }

        $order->status = $newStatus;
        $order->save();

 
        $statusText = match ($newStatus) {
            'pending' => 'Chờ xác nhận',
            'confirmed' => 'Đã xác nhận',
            'shipping' => 'Đơn hàng đang được giao',
            'delivered' => 'Đã nhận hàng',
            'cancelled' => 'Đơn hàng đã bị huỷ',
            'returned' => 'Đơn hàng đã được trả lại',
            default => 'Đơn hàng đã được cập nhật',
        };

        // Load user kèm địa chỉ (nếu cần trong email)
        // Gửi email
        if ($order->user && $order->user->email) {
            Mail::to($order->user->email)->send(new OrderStatusMail($order, $statusText));
        }

        return back()->with('success');
    }

    // Kiểm tra tính hợp lệ của việc chuyển trạng thái
    private function isValidStatusTransition($currentStatus, $newStatus)
    {
        // Nếu trạng thái không thay đổi thì cho phép
        if ($currentStatus === $newStatus) {
            return true;
        }

        // Kiểm tra xem trạng thái mới có trong danh sách cho phép không
        return in_array($newStatus, $this->statusFlow[$currentStatus] ?? []);
    }

    // Lấy danh sách trạng thái có thể chuyển đến
    public function getAvailableStatuses($currentStatus)
    {

        $allStatuses = [
            'pending' => 'Chờ xác nhận',
            'confirmed' => 'Đã xác nhận',
            'shipping' => 'Đang giao',
            'delivered' => 'Hoàn tất',
            'cancelled' => 'Đã huỷ',
            'returned' => 'Trả hàng'
        ];

        $availableStatuses = [];

        // Luôn hiển thị trạng thái hiện tại
        $availableStatuses[$currentStatus] = $allStatuses[$currentStatus];

        // Thêm các trạng thái có thể chuyển đến
        $nextStatuses = $this->statusFlow[$currentStatus] ?? [];
        foreach ($nextStatuses as $status) {
            $availableStatuses[$status] = $allStatuses[$status];
        }

        return $availableStatuses;
    }

    // Lấy nhãn hiển thị của trạng thái
    private function getStatusLabel($status)
    {

        $labels = [
            'pending' => 'Chờ xác nhận',
            'confirmed' => 'Đã xác nhận',
            'shipping' => 'Đang giao',
            'delivered' => 'Hoàn tất',
            'cancelled' => 'Đã huỷ',
            'returned' => 'Trả hàng'
        ];

        return $labels[$status] ?? $status;
    }

    // Xem chi tiết 1 đơn hàng
    public function show($id)
    {
        $order = Order::with([
            'user.addresses',
            'orderDetails.productVariant'
        ])->findOrFail($id);

 
        $totalBeforeDiscount = 0;
        foreach ($order->orderDetails as $item) {
            $totalBeforeDiscount += $item->price * $item->quantity;
        }

        $discount = $order->discount ?? 0;
        $total = $totalBeforeDiscount - $discount;

        // Lấy danh sách trạng thái có thể chuyển đến
        $availableStatuses = $this->getAvailableStatuses($order->status);

        return view('admin.orders.show', compact('order', 'totalBeforeDiscount', 'discount', 'total', 'availableStatuses'));
    }

    // Form chỉnh sửa đơn hàng
    public function edit($id)
    {
        $order = Order::findOrFail($id);
        return view('admin.orders.edit', compact('order'));
    }

    // Cập nhật đơn hàng
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,shipping,delivered,cancelled',
            'payment_method' => 'nullable|string|max:255',
            'shipping_method' => 'nullable|string|max:255',
        ]);
        $order = Order::findOrFail($id);
        dd($request->all());
        $order->update($request->only(['status', 'shipping_method', 'payment_method']));
        return redirect()->route('orders.index')->with('success', 'Cập nhật đơn hàng thành công.');
    }

    public function destroy($id)
    {
        Order::destroy($id);
        return redirect()->route('orders.index')->with('success', 'Xoá đơn hàng thành công.');
    }
}
