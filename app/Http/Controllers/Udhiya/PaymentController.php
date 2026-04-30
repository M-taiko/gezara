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
            // Handle file attachments upfront
            $attachmentPaths = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('payments/' . date('Y/m/d'), 'public');
                    $attachmentPaths[] = $path;
                }
            }

            // Get validated data
            $paymentData = $request->validated();

            // Pass attachment paths to PaymentService
            if (!empty($attachmentPaths)) {
                $paymentData['attachment_paths'] = $attachmentPaths;
            }

            $payment = $this->service->store($contract, $paymentData);

            // Also add attachments to contract if they don't already exist
            if (!empty($attachmentPaths) && $contract->attachment_paths) {
                $contractAttachments = json_decode($contract->attachment_paths, true) ?? [];
                $contractAttachments = array_merge($contractAttachments, $attachmentPaths);
                $contract->update([
                    'attachment_paths' => json_encode($contractAttachments),
                    'attachments' => collect($contractAttachments)->map(fn($p) => basename($p))->toArray(),
                ]);
            } elseif (!empty($attachmentPaths)) {
                $contract->update([
                    'attachment_paths' => json_encode($attachmentPaths),
                    'attachments' => collect($attachmentPaths)->map(fn($p) => basename($p))->toArray(),
                ]);
            }

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
