<?php

namespace App\Http\Controllers\Udhiya;

use App\Http\Controllers\Controller;
use App\Http\Requests\Udhiya\StorePaymentRequest;
use App\Models\Contract;
use App\Models\Payment;
use App\Services\Udhiya\PaymentService;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $service) {}

    public function store(StorePaymentRequest $request)
    {
        $contract = Contract::findOrFail($request->contract_id);
        try {
            $payment = $this->service->store($contract, $request->validated());
            return redirect()->route('udhiya.contracts.show', $contract)
                ->with('toast_success', 'تم تسجيل الدفعة بنجاح. إيصال #' . $payment->receipt_number);
        } catch (\Throwable $e) {
            return back()->with('toast_error', $e->getMessage());
        }
    }

    public function printView(Payment $payment)
    {
        $payment->load('contract.customer');
        return view('udhiya.print.receipt', compact('payment'));
    }

    public function destroy(Payment $payment)
    {
        try {
            $this->service->delete($payment);
            return back()->with('toast_success', 'تم حذف الدفعة وتحديث الحسابات المالية.');
        } catch (\Throwable $e) {
            return back()->with('toast_error', $e->getMessage());
        }
    }
}
