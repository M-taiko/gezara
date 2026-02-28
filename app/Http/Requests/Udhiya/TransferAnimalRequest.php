<?php

namespace App\Http\Requests\Udhiya;

use Illuminate\Foundation\Http\FormRequest;

class TransferAnimalRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'to_warehouse_id' => 'required|exists:warehouses,id',
            'notes'           => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'to_warehouse_id.required' => 'يجب اختيار المخزن المستهدف.',
            'to_warehouse_id.exists'   => 'المخزن غير موجود.',
        ];
    }
}
