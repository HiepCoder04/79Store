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
    public function index()
    {
        $orders = Order::with(['orderDetails', 'user'])->latest()->paginate(10);

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

        $order->status = $newStatus;
        $order->save();

        // Gửi email thông báo trạng thái
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

        return back()->with('success', 'Cập nhật trạng thái và gửi email thành công.');
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

        // Tổng tiền trước giảm
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

    // (Tuỳ chọn) Xoá đơn hàng
    public function destroy($id)
    {
        Order::destroy($id);
        return redirect()->route('orders.index')->with('success', 'Xoá đơn hàng thành công.');
    }
}
