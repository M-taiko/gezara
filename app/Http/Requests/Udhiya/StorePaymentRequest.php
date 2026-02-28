<?php

namespace App\Http\Requests\Udhiya;

use App\Models\Contract;
use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $contract = Contract::find($this->input('contract_id'));
        $maxAmount = $contract ? $contract->remaining_amount : 999999999;

        return [
            'contract_id'    => 'required|exists:contracts,id',
            'amount'         => "required|numeric|min:0.01|max:{$maxAmount}",
            'payment_method' => 'required|in:cash,bank,transfer',
            'date'           => 'required|date',
            'notes'          => 'nullable|string',
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
