<?php

namespace App\Http\Requests\Udhiya;

use App\Models\Contract;
use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
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
        });
        return $validator;
    }

    public function rules(): array
    {
        $contract = Contract::find($this->input('contract_id'));
        $maxAmount = $contract ? $contract->remaining_amount : 999999999;

        return [
            'contract_id'       => 'required|exists:contracts,id',
            'amount'            => "required|numeric|min:0.01|max:{$maxAmount}",
            'payment_method'    => 'required|in:cash,bank,transfer',
            'date'              => 'required|date',
            'notes'             => 'nullable|string',
            'reference_number'  => 'nullable|string|max:100',
            'wallet_id'         => 'nullable|exists:wallets,id',
            'attachments'       => 'nullable|array|max:5',
            'attachments.*'     => 'nullable|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'contract_id.required'    => 'يجب تحديد الصك.',
            'amount.required'         => 'المبلغ مطلوب.',
            'amount.max'              => 'المبلغ أكبر من المتبقي على الصك.',
            'payment_method.required' => 'يجب تحديد طريقة الدفع.',
            'date.required'           => 'تاريخ الدفعة مطلوب.',
        ];
    }
}
