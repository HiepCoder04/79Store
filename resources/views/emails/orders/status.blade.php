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
**📝 GHI CHÚ:** {{ $order->note ?? 'Không có' }}  
**💵 Tổng tiền:** <span style="color: #16a34a;"><strong>{{ number_format($order->total_after_discount, 0, ',', '.') }}₫</strong></span>  
**🧾 Thanh toán:** Thanh toán {{ strtoupper($order->payment_method) }}

---

@component('mail::button', ['url' => route('client.orders.show', $order->id), 'color' => 'success'])
Xem chi tiết đơn hàng
@endcomponent

Nếu bạn có bất kỳ câu hỏi hoặc cần hỗ trợ, đừng ngần ngại liên hệ đội ngũ của chúng tôi.  
Chúc bạn một ngày tốt lành 🌱

Trân trọng,  
**Đội ngũ 79Store**

@endcomponent
