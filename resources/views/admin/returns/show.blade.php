@extends('admin.layouts.dashboard')

@section('content')
<h4>Yêu cầu trả hàng #{{ $item->id }}</h4>

@if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
@if($errors->any()) <div class="alert alert-danger">{{ $errors->first() }}</div> @endif

<div class="card mb-3">
  <div class="card-body">
    <p><strong>Order:</strong> #{{ $item->order_id }}</p>
    <p><strong>User:</strong> {{ $item->user->name ?? 'User' }} (ID: {{ $item->user_id }})</p>
    <p><strong>Sản phẩm:</strong> {{ $item->product->name ?? 'Sản phẩm' }}</p>
    <p><strong>Chi tiết:</strong> 
       @if($item->orderDetail)
         Chiều cao: {{ $item->orderDetail->product_height ?? 'N/A' }} cm | 
         Chậu: {{ $item->orderDetail->product_pot ?? 'Không có' }} |
         Giá cây: {{ number_format($item->orderDetail->product_price ?? 0, 0, ',', '.') }}đ |
         Giá chậu: {{ number_format($item->orderDetail->pot_price ?? 0, 0, ',', '.') }}đ
       @endif
    </p>
    <p><strong>SL trả:</strong> {{ $item->quantity }}</p>
    <p><strong>Giá trị hoàn tiền đề xuất:</strong> 
       @php
         $suggestedAmount = 0;
         if ($item->orderDetail) {
             $productPrice = $item->orderDetail->product_price ?? 0;
             $potPrice = $item->orderDetail->pot_price ?? 0;
             $suggestedAmount = ($productPrice + $potPrice) * $item->quantity;
         }
       @endphp
       <strong class="text-success">{{ number_format($suggestedAmount, 0, ',', '.') }}đ</strong>
    </p>
    <p><strong>Trạng thái:</strong> 
      @switch($item->status)
        @case('pending')
          <span class="badge bg-warning">Chờ Duyệt</span>
          @break
        @case('approved')
          <span class="badge bg-info">Đã duyệt</span>
          @break
        @case('rejected')
          <span class="badge bg-secondary">Bị từ chối</span>
          @break
        @case('refunded')
          <span class="badge bg-success">Đã hoàn tiền</span>
          @break
        @case('exchanged')
          <span class="badge bg-primary">Đã đổi trả</span>
          @break
        @default
          <span class="badge bg-dark">{{ $item->status }}</span>
      @endswitch
    </p>
    <p><strong>Lý do:</strong> {{ $item->reason ?: '-' }}</p>
    <p><strong>Bank:</strong> {{ $item->bank_name }} — {{ $item->bank_account_name }} — {{ $item->bank_account_number }}</p>

    @if(!empty($item->images))
      <div class="d-flex flex-wrap gap-2">
        @foreach($item->images as $img)
          <a href="{{ asset('storage/'.$img) }}" target="_blank">
            <img src="{{ asset('storage/'.$img) }}" alt="" style="height:70px">
          </a>
        @endforeach
      </div>
    @endif
  </div>
</div>

<div class="row g-3">
  {{-- APPROVE --}}
  @if($item->status === 'pending')
  <div class="col-md-4">
    <div class="card">
      <div class="card-header">Duyệt yêu cầu</div>
      <div class="card-body">
        <form method="POST" action="{{ route('admin.returns.approve', $item->id) }}">
          @csrf
          <div class="mb-2">
            <textarea name="admin_note" class="form-control @error('admin_note') is-invalid @enderror" 
                      rows="2" placeholder="Ghi chú (tuỳ chọn)">{{ old('admin_note') }}</textarea>
            @error('admin_note')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <button class="btn btn-success w-100">Xác nhận</button>
        </form>
      </div>
    </div>
  </div>
  @endif

  {{-- REJECT --}}
  @if(in_array($item->status, ['pending','approved']))
  <div class="col-md-4">
    <div class="card">
      <div class="card-header">Từ chối</div>
      <div class="card-body">
        <form method="POST" action="{{ route('admin.returns.reject', $item->id) }}">
          @csrf
          <div class="mb-2">
            <textarea name="admin_note" class="form-control @error('admin_note') is-invalid @enderror" 
                      rows="2" placeholder="Lý do từ chối (bắt buộc)" required>{{ old('admin_note') }}</textarea>
            @error('admin_note')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <button class="btn btn-secondary w-100">Từ chối</button>
        </form>
      </div>
    </div>
  </div>
  @endif

  {{-- REFUND --}}
  @if($item->status === 'approved')
  <div class="col-md-4">
    <div class="card">
      <div class="card-header">Hoàn tiền</div>
      <div class="card-body">
        <form method="POST" action="{{ route('admin.returns.refund', $item->id) }}" enctype="multipart/form-data">
          @csrf
          <div class="alert alert-info">
            <strong>Thông tin tính toán:</strong><br>
            - Giá cây: {{ number_format($item->orderDetail->product_price ?? 0, 0, ',', '.') }}đ<br>
            - Giá chậu: {{ number_format($item->orderDetail->pot_price ?? 0, 0, ',', '.') }}đ<br>
            - Số lượng: {{ $item->quantity }}<br>
            <strong>Tổng đề xuất: {{ number_format($suggestedAmount, 0, ',', '.') }}đ</strong>
          </div>
          
          <div class="mb-2">
            <label class="form-label">Số tiền hoàn (VND) <span class="text-danger">*</span></label>
            <input type="number" name="amount" min="1" step="1" 
                   class="form-control @error('amount') is-invalid @enderror" 
                   value="{{ old('amount', $suggestedAmount) }}" required>
            @error('amount')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="text-muted">Số tiền đã được tính sẵn, bạn có thể điều chỉnh nếu cần</small>
          </div>
          
          <div class="mb-2">
            <label class="form-label">Hình ảnh bằng chứng chuyển khoản <span class="text-danger">*</span></label>
            <input type="file" name="proof_images[]" multiple accept="image/*" 
                   class="form-control @error('proof_images') is-invalid @enderror @error('proof_images.*') is-invalid @enderror" 
                   required>
            @error('proof_images')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            @error('proof_images.*')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="text-muted">Bắt buộc: Tối thiểu 1 ảnh, tối đa 5 ảnh, mỗi ảnh ≤5MB</small>
          </div>
          
          <div class="mb-2">
            <label class="form-label">Ghi chú <span class="text-danger">*</span></label>
            <textarea name="note" class="form-control @error('note') is-invalid @enderror" 
                      rows="2" placeholder="Ghi chú về việc hoàn tiền (bắt buộc)..." 
                      required minlength="3" maxlength="500">{{ old('note') }}</textarea>
            @error('note')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="text-muted">Bắt buộc: Ít nhất 3 ký tự</small>
          </div>
          
          <button class="btn btn-warning w-100">Xác nhận hoàn tiền</button>
        </form>
      </div>
    </div>
  </div>
  @endif
