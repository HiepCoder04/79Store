{{-- @extends('client.layouts.default')

@section('content')
<h4>Lịch sử trả hàng của đơn #{{ $order->id }}</h4> --}}
@extends('client.layouts.default')
@section('title', 'Đơn hàng của tôi')
@php use Illuminate\Support\Str; @endphp

@section('content')

<!-- Banner đầu trang -->
<section class="breadcrumb-area">
    <div class="top-breadcrumb-area bg-img bg-overlay d-flex align-items-center justify-content-center"
         style="background-image: url('{{ asset('assets/img/bg-img/24.jpg') }}'); height: 250px;">
        <h2 class="text-white">Lịch sử trả hàng</h2>
    </div>
</section>

@if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
@if($errors->any()) <div class="alert alert-danger">{{ $errors->first() }}</div> @endif

<table class="table table-striped">
  <thead>
    <tr>
      <th>ID</th><th>Sản phẩm</th><th>SL</th><th>Giá trị</th><th>Trạng thái</th><th>Ngày</th><th>Phản hồi admin</th><th>Chi tiết</th>
    </tr>
  </thead>
  <tbody>
    @forelse($requests as $r)
      @php
        // ✅ TÍNH ĐÚNG theo plant_quantity và pot_quantity
        $refundValue = 0;
        if ($r->orderDetail) {
            $productPrice = $r->orderDetail->product_price ?? 0;
            $potPrice = $r->orderDetail->pot_price ?? 0;
            
            // Tính riêng từng loại
            $plantRefund = $productPrice * ($r->plant_quantity ?? 0);
            $potRefund = $potPrice * ($r->pot_quantity ?? 0);
            $refundValue = $plantRefund + $potRefund;
        }
        $actualRefund = $r->transactions()->where('type', 'refund')->sum('amount');
        $proofImages = $r->transactions()->where('type', 'refund')->first()?->proof_images ?? [];
      @endphp
      <tr>
        <td>{{ $r->id }}</td>
        <td>
          {{ $r->product->name ?? 'Sản phẩm' }}<br>
          <small class="text-muted">
            @if($r->orderDetail)
              {{ $r->orderDetail->product_height }}cm, {{ $r->orderDetail->product_pot ?? 'Không chậu' }}
            @endif
          </small>
        </td>
        <td>
          {{-- ✅ HIỂN THỊ CHI TIẾT TÊN CÂY VÀ TÊN CHẬU --}}
          @if($r->plant_quantity > 0 && $r->pot_quantity > 0)
            <div class="fw-bold text-success">{{ $r->plant_quantity }} × {{ $r->product->name ?? 'Cây' }}</div>
            <div class="fw-bold text-info">{{ $r->pot_quantity }} × chậu {{ $r->orderDetail->product_pot ?? 'Chậu' }}</div>
            <small class="text-muted">(Trả cả cây lẫn chậu)</small>
          @elseif($r->plant_quantity > 0)
            <div class="fw-bold text-success">
              <i class="fas fa-seedling"></i> {{ $r->plant_quantity }} × {{ $r->product->name ?? 'Cây' }}
            </div>
            <small class="text-muted">(Chỉ trả cây)</small>
          @elseif($r->pot_quantity > 0)
            <div class="fw-bold text-info">
              <i class="fas fa-seedling"></i> {{ $r->pot_quantity }} × Chậu {{ $r->orderDetail->product_pot ?? 'Chậu' }}
            </div>
            <small class="text-muted">(Chỉ trả chậu)</small>
          @else
            <div class="text-warning">
              <i class="fas fa-question-circle"></i> {{ $r->quantity ?? 0 }} (không rõ)
            </div>
            <small class="text-muted">Dữ liệu cũ</small>
          @endif
        </td>
        <td>
          <div class="text-muted small">Ước tính: {{ number_format($refundValue, 0, ',', '.') }}đ</div>
          @if($r->plant_quantity > 0 && $r->pot_quantity > 0)
            <div class="text-muted small">
              ({{ number_format(($r->orderDetail->product_price ?? 0) * $r->plant_quantity, 0, ',', '.') }}đ cây + 
               {{ number_format(($r->orderDetail->pot_price ?? 0) * $r->pot_quantity, 0, ',', '.') }}đ chậu)
            </div>
          @endif
          @if($actualRefund > 0)
            <div class="text-success small fw-bold">Đã hoàn: {{ number_format($actualRefund, 0, ',', '.') }}đ</div>
            @if(!empty($proofImages))
              <div class="mt-1">
                <small class="text-info">📷 Có bằng chứng chuyển khoản</small>
              </div>
            @endif
          @endif
        </td>
        <td>
          <span class="badge
            @if($r->status==='pending') bg-warning
            @elseif($r->status==='approved') bg-info
            @elseif($r->status==='refunded') bg-success
            @elseif($r->status==='rejected') bg-secondary
            @else bg-dark @endif">
            @switch($r->status)
              @case('pending') Chờ duyệt @break
              @case('approved') Đã duyệt @break
              @case('refunded') Đã hoàn tiền @break
              @case('rejected') Từ chối @break
              @default {{ $r->status }}
            @endswitch
          </span>
        </td>
        <td>{{ $r->created_at->format('d/m/Y H:i') }}</td>
        <td>
          @if($r->admin_note)
            <div class="alert alert-info p-2 mb-0" style="max-width: 200px;">
              <small><strong>📝 Admin:</strong><br>{{ $r->admin_note }}</small>
            </div>
          @else
            <span class="text-muted">Chưa có phản hồi</span>
          @endif
        </td>
        <td>
          @if($r->status === 'refunded' && !empty($proofImages))
            <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#proofModal{{ $r->id }}">
              Xem bằng chứng
            </button>
            
            <!-- Modal hiển thị bằng chứng chuyển khoản -->
            <div class="modal fade" id="proofModal{{ $r->id }}" tabindex="-1">
              <div class="modal-dialog modal-lg">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">Bằng chứng chuyển khoản - Yêu cầu #{{ $r->id }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                    <div class="row">
                      @foreach($proofImages as $img)
                        <div class="col-md-6 mb-3">
                          <img src="{{ asset('storage/'.$img) }}" class="img-fluid rounded" alt="Bằng chứng">
                        </div>
                      @endforeach
                    </div>
                  </div>
                </div>
              </div>
            </div>
          @else
            <span class="text-muted">-</span>
          @endif
        </td>
      </tr>
    @empty
      <tr><td colspan="8" class="text-center text-muted py-4">Chưa có yêu cầu trả hàng nào.</td></tr>
    @endforelse
  </tbody>
</table>

{{ $requests->links() }}
@endsection
