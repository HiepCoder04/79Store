@component('mail::message')
{{-- Tiêu đề --}}
# 🪴 Cập nhật đơn hàng #{{ $order->id }}

Xin chào **{{ $order->name }}** 👋,

Cảm ơn bạn đã mua hàng tại **79Store**.  
Chúng tôi xin thông báo **trạng thái mới nhất** của đơn hàng của bạn như sau:

---

## 🚚 Trạng thái đơn hàng:
@component('mail::panel')
🔔 **{{ strtoupper($statusMessage) }}**
@endcomponent
<p><strong>Thời gian cập nhật:</strong> {{ now()->setTimezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i') }}</p>

---

## 📦 Thông tin đơn hàng:

**👤 Người nhận:** {{ $order->name }}  
**📞 Số điện thoại:** {{ $order->phone }}  
**📍 Địa chỉ giao hàng:** {{ optional($order->address)->address ?? 'Không có địa chỉ' }}  
**💵 Tổng tiền:** <span style="color: #16a34a;"><strong>{{ number_format($order->total_after_discount, 0, ',', '.') }}₫</strong></span>  
**🧾 Thanh toán:** {{ strtoupper($order->payment_method) }}

---

## 🛒 Danh sách sản phẩm:

<table width="100%" cellpadding="8" cellspacing="0" border="1" style="border-collapse: collapse; margin-top: 10px;">
    <thead>
        <tr>
            <th align="left">Sản phẩm</th>
            <th align="left">Loại</th>
            <th align="center">Số lượng</th>
            <th align="right">Giá</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($order->orderDetails as $item)
        <tr>
            <td>{{ $item->product_name }}</td>
            <td>{{ $item->product_height }}cm / {{ $item->product_pot }}</td>
            <td align="center">{{ $item->quantity }}</td>
            <td align="right">{{ number_format($item->price, 0, ',', '.') }}₫</td>
        </tr>
        @endforeach
    </tbody>
</table>

---

@component('mail::button', ['url' => route('client.orders.show', $order->id), 'color' => 'success'])
Xem chi tiết đơn hàng
@endcomponent

Nếu bạn có bất kỳ câu hỏi hoặc cần hỗ trợ, đừng ngần ngại liên hệ đội ngũ của chúng tôi.  
Chúc bạn một ngày tốt lành 🌱

Trân trọng,  
**Đội ngũ 79Store**

@endcomponent
