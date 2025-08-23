@extends('admin.layouts.dashboard')

@section('content')
<h4>Yêu cầu trả hàng #{{ $item->id }}</h4>

@if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
@if($errors->any()) <div class="alert alert-danger">{{ $errors->first() }}</div> @endif

<div class="card mb-3">
  <div class="card-body">
    <p><strong>Mã đơn hàng:</strong> {{ $item->order->order_code }}</p>
    <p><strong>Người dùng:</strong> {{ $item->user->name ?? 'User' }}</p>
    <p><strong>Liên hệ:</strong> 
      @if($item->contact_phone)
        <a href="tel:{{ $item->contact_phone }}" class="text-decoration-none">
          <i class="fas fa-phone-alt text-primary"></i> {{ $item->contact_phone }}
        </a>
      @else
        <span class="text-warning">Chưa có Số điện thoại</span>
      @endif
      
      @if($item->contact_email)
        | <a href="mailto:{{ $item->contact_email }}" class="text-decoration-none">
          <i class="fas fa-envelope text-info"></i> {{ $item->contact_email }}
        </a>
      @endif
    </p>
    <p><strong>Sản phẩm:</strong> {{ $item->product->name ?? 'Sản phẩm' }}</p>
    <p><strong>Chi tiết:</strong> 
       @if($item->orderDetail)
         Chiều cao: {{ $item->orderDetail->product_height ?? 'N/A' }} cm | 
         Chậu: {{ $item->orderDetail->product_pot ?? 'Không có' }} |
         Giá cây: {{ number_format($item->orderDetail->product_price ?? 0, 0, ',', '.') }}đ |
         Giá chậu: {{ number_format($item->orderDetail->pot_price ?? 0, 0, ',', '.') }}đ
       @endif
    </p>
    <p><strong>Số lượng trả:</strong> 
       {{-- ✅ HIỂN THỊ CHI TIẾT VỚI TÊN CỤ THỂ --}}
       @if($item->plant_quantity > 0 && $item->pot_quantity > 0)
         <div class="mt-1">
           <span class="badge bg-success me-1">🌱 {{ $item->plant_quantity }} × {{ $item->product->name ?? 'Cây' }}</span>
           <span class="badge bg-info">🪴 {{ $item->pot_quantity }} × {{ $item->orderDetail->product_pot ?? 'Chậu' }}</span>
         </div>
         <small class="text-muted d-block">Khách hàng trả cả cây lẫn chậu</small>
       @elseif($item->plant_quantity > 0)
         <div class="mt-1">
           <span class="badge bg-success">🌱 {{ $item->plant_quantity }} × {{ $item->product->name ?? 'Cây' }}</span>
         </div>
         <small class="text-muted d-block">Khách hàng chỉ trả cây, giữ lại chậu</small>
       @elseif($item->pot_quantity > 0)
         <div class="mt-1">
           <span class="badge bg-info">🪴 {{ $item->pot_quantity }} × {{ $item->orderDetail->product_pot ?? 'Chậu' }}</span>
         </div>
         <small class="text-muted d-block">Khách hàng chỉ trả chậu, giữ lại cây</small>
       @else
         <div class="mt-1">
           <span class="badge bg-secondary">❓ {{ $item->quantity }} (không rõ loại)</span>
         </div>
         <small class="text-warning d-block">⚠️ Dữ liệu từ phiên bản cũ, chưa phân loại cây/chậu</small>
       @endif
    </p>
    <p><strong>Giá trị hoàn tiền đề xuất:</strong> 
       @php
         // ✅ TÍNH ĐÚNG theo plant_quantity và pot_quantity
         $suggestedAmount = 0;
         if ($item->orderDetail) {
             $productPrice = $item->orderDetail->product_price ?? 0;
             $potPrice = $item->orderDetail->pot_price ?? 0;
             $plantRefund = $productPrice * ($item->plant_quantity ?? 0);
             $potRefund = $potPrice * ($item->pot_quantity ?? 0);
             $suggestedAmount = $plantRefund + $potRefund;
         }
       @endphp
       <strong class="text-success">{{ number_format($suggestedAmount, 0, ',', '.') }}đ</strong>
       @if($item->plant_quantity > 0 && $item->pot_quantity > 0)
         <br><small class="text-muted">
           ({{ number_format(($item->orderDetail->product_price ?? 0) * $item->plant_quantity, 0, ',', '.') }}đ cây + 
            {{ number_format(($item->orderDetail->pot_price ?? 0) * $item->pot_quantity, 0, ',', '.') }}đ chậu)
         </small>
       @endif
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
    <p><strong>Tài khoản ngân hàng:</strong> {{ $item->bank_name }} — {{ $item->bank_account_name }} — {{ $item->bank_account_number }}</p>

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
            <strong>Thông tin chi tiết:</strong><br>
            @if($item->plant_quantity > 0)
              - <strong>Cây:</strong> {{ $item->product->name ?? 'Sản phẩm' }} - 
              Giá: {{ number_format($item->orderDetail->product_price ?? 0, 0, ',', '.') }}đ × {{ $item->plant_quantity }} = 
              <strong>{{ number_format(($item->orderDetail->product_price ?? 0) * ($item->plant_quantity ?? 0), 0, ',', '.') }}đ</strong><br>
            @endif
            @if($item->pot_quantity > 0)
              - <strong>Chậu:</strong> {{ $item->orderDetail->product_pot ?? 'Chậu' }} - 
              Giá: {{ number_format($item->orderDetail->pot_price ?? 0, 0, ',', '.') }}đ × {{ $item->pot_quantity }} = 
              <strong>{{ number_format(($item->orderDetail->pot_price ?? 0) * ($item->pot_quantity ?? 0), 0, ',', '.') }}đ</strong><br>
            @endif
            <hr class="my-2">
            <strong>💰 Tổng hoàn tiền đề xuất: {{ number_format($suggestedAmount, 0, ',', '.') }}đ</strong>
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
  <thead><tr><th>STT</th><th>Loại giao dịch</th><th>Số tiền</th><th>Bằng chứng</th><th>Ghi chú</th><th>Thời gian xử lý</th></tr></thead>
  <tbody>
    @forelse($item->transactions as $t)
      <tr>
        <td>{{ $t->id }}</td>
        <td>
          {{-- ✅ Chuyển đổi trạng thái sang tiếng Việt --}}
          @switch($t->type)
            @case('refund')
              <span class="badge bg-success">Đã hoàn tiền</span>
              @break
            @case('exchange')
              <span class="badge bg-primary">Đã đổi hàng</span>
              @break
            @case('partial_refund')
              <span class="badge bg-warning">Hoàn tiền một phần</span>
              @break
            @default
              <span class="badge bg-secondary">{{ ucfirst($t->type) }}</span>
          @endswitch
        </td>
        <td>
          @if($t->amount > 0)
            <strong class="text-success">{{ number_format($t->amount, 0, ',', '.') }}đ</strong>
          @else
            <span class="text-muted">-</span>
          @endif
        </td>
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
        <td>{{ $t->note ?: '-' }}</td>
        <td>{{ optional($t->processed_at)->format('d/m/Y H:i') ?: '-' }}</td>
      </tr>
    @empty
      <tr><td colspan="6" class="text-muted text-center py-3">Chưa có giao dịch nào.</td></tr>
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
