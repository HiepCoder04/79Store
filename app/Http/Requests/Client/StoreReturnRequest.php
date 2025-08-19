<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class StoreReturnRequest extends FormRequest
{
public function authorize(): bool { return auth()->check(); }
public function rules(): array {
    return [
        'order_detail_id'     => ['required','exists:order_details,id'],
        'quantity'            => ['required','integer','min:1','max:10'],
        'reason'              => ['required','string','min:3','max:500'],
        'images.*'            => ['nullable','image','mimes:jpg,jpeg,png,webp','max:5120'], // 5MB = 5120KB
        'bank_name'           => ['required','string','min:3','max:100'],
        'bank_account_name'   => ['required','string','min:3','max:150'],
        'bank_account_number' => ['required','string','min:8','max:50','regex:/^[0-9\-\s]+$/'],
        'return_items'        => ['required','array','min:1'],
        'return_items.*'      => ['in:plant,pot'],
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

        // ✅ KIỂM TRA RIÊNG TỪNG LOẠI - LOGIC MỚI
        $returnItems = $this->input('return_items', []);
        $qtyWant = (int) $this->input('quantity', 0);
        
        $wantPlant = in_array('plant', $returnItems);
        $wantPot = in_array('pot', $returnItems);
        
        // ✅ Nếu trả cả 2 loại → mỗi loại cần kiểm tra với số lượng riêng
        if ($wantPlant && $wantPot) {
            // Trả cả cây và chậu → số lượng phải <= min(remainingPlant, remainingPot)
            $remainingPlant = $detail->remainingPlantQty();
            $remainingPot = $detail->remainingPotQty();
            
            if ($qtyWant > $remainingPlant) {
                $v->errors()->add('quantity', "Số lượng cây trả vượt quá giới hạn. Còn có thể trả: {$remainingPlant} cây");
                return;
            }
            
            if ($qtyWant > $remainingPot) {
                $v->errors()->add('quantity', "Số lượng chậu trả vượt quá giới hạn. Còn có thể trả: {$remainingPot} chậu");
                return;
            }
            
            if (($detail->pot_price ?? 0) <= 0) {
                $v->errors()->add('return_items', 'Sản phẩm này không có chậu để trả.');
                return;
            }
        }
        // ✅ Chỉ trả cây
        elseif ($wantPlant && !$wantPot) {
            $remainingPlant = $detail->remainingPlantQty();
            if ($qtyWant > $remainingPlant) {
                $v->errors()->add('quantity', "Số lượng cây trả vượt quá giới hạn. Còn có thể trả: {$remainingPlant} cây");
                return;
            }
        }
        // ✅ Chỉ trả chậu  
        elseif (!$wantPlant && $wantPot) {
            $remainingPot = $detail->remainingPotQty();
            if ($qtyWant > $remainingPot) {
                $v->errors()->add('quantity', "Số lượng chậu trả vượt quá giới hạn. Còn có thể trả: {$remainingPot} chậu");
                return;
            }
            
            // Kiểm tra sản phẩm có chậu không
            if (($detail->pot_price ?? 0) <= 0) {
                $v->errors()->add('return_items', 'Sản phẩm này không có chậu để trả.');
                return;
            }
        }
        else {
            $v->errors()->add('return_items', 'Vui lòng chọn ít nhất cây hoặc chậu để trả.');
            return;
        }

        // ✅ Kiểm tra yêu cầu pending chồng chéo theo từng loại
        if ($wantPlant) {
            $pendingPlant = (int) \App\Models\ReturnRequest::where('order_detail_id', $detail->id)
                ->where('status', 'pending')
                ->sum('plant_quantity');
            
            $remainAfterPending = max(0, $detail->remainingPlantQty() - $pendingPlant);
            if ($qtyWant > $remainAfterPending) {
                $v->errors()->add('quantity', "Có yêu cầu trả cây chờ duyệt khác. Chỉ có thể yêu cầu thêm: {$remainAfterPending} cây");
                return;
            }
        }
        
        if ($wantPot) {
            $pendingPot = (int) \App\Models\ReturnRequest::where('order_detail_id', $detail->id)
                ->where('status', 'pending')
                ->sum('pot_quantity');
            
            $remainAfterPending = max(0, $detail->remainingPotQty() - $pendingPot);
            if ($qtyWant > $remainAfterPending) {
                $v->errors()->add('quantity', "Có yêu cầu trả chậu chờ duyệt khác. Chỉ có thể yêu cầu thêm: {$remainAfterPending} chậu");
                return;
            }
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
