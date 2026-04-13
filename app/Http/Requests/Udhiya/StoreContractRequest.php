<?php

namespace App\Http\Requests\Udhiya;

use Illuminate\Foundation\Http\FormRequest;

class StoreContractRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'customer_id'              => 'required|exists:customers,id',
            'slaughter_day'            => 'nullable|date',
            'slaughter_order'          => 'nullable|integer|min:1',
            'notes'                    => 'nullable|string',
            'items'                    => 'required|array|min:1',
            'items.*.animal_id'        => 'nullable|exists:animals,id',
            'items.*.unit_price'       => 'required|numeric|min:0',
            'items.*.share_type'       => 'required|in:full,seven,six,five,quarter,third,half',
            'items.*.shares_count'     => 'required|integer|min:1|max:7',
            'items.*.group_id'         => 'nullable|exists:slaughter_groups,id',
            'payment_amount'           => 'nullable|numeric|min:0',
            'payment_method'           => 'nullable|in:cash,bank,check',
        ];
    }

    public function messages(): array
    {
        return [
            'customer_id.required'        => 'يجب اختيار العميل.',
            'items.required'              => 'يجب إضافة حيوان واحد على الأقل.',
            'items.*.unit_price.required' => 'يجب تحديد سعر الوحدة أو اختيار حيوان.',
            'items.*.share_type.required' => 'يجب تحديد نوع الحصة.',
            'items.*.share_type.in'       => 'نوع الحصة غير صحيح.',
        ];
    }
}
