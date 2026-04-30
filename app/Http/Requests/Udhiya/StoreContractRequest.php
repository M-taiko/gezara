<?php

namespace App\Http\Requests\Udhiya;

use Illuminate\Foundation\Http\FormRequest;

class StoreContractRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validate attachments
            if ($this->has('attachments') && is_array($this->attachments)) {
                $allowed = ['pdf', 'jpg', 'jpeg', 'png', 'gif'];
                foreach ($this->attachments as $idx => $file) {
                    if ($file && is_object($file)) {
                        $ext = strtolower($file->getClientOriginalExtension());
                        if (!in_array($ext, $allowed)) {
                            $validator->errors()->add(
                                'attachments.' . $idx,
                                'المرفق يجب أن يكون من نوع: PDF, JPG, PNG, GIF.'
                            );
                        }
                    }
                }
            }

            // Validate that each item has either (share_type + shares_count) or weight
            if ($this->has('items') && is_array($this->items)) {
                foreach ($this->items as $idx => $item) {
                    if (empty($item['animal_id'])) {
                        // Standalone item: must have either shares or weight
                        $hasShares = !empty($item['share_type']) && !empty($item['shares_count']);
                        $hasWeight = !empty($item['weight']);

                        if (!$hasShares && !$hasWeight) {
                            $validator->errors()->add(
                                "items.{$idx}.weight",
                                'يجب تحديد إما (نوع الحصة + عدد الحصص) أو الوزن بالكيلوجرام.'
                            );
                        }
                    }
                }
            }
        });
        return $validator;
    }

    public function rules(): array
    {
        // Get the contract ID from route if updating
        $contractId = $this->route('contract')?->id;

        return [
            'customer_id'              => 'required|exists:customers,id',
            'contract_number'          => $contractId
                ? "nullable|string|unique:contracts,contract_number,{$contractId}"
                : 'nullable|string|unique:contracts',
            'slaughter_day'            => 'nullable|date',
            'slaughter_order'          => 'nullable|integer|min:1',
            'notes'                    => 'nullable|string',
            'items'                    => 'required|array|min:1',
            'items.*.animal_id'        => 'nullable|exists:animals,id',
            'items.*.unit_price'       => 'required|numeric|min:0',
            'items.*.share_type'       => 'nullable|in:full,seven,six,five,quarter,third,half',
            'items.*.shares_count'     => 'nullable|integer|min:1|max:7',
            'items.*.weight'           => 'nullable|numeric|min:0.01',
            'items.*.group_id'         => 'nullable|exists:slaughter_groups,id',
            'payment_amount'           => 'nullable|numeric|min:0',
            'payment_method'           => 'nullable|in:cash,bank,check',
            'payment_wallet_id'        => 'nullable|exists:wallets,id',
            'payment_receipt_number'   => 'nullable|string|max:100',
            'payment_reference_number' => 'nullable|string|max:100',
            'attachments'              => 'nullable|array|max:5',
            'attachments.*'            => 'nullable|max:5120',
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
            'attachments.*.max'           => 'حجم المرفق يجب ألا يتجاوز 5 ميجابايت.',
        ];
    }
}
