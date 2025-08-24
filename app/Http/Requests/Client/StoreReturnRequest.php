<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class StoreReturnRequest extends FormRequest
{
    public function authorize(): bool 
    { 
        return auth()->check(); 
    }

    public function rules(): array 
    {
        return [
            'order_detail_id'     => ['required','exists:order_details,id'],
            'quantity'            => ['required','integer','min:1','max:10'],
            'plant_quantity'      => ['nullable','integer','min:0','max:10'], 
            'pot_quantity'        => ['nullable','integer','min:0','max:10'],
            'reason'              => ['required','string','min:3','max:500'],
            'images.*'            => ['nullable','image','mimes:jpg,jpeg,png,webp','max:5120'], // 5MB
            'bank_name'           => ['required','string','min:3','max:100'],
            'bank_account_name'   => ['required','string','min:3','max:150'],
            'bank_account_number' => ['required','string','min:8','max:50','regex:/^[0-9\-\s]+$/'],
            'return_items'        => ['required','array','min:1'],
            'return_items.*'      => ['in:plant,pot'],
            'agree_terms'         => ['required','accepted'], // ✅ THÊM VALIDATION CHO ĐIỀU KHOẢN
        ];
    }

    public function messages(): array
    {
        return [
            'order_detail_id.required' => 'Vui lòng chọn sản phẩm cần trả.',
            'order_detail_id.exists'   => 'Sản phẩm không hợp lệ.',
            'quantity.required'        => 'Vui lòng nhập số lượng.',
            'quantity.integer'         => 'Số lượng phải là số nguyên.',
            'quantity.min'             => 'Số lượng tối thiểu là 1.',
            'quantity.max'             => 'Số lượng tối đa là 10.',
            'plant_quantity.integer'   => 'Số lượng cây phải là số nguyên.',
            'plant_quantity.max'       => 'Số lượng cây tối đa là 10.',
            'pot_quantity.integer'     => 'Số lượng chậu phải là số nguyên.',
            'pot_quantity.max'         => 'Số lượng chậu tối đa là 10.',
            'reason.required'          => 'Vui lòng nhập lý do trả hàng.',
            'reason.min'               => 'Lý do trả hàng phải có ít nhất 3 ký tự.',
            'reason.max'               => 'Lý do trả hàng không được quá 500 ký tự.',
            'images.*.image'           => 'Chỉ chấp nhận tệp hình ảnh.',
            'images.*.mimes'           => 'Ảnh phải có định dạng jpg, jpeg, png hoặc webp.',
            'images.*.max'             => 'Mỗi ảnh tối đa 5MB.',
            'bank_name.required'       => 'Vui lòng nhập tên ngân hàng.',
            'bank_name.min'            => 'Tên ngân hàng phải có ít nhất 3 ký tự.',
            'bank_account_name.required' => 'Vui lòng nhập tên chủ tài khoản.',
            'bank_account_name.min'    => 'Tên chủ tài khoản phải có ít nhất 3 ký tự.',
            'bank_account_number.required' => 'Vui lòng nhập số tài khoản.',
            'bank_account_number.min'  => 'Số tài khoản phải có ít nhất 8 ký tự.',
            'bank_account_number.regex'=> 'Số tài khoản chỉ được chứa số, dấu gạch ngang và khoảng trắng.',
            'return_items.required'    => 'Vui lòng chọn ít nhất cây hoặc chậu để trả.',
            'return_items.min'         => 'Vui lòng chọn ít nhất một loại để trả.',
            'agree_terms.required'    => 'Bạn phải đồng ý với điều khoản đổi trả hàng.',
            'agree_terms.accepted'    => 'Bạn phải chấp nhận điều khoản đổi trả hàng để tiếp tục.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($v) {
            $order = $this->route('order');
            if (!$order) {
                $v->errors()->add('order', 'Không xác định được đơn hàng.');
                return;
            }

            // Kiểm tra đơn hàng đã được giao và trong thời hạn
            if (empty($order->delivered_at)) {
                $v->errors()->add('return', 'Đơn hàng chưa được giao.');
                return;
            }
            
            $deadline = \Carbon\Carbon::parse($order->delivered_at)->addDays(7);
            if (now()->greaterThan($deadline)) {
                $v->errors()->add('return', 'Đã quá thời hạn 7 ngày để yêu cầu trả hàng.');
                return;
            }

            // Kiểm tra sản phẩm thuộc đơn hàng
            $detailId = (int) $this->input('order_detail_id');
            $detail = \App\Models\OrderDetail::find($detailId);
            if (!$detail || (int) $detail->order_id !== (int) $order->id) {
                $v->errors()->add('order_detail_id', 'Sản phẩm không thuộc đơn hàng này.');
                return;
            }

            // ✅ LOGIC MỚI: Kiểm tra cả pending requests
            $returnItems   = $this->input('return_items', []);
            $plantQtyWant  = (int) $this->input('plant_quantity', 0);
            $potQtyWant    = (int) $this->input('pot_quantity', 0);

            $wantPlant = in_array('plant', $returnItems);
            $wantPot   = in_array('pot', $returnItems);
            
            // Nếu muốn trả cây → check không vượt quá số cây còn lại (bao gồm pending)
            if ($wantPlant && $plantQtyWant > 0) {
                $remainingPlant = $detail->remainingPlantQty(); // ✅ Đã tính cả pending
                if ($plantQtyWant > $remainingPlant) {
                    $pendingPlant = $detail->plantQtyReturned() - $detail->plantQtyActuallyReturned();
                    $v->errors()->add('plant_quantity', 
                        "Không thể trả {$plantQtyWant} cây. Còn lại: {$remainingPlant} cây " .
                        "({$pendingPlant} cây đang chờ duyệt)"
                    );
                    return;
                }
            }
            
            // Nếu muốn trả chậu → check tương tự
            if ($wantPot && $potQtyWant > 0) {
                $remainingPot = $detail->remainingPotQty(); // ✅ Đã tính cả pending
                if ($potQtyWant > $remainingPot) {
                    $pendingPot = $detail->potQtyReturned() - $detail->potQtyActuallyReturned();
                    $v->errors()->add('pot_quantity', 
                        "Không thể trả {$potQtyWant} chậu. Còn lại: {$remainingPot} chậu " .
                        "({$pendingPot} chậu đang chờ duyệt)"
                    );
                    return;
                }
                
                // Check sản phẩm có chậu không
                if (($detail->pot_price ?? 0) <= 0) {
                    $v->errors()->add('return_items', 'Sản phẩm này không có chậu để trả.');
                    return;
                }
            }

            // Phải chọn ít nhất 1 loại
            if (!$wantPlant && !$wantPot) {
                $v->errors()->add('return_items', 'Vui lòng chọn ít nhất cây hoặc chậu để trả.');
                return;
            }

            if ($wantPlant && $plantQtyWant <= 0) {
                $v->errors()->add('plant_quantity', 'Vui lòng nhập số lượng cây cần trả.');
                return;
            }

            if ($wantPot && $potQtyWant <= 0) {
                $v->errors()->add('pot_quantity', 'Vui lòng nhập số lượng chậu cần trả.');
                return;
            }

            // Kiểm tra số lượng file upload
            if ($this->hasFile('images') && count($this->file('images')) > 5) {
                $v->errors()->add('images', 'Chỉ được upload tối đa 5 ảnh.');
            }

            // Kiểm tra định dạng số tài khoản chi tiết hơn
            $bankNumber = $this->input('bank_account_number');
            if ($bankNumber && (strlen(preg_replace('/[\s\-]/', '', $bankNumber)) < 8)) {
                $v->errors()->add('bank_account_number', 'Số tài khoản phải có ít nhất 8 chữ số.');
            }
        });
    }
}
