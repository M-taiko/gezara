<?php

namespace App\Http\Controllers\Udhiya;

use App\Http\Controllers\Controller;
use App\Http\Requests\Udhiya\StoreSupplierRequest;
use App\Models\Supplier;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::withCount('purchases')->latest()->paginate(15);
        return view('udhiya.suppliers.index', compact('suppliers'));
    }

    public function store(StoreSupplierRequest $request)
    {
        Supplier::create($request->validated());
        return redirect()->route('udhiya.suppliers.index')
            ->with('toast_success', 'تم إضافة المورد بنجاح.');
    }

    public function update(StoreSupplierRequest $request, Supplier $supplier)
    {
        $supplier->update($request->validated());
        return redirect()->route('udhiya.suppliers.index')
            ->with('toast_success', 'تم تحديث بيانات المورد.');
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->purchases()->exists()) {
            return redirect()->route('udhiya.suppliers.index')
                ->with('toast_error', 'لا يمكن حذف المورد: لديه مشتريات مرتبطة.');
        }
        $supplier->delete();
        return redirect()->route('udhiya.suppliers.index')
            ->with('toast_success', 'تم حذف المورد.');
    }
}
