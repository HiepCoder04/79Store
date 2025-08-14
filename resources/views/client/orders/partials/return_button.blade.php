@php
  $canReturn = $order->delivered_at && now()->lte(\Carbon\Carbon::parse($order->delivered_at)->addDays(7));
@endphp

@if($canReturn)
<button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#returnModal">
  Yêu cầu trả hàng
</button>
@endif

<div class="modal fade" id="returnModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST"
          action="{{ route('client.orders.returns.store', $order) }}"
          enctype="multipart/form-data">
      @csrf
      <div class="modal-header"><h5 class="modal-title">Tạo yêu cầu trả hàng</h5></div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Sản phẩm trong đơn</label>
          <select name="order_detail_id" class="form-select" required>
            @foreach($order->orderDetails as $d)
              @php $remain = $d->quantity - $d->qtyReturned(); @endphp
              @if($remain > 0)
                <option value="{{ $d->id }}" 
                        data-product-price="{{ $d->product_price ?? 0 }}"
                        data-pot-price="{{ $d->pot_price ?? 0 }}"
                        data-max-qty="{{ $remain }}">
                  #{{ $d->id }} — {{ $d->product_name ?? $d->product->name }} (còn trả: {{ $remain }})
                </option>
              @endif
            @endforeach
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">Số lượng</label>
          <input type="number" name="quantity" min="1" class="form-control" required id="return-quantity">
          <small class="text-muted">Giá trị hoàn tiền ước tính: <span id="estimated-amount">0đ</span></small>
        </div>

        <div class="mb-3">
          <label class="form-label">Lý do <span class="text-danger">*</span></label>
          <textarea name="reason" class="form-control" rows="3" 
                    placeholder="VD: Hàng bị lỗi, không đúng mô tả, kích thước không phù hợp..." 
                    required minlength="3" maxlength="500"></textarea>
          <small class="text-muted">Còn <span id="reason-count">500</span> ký tự</small>
        </div>

        <div class="mb-3">
          <label class="form-label">Ảnh minh họa (tối đa 5 ảnh, mỗi ảnh ≤5MB)</label>
          <input type="file" name="images[]" multiple accept="image/jpeg,image/jpg,image/png,image/webp" 
                 class="form-control" id="images-input">
          <small class="text-muted">Định dạng: JPG, JPEG, PNG, WEBP</small>
        </div>

        <div class="mb-3">
          <label class="form-label">Ngân hàng <span class="text-danger">*</span></label>
          <input type="text" name="bank_name" class="form-control" 
                 placeholder="VD: Vietcombank, BIDV, Techcombank..." 
                 required minlength="2" maxlength="100">
        </div>
        <div class="mb-3">
          <label class="form-label">Chủ tài khoản <span class="text-danger">*</span></label>
          <input type="text" name="bank_account_name" class="form-control" 
                 placeholder="Họ tên chủ tài khoản" 
                 required minlength="3" maxlength="150">
        </div>
        <div class="mb-3">
          <label class="form-label">Số tài khoản <span class="text-danger">*</span></label>
          <input type="text" name="bank_account_number" class="form-control" 
                 placeholder="Nhập số tài khoản ngân hàng" 
                 required minlength="8" maxlength="50" pattern="[0-9\s\-]+">
          <small class="text-muted">Chỉ được nhập số, dấu gạch ngang và khoảng trắng</small>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
        <button type="submit" class="btn btn-danger">Gửi yêu cầu</button>
      </div>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const productSelect = document.querySelector('select[name="order_detail_id"]');
    const quantityInput = document.querySelector('#return-quantity');
    const estimatedAmount = document.querySelector('#estimated-amount');
    const reasonTextarea = document.querySelector('textarea[name="reason"]');
    const reasonCount = document.querySelector('#reason-count');
    const imagesInput = document.querySelector('#images-input');
    const bankNumberInput = document.querySelector('input[name="bank_account_number"]');
    const submitBtn = document.querySelector('button[type="submit"]');
    
    // Tính toán giá hoàn tiền
    function calculateEstimatedAmount() {
        const selectedOption = productSelect.selectedOptions[0];
        if (!selectedOption) return;
        
        const productPrice = parseFloat(selectedOption.dataset.productPrice || 0);
        const potPrice = parseFloat(selectedOption.dataset.potPrice || 0);
        const quantity = parseInt(quantityInput.value || 0);
        const maxQty = parseInt(selectedOption.dataset.maxQty || 0);
        
        quantityInput.max = maxQty;
        
        const totalRefund = (productPrice + potPrice) * quantity;
        estimatedAmount.textContent = totalRefund.toLocaleString('vi-VN') + 'đ';
    }
    
    // Đếm ký tự lý do
    reasonTextarea.addEventListener('input', function() {
        const remaining = 500 - this.value.length;
        reasonCount.textContent = remaining;
        reasonCount.className = remaining < 50 ? 'text-warning' : 'text-muted';
    });
    
    // Validate file upload
    imagesInput.addEventListener('change', function() {
        const files = Array.from(this.files);
        const errors = [];
        
        if (files.length > 5) {
            errors.push('Chỉ được chọn tối đa 5 ảnh');
        }
        
        files.forEach((file, index) => {
            if (file.size > 5 * 1024 * 1024) { // Tăng lên 5MB
                errors.push(`Ảnh ${index + 1} vượt quá 5MB`);
            }
            
            if (!['image/jpeg', 'image/jpg', 'image/png', 'image/webp'].includes(file.type)) {
                errors.push(`Ảnh ${index + 1} không đúng định dạng`);
            }
        });
        
        if (errors.length > 0) {
            alert('Lỗi upload ảnh:\n' + errors.join('\n'));
            this.value = '';
        }
    });
    
    // Validate số tài khoản
    bankNumberInput.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9\s\-]/g, '');
        const cleanNumber = this.value.replace(/[\s\-]/g, '');
        
        if (cleanNumber.length < 8 && cleanNumber.length > 0) {
            this.setCustomValidity('Số tài khoản phải có ít nhất 8 chữ số');
        } else {
            this.setCustomValidity('');
        }
    });
    
    // Validate form trước khi submit
    submitBtn.addEventListener('click', function(e) {
        const reason = reasonTextarea.value.trim();
        const bankName = document.querySelector('input[name="bank_name"]').value.trim();
        const bankAccountName = document.querySelector('input[name="bank_account_name"]').value.trim();
        const bankNumber = bankNumberInput.value.trim();
        
        const errors = [];
        
        if (reason.length < 3) {
            errors.push('Lý do trả hàng phải có ít nhất 3 ký tự');
        }
        
        if (bankName.length < 2) {
            errors.push('Tên ngân hàng phải có ít nhất 2 ký tự');
        }
        
        if (bankAccountName.length < 3) {
            errors.push('Tên chủ tài khoản phải có ít nhất 3 ký tự');
        }
        
        const cleanBankNumber = bankNumber.replace(/[\s\-]/g, '');
        if (cleanBankNumber.length < 8) {
            errors.push('Số tài khoản phải có ít nhất 8 chữ số');
        }
        
        if (errors.length > 0) {
            e.preventDefault();
            alert('Vui lòng kiểm tra lại:\n' + errors.join('\n'));
            return false;
        }
    });
    
    productSelect.addEventListener('change', calculateEstimatedAmount);
    quantityInput.addEventListener('input', calculateEstimatedAmount);
    
    // Tính toán ban đầu
    calculateEstimatedAmount();
});
</script>
