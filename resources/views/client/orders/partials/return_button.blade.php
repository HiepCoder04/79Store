@php
  $canReturn = $order->delivered_at && now()->lte(\Carbon\Carbon::parse($order->delivered_at)->addDays(7));
@endphp

@if($canReturn)
<button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#returnModal">
  Yêu cầu trả hàng
</button>
@endif

<div class="modal fade" id="returnModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form class="modal-content" method="POST"
          action="{{ route('client.orders.returns.store', $order) }}"
          enctype="multipart/form-data">
      @csrf
      
      <!-- ✅ THÊM HIDDEN INPUTs cho số lượng riêng -->
      <input type="hidden" name="quantity" id="total-quantity" value="0">
      <input type="hidden" name="plant_quantity" id="plant-quantity-hidden" value="0">
      <input type="hidden" name="pot_quantity" id="pot-quantity-hidden" value="0">
      
      <div class="modal-header"><h5 class="modal-title">Tạo yêu cầu trả hàng</h5></div>
      <div class="modal-body">
        <!-- Giữ nguyên phần UI cũ -->
        <div class="mb-3">
          <label class="form-label">Sản phẩm trong đơn</label>
          <select name="order_detail_id" class="form-select" required>
            @foreach($order->orderDetails as $d)
              @php 
                $remainingPlant = $d->remainingPlantQty(); // ✅ Đã tính cả pending
                $remainingPot = $d->remainingPotQty();     // ✅ Đã tính cả pending
                $hasAnythingToReturn = $remainingPlant > 0 || $remainingPot > 0;
                
                // ✅ THÊM thông tin pending để user hiểu
                $pendingPlant = $d->plantQtyReturned() - $d->plantQtyActuallyReturned();
                $pendingPot = $d->potQtyReturned() - $d->potQtyActuallyReturned();
              @endphp
              @if($hasAnythingToReturn)
                <option value="{{ $d->id }}" 
                        data-product-price="{{ $d->product_price ?? 0 }}"
                        data-pot-price="{{ $d->pot_price ?? 0 }}"
                        data-has-pot="{{ ($d->pot_price ?? 0) > 0 ? 'true' : 'false' }}"
                        data-remaining-plant="{{ $remainingPlant }}"
                        data-remaining-pot="{{ $remainingPot }}">
                  #{{ $d->id }} — {{ $d->product_name ?? $d->product->name }}
                  @if($pendingPlant > 0 || $pendingPot > 0)
                    <br>⏳ Đang chờ duyệt: 
                    @if($pendingPlant > 0) {{ $pendingPlant }} cây @endif
                    @if($pendingPot > 0) {{ $pendingPot }} chậu @endif
                  @endif
                  <br>(Có thể trả: {{ $remainingPlant }} cây, {{ $remainingPot }} chậu)
                </option>
              @endif
            @endforeach
          </select>
          
        </div>

        <!-- ✅ Giữ nguyên UI cây/chậu -->
        <div class="mb-3" id="return-type-section">
          <label class="form-label">Bạn muốn trả gì? <span class="text-danger">*</span></label>
          
          <!-- Trả cây -->
          <div class="card mb-2">
            <div class="card-body py-2">
              <div class="row align-items-center">
                <div class="col-2">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="return-plant-check" name="return_items[]" value="plant">
                    <label class="form-check-label fw-bold" for="return-plant-check">🌱 Cây</label>
                  </div>
                </div>
                <div class="col-4">
                  <div class="input-group input-group-sm">
                    <span class="input-group-text">Số lượng</span>
                    <!-- ✅ BỎ name attribute, chỉ dùng để hiển thị -->
                    <input type="number" class="form-control" id="plant-quantity" 
                           min="0" max="10" value="0" disabled>
                  </div>
                </div>
                <div class="col-6">
                  <small class="text-muted">
                    Giá: <span id="plant-unit-price">0đ</span>/cây
                    → Tổng: <span id="plant-total-price" class="fw-bold text-success">0đ</span>
                  </small>
                </div>
              </div>
            </div>
          </div>

          <!-- Trả chậu -->
          <div class="card mb-2" id="pot-section">
            <div class="card-body py-2">
              <div class="row align-items-center">
                <div class="col-2">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="return-pot-check" name="return_items[]" value="pot">
                    <label class="form-check-label fw-bold" for="return-pot-check">🪴 Chậu</label>
                  </div>
                </div>
                <div class="col-4">
                  <div class="input-group input-group-sm">
                    <span class="input-group-text">Số lượng</span>
                    <!-- ✅ BỎ name attribute, chỉ dùng để hiển thị -->
                    <input type="number" class="form-control" id="pot-quantity" 
                           min="0" max="10" value="0" disabled>
                  </div>
                </div>
                <div class="col-6">
                  <small class="text-muted">
                    Giá: <span id="pot-unit-price">0đ</span>/chậu
                    → Tổng: <span id="pot-total-price" class="fw-bold text-success">0đ</span>
                  </small>
                </div>
              </div>
            </div>
          </div>

          <!-- Thông báo không có chậu -->
          <div id="no-pot-alert" class="alert alert-info py-2" style="display: none;">
            <small><i class="fas fa-info-circle"></i> Sản phẩm này không có chậu, chỉ có thể trả cây.</small>
          </div>

          <!-- Tổng hoàn tiền -->
          <div class="card bg-light">
            <div class="card-body py-2">
              <div class="row align-items-center">
                <div class="col-8">
                  <strong>💰 Tổng giá trị hoàn tiền ước tính:</strong>
                </div>
                <div class="col-4 text-end">
                  <h5 class="mb-0 text-primary" id="total-refund-amount">0đ</h5>
                </div>
              </div>
              <div class="mt-1">
                <small class="text-muted" id="refund-breakdown"></small>
              </div>
            </div>
          </div>

          <!-- Quick buttons -->
          <div class="mt-2">
            <button type="button" class="btn btn-sm btn-outline-secondary me-1" onclick="selectAllItems()">
              Chọn tất cả
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary me-1" onclick="selectOnlyPlant()">
              Chỉ cây
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="selectOnlyPot()" id="only-pot-btn">
              Chỉ chậu
            </button>
          </div>
        </div>

        <!-- ✅ Giữ nguyên các field khác -->
        <div class="mb-3">
          <label class="form-label">Lý do <span class="text-danger">*</span></label>
          <textarea name="reason" class="form-control" rows="3" 
                    placeholder="VD: Cây bị héo, chậu bị vỡ, không đúng mô tả..." 
                    required minlength="3" maxlength="500"></textarea>
          <small class="text-muted">Còn <span id="reason-count">500</span> ký tự</small>
        </div>

        <div class="mb-3">
          <label class="form-label">Ảnh minh họa (tối đa 5 ảnh, mỗi ảnh ≤5MB)</label>
          <input type="file" name="images[]" multiple accept="image/jpeg,image/jpg,image/png,image/webp" 
                 class="form-control" id="images-input">
          <small class="text-muted">Định dạng: JPG, JPEG, PNG, WEBP</small>
        </div>

        <!-- Ngân hàng -->
        <div class="mb-3">
          <label class="form-label">Ngân hàng <span class="text-danger">*</span></label>
          <select name="bank_name" class="form-select" required id="bank-select">
            <option value="">Đang tải danh sách ngân hàng...</option>
          </select>
          <div class="text-center mt-2" id="bank-loading" style="display: none;">
            <div class="spinner-border spinner-border-sm" role="status"></div>
            <small class="text-muted ms-2">Đang tải...</small>
          </div>
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

        <!-- ✅ THÊM PHẦN ĐIỀU KHOẢN ĐỔI TRẢ -->
        <hr class="my-4">
        <div class="mb-3">
          <h6 class="fw-bold text-primary"><i class="fas fa-file-contract"></i> Điều khoản đổi trả hàng</h6>
          
          <div class="card">
            <div class="card-body p-3" style="max-height: 200px; overflow-y: auto; background-color: #f8f9fa;">
              <div class="small text-dark">
                <p class="mb-2"><strong>🕐 Thời hạn:</strong> Chỉ chấp nhận đổi trả trong vòng 7 ngày kể từ ngày nhận hàng.</p>
                
                <p class="mb-2"><strong>📦 Điều kiện sản phẩm:</strong></p>
                <ul class="mb-2 ps-3">
                  <li>Cây còn nguyên vẹn, chưa bị héo úa do chăm sóc không đúng cách</li>
                  <li>Chậu không bị vỡ, nứt do tác động bên ngoài</li>
                  <li>Còn đầy đủ phụ kiện đi kèm (nếu có)</li>
                </ul>

                <p class="mb-2"><strong>✅ Trường hợp được chấp nhận:</strong></p>
                <ul class="mb-2 ps-3">
                  <li>Sản phẩm bị lỗi từ nhà sản xuất</li>
                  <li>Giao sai sản phẩm so với đơn hàng</li>
                  <li>Sản phẩm bị hư hỏng trong quá trình vận chuyển</li>
                  <li>Cây không đúng mô tả trên website</li>
                </ul>

                <p class="mb-2"><strong>❌ Trường hợp KHÔNG được chấp nhận:</strong></p>
                <ul class="mb-2 ps-3">
                  <li>Cây héo, chết do chăm sóc không đúng cách</li>
                  <li>Chậu bị vỡ do va đập sau khi nhận hàng</li>
                  <li>Thay đổi ý định mua hàng không có lý do chính đáng</li>
                  <li>Sản phẩm đã sử dụng quá 24 giờ</li>
                </ul>

                <p class="mb-2"><strong>💰 Hoàn tiền:</strong></p>
                <ul class="mb-2 ps-3">
                  <li>Hoàn 100% giá trị sản phẩm nếu lỗi từ shop</li>
                  <li>Thời gian xử lý: 3-7 ngày làm việc sau khi duyệt</li>
                  <li>Phí vận chuyển trả hàng do khách hàng chi trả (trừ trường hợp lỗi từ shop)</li>
                </ul>

                <p class="mb-2"><strong>📋 Quy trình:</strong></p>
                <ol class="mb-0 ps-3">
                  <li>Khách hàng gửi yêu cầu đổi trả qua website</li>
                  <li>79Store xem xét và phản hồi trong 24 giờ</li>
                  <li>Nếu được duyệt, khách hàng gửi hàng về theo địa chỉ shop cung cấp</li>
                  <li>Shop kiểm tra hàng và thực hiện hoàn tiền</li>
                </ol>
              </div>
            </div>
          </div>

          <div class="form-check mt-3">
            <input class="form-check-input" type="checkbox" id="agree-terms" name="agree_terms" required>
            <label class="form-check-label" for="agree-terms">
              <strong>Tôi đã đọc, hiểu và đồng ý với các điều khoản đổi trả hàng của 79Store</strong> <span class="text-danger">*</span>
            </label>
          </div>
          
          <div class="alert alert-warning mt-2 py-2">
            <small><i class="fas fa-exclamation-triangle"></i> 
              <strong>Lưu ý:</strong> Việc gửi yêu cầu không đồng nghĩa với việc tự động được chấp nhận. 
              79Store sẽ xem xét từng trường hợp cụ thể và phản hồi trong thời gian sớm nhất.
            </small>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
        <button type="submit" class="btn btn-danger" id="submit-btn" disabled>Gửi yêu cầu</button>
      </div>

    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const productSelect = document.querySelector('select[name="order_detail_id"]');
    const totalQuantityInput = document.querySelector('#total-quantity'); // ✅ Hidden input
    
    const plantCheck = document.querySelector('#return-plant-check');
    const potCheck = document.querySelector('#return-pot-check');
    const plantQuantityInput = document.querySelector('#plant-quantity');
    const potQuantityInput = document.querySelector('#pot-quantity');
    
    const plantUnitPrice = document.querySelector('#plant-unit-price');
    const potUnitPrice = document.querySelector('#pot-unit-price');
    const plantTotalPrice = document.querySelector('#plant-total-price');
    const potTotalPrice = document.querySelector('#pot-total-price');
    const totalRefundAmount = document.querySelector('#total-refund-amount');
    const refundBreakdown = document.querySelector('#refund-breakdown');
    
    const potSection = document.querySelector('#pot-section');
    const noPotAlert = document.querySelector('#no-pot-alert');
    const onlyPotBtn = document.querySelector('#only-pot-btn');
    const submitBtn = document.querySelector('#submit-btn');
    
    const reasonTextarea = document.querySelector('textarea[name="reason"]');
    const reasonCount = document.querySelector('#reason-count');
    const imagesInput = document.querySelector('#images-input');
    const bankNumberInput = document.querySelector('input[name="bank_account_number"]');
    const bankSelect = document.querySelector('#bank-select');
    const bankLoading = document.querySelector('#bank-loading');

    let currentProductPrice = 0;
    let currentPotPrice = 0;
    let maxPlantQuantity = 0; // ✅ Tách riêng
    let maxPotQuantity = 0;   // ✅ Tách riêng
    let hasPot = false;

    // ✅ Load banks (giữ nguyên)
    async function loadBankList() {
        try {
            bankLoading.style.display = 'block';
            bankSelect.innerHTML = '<option value="">Đang tải...</option>';
            
            const response = await fetch('https://api.vietqr.io/v2/banks');
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const result = await response.json();
            
            if (result.code === '00' && result.data) {
                bankSelect.innerHTML = '<option value="">-- Chọn ngân hàng --</option>';
                
                const popularBanks = ['VCB', 'TCB', 'BIDV', 'VTB', 'CTG', 'MBB', 'ACB', 'TPB', 'STB', 'HDB'];
                const bankData = result.data;
                
                const sortedBanks = bankData.sort((a, b) => {
                    const aIsPopular = popularBanks.includes(a.bin);
                    const bIsPopular = popularBanks.includes(b.bin);
                    
                    if (aIsPopular && !bIsPopular) return -1;
                    if (!aIsPopular && bIsPopular) return 1;
                    if (aIsPopular && bIsPopular) {
                        return popularBanks.indexOf(a.bin) - popularBanks.indexOf(b.bin);
                    }
                    return a.short_name.localeCompare(b.short_name);
                });
                
                sortedBanks.forEach(bank => {
                    const option = document.createElement('option');
                    option.value = bank.short_name;
                    option.textContent = `${bank.short_name} - ${bank.name}`;
                    bankSelect.appendChild(option);
                });
                
            } else {
                throw new Error(result.desc || 'Không thể lấy danh sách ngân hàng');
            }
            
        } catch (error) {
            console.error('Lỗi tải danh sách ngân hàng:', error);
            
            bankSelect.innerHTML = `
                <option value="">-- Chọn ngân hàng --</option>
                <option value="VCB">VCB - Vietcombank</option>
                <option value="TCB">TCB - Techcombank</option>
                <option value="BIDV">BIDV - Ngân hàng BIDV</option>
                <option value="VTB">VTB - VietinBank</option>
                <option value="CTG">CTG - VPBank</option>
                <option value="MBB">MBB - MB Bank</option>
                <option value="ACB">ACB - Á Châu Bank</option>
                <option value="TPB">TPB - TPBank</option>
                <option value="STB">STB - Sacombank</option>
                <option value="HDB">HDB - HDBank</option>
            `;
            
        } finally {
            bankLoading.style.display = 'none';
        }
    }

    // ✅ Update product info (sửa lại)
    function updateProductInfo() {
        const selectedOption = productSelect.selectedOptions[0];
        if (!selectedOption) return;
        
        currentProductPrice = parseFloat(selectedOption.dataset.productPrice || 0);
        currentPotPrice = parseFloat(selectedOption.dataset.potPrice || 0);
        maxPlantQuantity = parseInt(selectedOption.dataset.remainingPlant || 0); // ✅ Lấy riêng
        maxPotQuantity = parseInt(selectedOption.dataset.remainingPot || 0);     // ✅ Lấy riêng
        hasPot = selectedOption.dataset.hasPot === 'true';
        
        plantUnitPrice.textContent = currentProductPrice.toLocaleString('vi-VN') + 'đ';
        potUnitPrice.textContent = currentPotPrice.toLocaleString('vi-VN') + 'đ';
        
        plantQuantityInput.max = maxPlantQuantity; // ✅ Max riêng biệt
        potQuantityInput.max = maxPotQuantity;     // ✅ Max riêng biệt
        
        if (!hasPot || maxPotQuantity === 0) {
            potSection.style.display = 'none';
            noPotAlert.style.display = 'block';
            onlyPotBtn.style.display = 'none';
            
            potCheck.checked = false;
            potCheck.disabled = true;
            potQuantityInput.value = 0;
            potQuantityInput.disabled = true;
        } else {
            potSection.style.display = 'block';
            noPotAlert.style.display = 'none';
            onlyPotBtn.style.display = 'inline-block';
            potCheck.disabled = false;
        }
        
        // ✅ Disable các option không còn số lượng
        if (maxPlantQuantity === 0) {
            plantCheck.disabled = true;
            plantQuantityInput.disabled = true;
        } else {
            plantCheck.disabled = false;
        }
        
        plantCheck.checked = false;
        potCheck.checked = false;
        plantQuantityInput.value = 0;
        potQuantityInput.value = 0;
        plantQuantityInput.disabled = true;
        potQuantityInput.disabled = true;
        
        calculateTotalRefund();
    }

    // ✅ ĐƠN GIẢN HÓA: Chỉ cần lấy số lượng thực tế người dùng nhập
    function calculateTotalRefund() {
        const plantQty = parseInt(plantQuantityInput.value || 0);
        const potQty = parseInt(potQuantityInput.value || 0);
        
        // ✅ ĐƠN GIẢN: Cập nhật tất cả hidden inputs
        document.getElementById('plant-quantity-hidden').value = plantQty;
        document.getElementById('pot-quantity-hidden').value = potQty;
        totalQuantityInput.value = Math.max(plantQty, potQty); // Để validation backend
        
        const plantTotal = currentProductPrice * plantQty;
        const potTotal = currentPotPrice * potQty;
        const grandTotal = plantTotal + potTotal;
        
        plantTotalPrice.textContent = plantTotal.toLocaleString('vi-VN') + 'đ';
        potTotalPrice.textContent = potTotal.toLocaleString('vi-VN') + 'đ';
        totalRefundAmount.textContent = grandTotal.toLocaleString('vi-VN') + 'đ';
        
        const breakdownParts = [];
        if (plantQty > 0) {
            breakdownParts.push(`${plantQty} cây × ${currentProductPrice.toLocaleString('vi-VN')}đ`);
        }
        if (potQty > 0) {
            breakdownParts.push(`${potQty} chậu × ${currentPotPrice.toLocaleString('vi-VN')}đ`);
        }
        refundBreakdown.textContent = breakdownParts.join(' + ');
        
        const hasSelection = plantQty > 0 || potQty > 0;
        submitBtn.disabled = !hasSelection;
        submitBtn.textContent = hasSelection ? 'Gửi yêu cầu' : 'Vui lòng chọn ít nhất 1 sản phẩm';
    }

    // ✅ Event handlers (giữ nguyên)
    plantCheck.addEventListener('change', function() {
        if (this.checked) {
            plantQuantityInput.disabled = false;
            plantQuantityInput.value = Math.min(1, maxPlantQuantity);
        } else {
            plantQuantityInput.disabled = true;
            plantQuantityInput.value = 0;
        }
        calculateTotalRefund();
    });

    potCheck.addEventListener('change', function() {
        if (this.checked) {
            potQuantityInput.disabled = false;
            potQuantityInput.value = Math.min(1, maxPotQuantity);
        } else {
            potQuantityInput.disabled = true;
            potQuantityInput.value = 0;
        }
        calculateTotalRefund();
    });

    // ✅ THÊM validation đồng bộ số lượng - BỎ LOGIC ĐỒNG BỘ
    plantQuantityInput.addEventListener('input', function() {
        if (parseInt(this.value) > 0) {
            plantCheck.checked = true;
        } else {
            plantCheck.checked = false;
        }
        
        // ✅ BỎ đồng bộ số lượng - cho phép khác nhau
        calculateTotalRefund();
    });

    potQuantityInput.addEventListener('input', function() {
        if (parseInt(this.value) > 0) {
            potCheck.checked = true;
        } else {
            potCheck.checked = false;
        }
        
        // ✅ BỎ đồng bộ số lượng - cho phép khác nhau
        calculateTotalRefund();
    });

    // ✅ Quick selection functions - SỬA LOGIC ĐÚNG
    window.selectAllItems = function() {
        // Trả hết cây có trong đơn (nếu có)
        if (maxPlantQuantity > 0) {
            plantCheck.checked = true;
            plantQuantityInput.disabled = false;
            plantQuantityInput.value = maxPlantQuantity;
        }
        
        // Trả hết chậu có trong đơn (nếu có)
        if (hasPot && maxPotQuantity > 0) {
            potCheck.checked = true;
            potQuantityInput.disabled = false;
            potQuantityInput.value = maxPotQuantity;
        }
        
        calculateTotalRefund();
    };

    window.selectOnlyPlant = function() {
        // Chỉ trả hết cây
        if (maxPlantQuantity > 0) {
            plantCheck.checked = true;
            plantQuantityInput.disabled = false;
            plantQuantityInput.value = maxPlantQuantity;
        }
        
        // Bỏ chậu
        potCheck.checked = false;
        potQuantityInput.disabled = true;
        potQuantityInput.value = 0;
        
        calculateTotalRefund();
    };

    window.selectOnlyPot = function() {
        if (!hasPot || maxPotQuantity === 0) return;
        
        // Bỏ cây
        plantCheck.checked = false;
        plantQuantityInput.disabled = true;
        plantQuantityInput.value = 0;
        
        // Chỉ trả hết chậu
        potCheck.checked = true;
        potQuantityInput.disabled = false;
        potQuantityInput.value = maxPotQuantity;
        
        calculateTotalRefund();
    };
    
    // ✅ Validation (giữ nguyên các phần còn lại)
    reasonTextarea.addEventListener('input', function() {
        const remaining = 500 - this.value.length;
        reasonCount.textContent = remaining;
        reasonCount.className = remaining < 50 ? 'text-warning' : 'text-muted';
    });
    
    imagesInput.addEventListener('change', function() {
        const files = Array.from(this.files);
        const errors = [];
        
        if (files.length > 5) {
            errors.push('Chỉ được chọn tối đa 5 ảnh');
        }
        
        files.forEach((file, index) => {
            if (file.size > 5 * 1024 * 1024) {
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
    
    bankNumberInput.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9\s\-]/g, '');
        const cleanNumber = this.value.replace(/[\s\-]/g, '');
        
        if (cleanNumber.length < 8 && cleanNumber.length > 0) {
            this.setCustomValidity('Số tài khoản phải có ít nhất 8 chữ số');
        } else {
            this.setCustomValidity('');
        }
    });
    
    submitBtn.addEventListener('click', function(e) {
        const reason = reasonTextarea.value.trim();
        const bankName = bankSelect.value;
        const bankAccountName = document.querySelector('input[name="bank_account_name"]').value.trim();
        const bankNumber = bankNumberInput.value.trim();
        const agreeTerms = document.querySelector('#agree-terms').checked; // ✅ THÊM CHECK ĐIỀU KHOẢN
        
        const totalQty = parseInt(totalQuantityInput.value || 0);
        const plantQty = parseInt(plantQuantityInput.value || 0);
        const potQty = parseInt(potQuantityInput.value || 0);
        
        // ✅ THÊM LOG DEBUG CHI TIẾT HÔN
        console.log('=== DEBUG FORM SUBMIT (FIXED) ===');
        console.log('🔢 Total quantity (sent to backend):', totalQty);
        console.log('🌱 Plant quantity (UI):', plantQty);
        console.log('🪴 Pot quantity (UI):', potQty);
        console.log('✅ Return items checked:', [...document.querySelectorAll('input[name="return_items[]"]:checked')].map(el => el.value));
        console.log('📊 Max plant quantity available:', maxPlantQuantity);
        console.log('📊 Max pot quantity available:', maxPotQuantity);
        console.log('🔍 Has pot:', hasPot);
        console.log('💰 Plant price:', currentProductPrice);
        console.log('💰 Pot price:', currentPotPrice);
        
        const errors = [];
        
        if (totalQty === 0) {
            errors.push('Vui lòng chọn ít nhất 1 sản phẩm để trả');
        }
        
        // ✅ SỬA VALIDATION: Kiểm tra từng loại riêng biệt, KHÔNG dùng totalQty
        const returnItems = [...document.querySelectorAll('input[name="return_items[]"]:checked')].map(el => el.value);
        const wantPlant = returnItems.includes('plant');
        const wantPot = returnItems.includes('pot');
        
        // ✅ LOGIC MỚI: Kiểm tra riêng từng loại với số lượng thực tế
        if (wantPlant && plantQty > 0) {
            if (plantQty > maxPlantQuantity) {
                errors.push(`Số lượng cây vượt quá giới hạn. Có thể trả: ${maxPlantQuantity} cây (bạn chọn: ${plantQty})`);
            }
        }
        
        if (wantPot && potQty > 0) {
            if (potQty > maxPotQuantity) {
                errors.push(`Số lượng chậu vượt quá giới hạn. Có thể trả: ${maxPotQuantity} chậu (bạn chọn: ${potQty})`);
            }
        }
        
        if (reason.length < 3) {
            errors.push('Lý do trả hàng phải có ít nhất 3 ký tự');
        }
        
        if (!bankName) {
            errors.push('Vui lòng chọn ngân hàng');
        }
        
        if (bankAccountName.length < 3) {
            errors.push('Tên chủ tài khoản phải có ít nhất 3 ký tự');
        }
        
        const cleanBankNumber = bankNumber.replace(/[\s\-]/g, '');
        if (cleanBankNumber.length < 8) {
            errors.push('Số tài khoản phải có ít nhất 8 chữ số');
        }
        
        // ✅ THÊM VALIDATION CHO ĐIỀU KHOẢN
        if (!agreeTerms) {
            errors.push('Bạn phải đồng ý với điều khoản đổi trả hàng để tiếp tục');
        }
        
        if (errors.length > 0) {
            e.preventDefault();
            console.error('❌ Form validation errors:', errors);
            alert('Vui lòng kiểm tra lại:\n' + errors.join('\n'));
            return false;
        }
        
        console.log('✅ Form hợp lệ, đang gửi yêu cầu...');
        console.log('📤 Final form data:');
        console.log('  - plant_quantity:', plantQty);
        console.log('  - pot_quantity:', potQty);
        console.log('  - quantity (max):', totalQty);
        // ✅ Không prevent default → cho phép submit
    });
    
    // ✅ THÊM VALIDATION CHO SUBMIT BUTTON - CẬP NHẬT TRẠNG THÁI KHI CHECKBOX THAY ĐỔI
    const agreeTermsCheckbox = document.querySelector('#agree-terms');
    
    function updateSubmitButtonState() {
        const hasSelection = parseInt(totalQuantityInput.value || 0) > 0;
        const agreeTerms = agreeTermsCheckbox.checked;
        const canSubmit = hasSelection && agreeTerms;
        
        submitBtn.disabled = !canSubmit;
        
        if (!hasSelection) {
            submitBtn.textContent = 'Vui lòng chọn ít nhất 1 sản phẩm';
        } else if (!agreeTerms) {
            submitBtn.textContent = 'Vui lòng đồng ý với điều khoản';
        } else {
            submitBtn.textContent = 'Gửi yêu cầu';
        }
    }
    
    // ✅ CẬP NHẬT HÀM calculateTotalRefund() ĐỂ GỌI updateSubmitButtonState()
    function calculateTotalRefund() {
        const plantQty = parseInt(plantQuantityInput.value || 0);
        const potQty = parseInt(potQuantityInput.value || 0);
        
        // ✅ ĐƠN GIẢN: Cập nhật tất cả hidden inputs
        document.getElementById('plant-quantity-hidden').value = plantQty;
        document.getElementById('pot-quantity-hidden').value = potQty;
        totalQuantityInput.value = Math.max(plantQty, potQty); // Để validation backend
        
        const plantTotal = currentProductPrice * plantQty;
        const potTotal = currentPotPrice * potQty;
        const grandTotal = plantTotal + potTotal;
        
        plantTotalPrice.textContent = plantTotal.toLocaleString('vi-VN') + 'đ';
        potTotalPrice.textContent = potTotal.toLocaleString('vi-VN') + 'đ';
        totalRefundAmount.textContent = grandTotal.toLocaleString('vi-VN') + 'đ';
        
        const breakdownParts = [];
        if (plantQty > 0) {
            breakdownParts.push(`${plantQty} cây × ${currentProductPrice.toLocaleString('vi-VN')}đ`);
        }
        if (potQty > 0) {
            breakdownParts.push(`${potQty} chậu × ${currentPotPrice.toLocaleString('vi-VN')}đ`);
        }
        refundBreakdown.textContent = breakdownParts.join(' + ');
        
        updateSubmitButtonState(); // ✅ THAY THẾ LOGIC CŨ
    }
    
    // ✅ THÊM EVENT LISTENER CHO CHECKBOX ĐIỀU KHOẢN
    agreeTermsCheckbox.addEventListener('change', updateSubmitButtonState);
    
    productSelect.addEventListener('change', updateProductInfo);
    
    document.querySelector('#returnModal').addEventListener('shown.bs.modal', function() {
        if (bankSelect.children.length <= 1) {
            loadBankList();
        }
    });
    
    updateProductInfo();
});
</script>