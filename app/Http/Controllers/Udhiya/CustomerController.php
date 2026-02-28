<?php

namespace App\Http\Controllers\Udhiya;

use App\Http\Controllers\Controller;
use App\Http\Requests\Udhiya\StoreCustomerRequest;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::withCount('contracts')->latest()->paginate(15);
        return view('udhiya.customers.index', compact('customers'));
    }

    public function store(StoreCustomerRequest $request)
    {
        Customer::create($request->validated());
        return redirect()->route('udhiya.customers.index')
            ->with('toast_success', 'تم إضافة العميل بنجاح.');
    }

    public function update(StoreCustomerRequest $request, Customer $customer)
    {
        $customer->update($request->validated());
        return redirect()->route('udhiya.customers.index')
            ->with('toast_success', 'تم تحديث بيانات العميل.');
    }

    public function destroy(Customer $customer)
    {
        if ($customer->contracts()->exists()) {
            return redirect()->route('udhiya.customers.index')
                ->with('toast_error', 'لا يمكن حذف العميل: لديه صكوك مرتبطة.');
        }
        $customer->delete();
        return redirect()->route('udhiya.customers.index')
            ->with('toast_success', 'تم حذف العميل.');
    }
}
