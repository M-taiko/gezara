<?php

namespace App\Http\Requests\Udhiya;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'supplier_id'            => 'required|exists:suppliers,id',
            'date'                   => 'required|date',
            'paid'                   => 'nullable|numeric|min:0',
            'notes'                  => 'nullable|string',
            'items'                  => 'required|array|min:1',
            'items.*.product_id'     => 'required|exists:products,id',
            'items.*.quantity'       => 'required|integer|min:1',
            'items.*.weight'         => 'nullable|numeric|min:0',
            'items.*.cost_per_unit'  => 'required|numeric|min:0',
            'items.*.total'          => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'supplier_id.required'          => 'يجب اختيار المورد.',
            'supplier_id.exists'            => 'المورد غير موجود.',
            'date.required'                 => 'تاريخ الشراء مطلوب.',
            'items.required'                => 'يجب إضافة صنف واحد على الأقل.',
            'items.*.product_id.required'   => 'يجب اختيار المنتج.',
            'items.*.quantity.required'     => 'الكمية مطلوبة.',
            'items.*.cost_per_unit.required' => 'سعر الوحدة مطلوب.',
        ];
    }
}
