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
            'contract_id'       => 'required|exists:contracts,id',
            'amount'            => "required|numeric|min:0.01|max:{$maxAmount}",
            'payment_method'    => 'required|in:cash,bank,transfer',
            'date'              => 'required|date',
            'notes'             => 'nullable|string',
            'reference_number'  => 'nullable|string|max:100',
            'wallet_id'         => 'nullable|exists:wallets,id',
            'attachments'       => 'nullable|array|max:5',
            'attachments.*'     => 'file|mimes:pdf,jpg,jpeg,png,gif|max:5120',
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
