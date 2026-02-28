<?php

namespace App\Http\Requests\Udhiya;

use Illuminate\Foundation\Http\FormRequest;

class StoreSupplierRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('supplier')?->id;
        return [
            'name'    => 'required|string|max:255',
            'phone'   => "nullable|string|max:20|unique:suppliers,phone,{$id}",
            'address' => 'nullable|string|max:500',
            'notes'   => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم المورد مطلوب.',
            'phone.unique'  => 'رقم الهاتف مسجل بالفعل.',
        ];
    }
}