</div>

<hr>

<h5 class="mt-4">Lịch sử giao dịch</h5>
<table class="table">
  <thead><tr><th>ID</th><th>Type</th><th>Amount</th><th>Bằng chứng</th><th>Note</th><th>Processed</th></tr></thead>
  <tbody>
    @forelse($item->transactions as $t)
      <tr>
        <td>{{ $t->id }}</td>
        <td>{{ $t->type }}</td>
        <td>{{ number_format($t->amount) }}</td>
        <td>
          @if(!empty($t->proof_images))
            <div class="d-flex flex-wrap gap-1">
              @foreach($t->proof_images as $img)
                <a href="{{ asset('storage/'.$img) }}" target="_blank">
                  <img src="{{ asset('storage/'.$img) }}" alt="Bằng chứng" style="height:40px; width:40px; object-fit:cover;">
                </a>
              @endforeach
            </div>
          @else
            <span class="text-muted">-</span>
          @endif
        </td>
        <td>{{ $t->note }}</td>
        <td>{{ optional($t->processed_at)->format('d/m/Y H:i') }}</td>
      </tr>
    @empty
      <tr><td colspan="6" class="text-muted">Chưa có giao dịch.</td></tr>
    @endforelse
  </tbody>
</table>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const refundForm = document.querySelector('form[action*="refund"]');
    
    if (refundForm) {
        refundForm.addEventListener('submit', function(e) {
            const amountInput = this.querySelector('input[name="amount"]');
            const proofImagesInput = this.querySelector('input[name="proof_images[]"]');
            const noteTextarea = this.querySelector('textarea[name="note"]');
            
            const errors = [];
            
            // Kiểm tra số tiền
            const amount = parseFloat(amountInput.value);
            if (!amount || amount <= 0) {
                errors.push('Vui lòng nhập số tiền hoàn hợp lệ.');
            }
            
            // Kiểm tra ảnh bằng chứng
            if (!proofImagesInput.files || proofImagesInput.files.length === 0) {
                errors.push('Vui lòng upload ít nhất 1 ảnh bằng chứng chuyển khoản.');
            } else {
                // Kiểm tra từng file
                for (let i = 0; i < proofImagesInput.files.length; i++) {
                    const file = proofImagesInput.files[i];
                    
                    // Kiểm tra định dạng
                    if (!['image/jpeg', 'image/jpg', 'image/png'].includes(file.type)) {
                        errors.push(`Ảnh ${i + 1} không đúng định dạng (chỉ chấp nhận JPG, JPEG, PNG).`);
                    }
                    
                    // Kiểm tra kích thước - tăng lên 5MB
                    if (file.size > 5 * 1024 * 1024) {
                        errors.push(`Ảnh ${i + 1} vượt quá 5MB.`);
                    }
                }
            }
            
            // Kiểm tra ghi chú
            const note = noteTextarea.value.trim();
            if (!note || note.length < 3) {
                errors.push('Vui lòng nhập ghi chú ít nhất 3 ký tự.');
            }
            
            // Nếu có lỗi, ngăn submit và hiển thị
            if (errors.length > 0) {
                e.preventDefault();
                alert('Vui lòng kiểm tra lại:\n\n' + errors.join('\n'));
                return false;
            }
        });
    }
});
</script>
@endpush
