<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RefundReturnRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
public function authorize(): bool { return auth()->check(); }
public function rules(): array {
    return [
        'amount'            => ['required','integer','min:1'],
        'proof_images.*'    => ['nullable','image','mimes:jpg,jpeg,png','max:2048'],
        'note'              => ['nullable','string','max:500'],
    ];
}

public function messages(): array
{
    return [
        'amount.required' => 'Vui lòng nhập số tiền hoàn.',
        'amount.integer'  => 'Số tiền phải là số nguyên.',
        'amount.min'      => 'Số tiền hoàn phải lớn hơn 0.',
        'proof_images.*.image' => 'Chỉ chấp nhận tệp hình ảnh.',
        'proof_images.*.mimes' => 'Ảnh phải có định dạng jpg, jpeg hoặc png.',
        'proof_images.*.max'   => 'Mỗi ảnh tối đa 2MB.',
    ];
}


}
