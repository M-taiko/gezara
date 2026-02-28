<?php

namespace App\Http\Requests\Udhiya;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('customer')?->id;
        return [
            'name'    => 'required|string|max:255',
            'phone'   => "required|string|max:20|unique:customers,phone,{$id}",
            'address' => 'nullable|string|max:500',
            'notes'   => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => 'اسم العميل مطلوب.',
            'phone.required' => 'رقم هاتف العميل مطلوب.',
            'phone.unique'   => 'رقم الهاتف مسجل بالفعل.',
        ];
    }
}
