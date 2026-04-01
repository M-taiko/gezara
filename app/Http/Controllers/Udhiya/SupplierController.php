<?php

namespace App\Http\Controllers\Udhiya;

use App\Http\Controllers\Controller;
use App\Http\Requests\Udhiya\StoreSupplierRequest;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::withCount('purchases')->with('purchases')->latest()->paginate(15);
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

    public function pay(Request $request, Supplier $supplier)
    {
        $data = $request->validate([
            'purchase_id' => 'required|exists:purchases,id',
            'amount'      => 'required|numeric|min:0.01',
            'paid_at'     => 'nullable|date',
            'notes'       => 'nullable|string|max:255',
        ]);

        $purchase = Purchase::findOrFail($data['purchase_id']);

        if ($purchase->supplier_id !== $supplier->id) {
            return back()->with('toast_error', 'الفاتورة لا تخص هذا المورد');
        }

        $remaining = round($purchase->total - $purchase->paid, 2);
        $amount    = (float) $data['amount'];

        if ($remaining <= 0) {
            return back()->with('toast_error', 'الفاتورة مسددة بالكامل بالفعل');
        }

        if ($amount > $remaining) {
            return back()->with('toast_error',
                'المبلغ المدخل (' . number_format($amount, 0) . ' ج.م) أكبر من المتبقي (' . number_format($remaining, 0) . ' ج.م)'
            );
        }

        $purchase->increment('paid', $amount);

        // سجل الدفعة
        SupplierPayment::create([
            'supplier_id' => $supplier->id,
            'purchase_id' => $purchase->id,
            'amount'      => $amount,
            'paid_at'     => $data['paid_at'] ?? today(),
            'notes'       => $data['notes'] ?? null,
        ]);

        // تحديث رصيد المورد
        $newBalance = $supplier->purchases()->selectRaw('SUM(total) - SUM(paid) as bal')->value('bal') ?? 0;
        $supplier->update(['balance' => $newBalance]);

        return back()->with('toast_success', 'تم تسجيل دفعة ' . number_format($amount, 0) . ' ج.م للمورد ' . $supplier->name);
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
