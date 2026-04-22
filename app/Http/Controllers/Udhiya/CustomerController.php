<?php

namespace App\Http\Controllers\Udhiya;

use App\Http\Controllers\Controller;
use App\Http\Requests\Udhiya\StoreCustomerRequest;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $search = $request->input('search');

        $customers = Customer::withCount('contracts')
            ->with(['groupMembers.group'])
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            })
            ->latest()->paginate(20)->withQueryString();

        return view('udhiya.customers.index', compact('customers', 'search'));
    }

    public function store(StoreCustomerRequest $request)
    {
        $customer = Customer::create($request->validated());

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'customer' => $customer]);
        }

        return redirect()->route('udhiya.customers.index')
            ->with('toast_success', 'تم إضافة العميل بنجاح.');
    }

    public function update(StoreCustomerRequest $request, Customer $customer)
    {
        $customer->update($request->validated());
        return back()->with('toast_success', 'تم تحديث بيانات العميل.');
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

    public function statement(Customer $customer)
    {
        $customer->load([
            'contracts.items.animal.product',
            'contracts.payments',
            'groupMembers.group.animal.product'
        ]);

        return view('udhiya.customers.statement', compact('customer'));
    }
}
