<?php

namespace App\Http\Controllers\Udhiya;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Wallet;
use App\Services\Udhiya\PaymentService;
use Illuminate\Http\Request;

class CollectionController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with('contract.customer', 'wallet');

        if ($request->customer_id) {
            $query->whereHas('contract', fn($q) => $q->where('customer_id', $request->customer_id));
        }
        if ($request->from) {
            $query->where('date', '>=', $request->from);
        }
        if ($request->to) {
            $query->where('date', '<=', $request->to);
        }

        $payments = $query->orderByDesc('date')->orderByDesc('id')->paginate(20)->withQueryString();

        $customers = Customer::whereHas('contracts', fn($q) => $q->where('remaining_amount', '>', 0))
            ->orderBy('name')
            ->get(['id', 'name', 'phone']);

        $wallets = Wallet::where('is_active', true)->orderBy('name')->get();

        return view('udhiya.collections.index', compact('payments', 'customers', 'wallets'));
    }

    public function create()
    {
        $customers = Customer::whereHas('contracts', fn($q) => $q->where('remaining_amount', '>', 0))
            ->orderBy('name')
            ->get(['id', 'name', 'phone']);

        $wallets = Wallet::where('is_active', true)->orderBy('name')->get();

        return view('udhiya.collections.create', compact('customers', 'wallets'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id'       => 'required|exists:customers,id',
            'contract_id'       => 'required|exists:contracts,id',
            'amount'            => 'required|numeric|min:0.01',
            'payment_method'    => 'required|in:cash,bank,transfer',
            'wallet_id'         => 'nullable|exists:wallets,id',
            'date'              => 'required|date',
            'notes'             => 'nullable|string',
            'reference_number'  => 'nullable|string|max:100',
            'attachments'       => 'nullable|array|max:5',
            'attachments.*'     => 'file|mimes:pdf,jpg,jpeg,png,gif|max:5120',
        ]);

        try {
            $contract = \App\Models\Contract::find($data['contract_id']);

            // Verify contract belongs to customer
            if ($contract->customer_id != $data['customer_id']) {
                return back()->with('toast_error', 'الصك لا ينتمي لهذا العميل');
            }

            // Verify amount doesn't exceed remaining
            if ($data['amount'] > $contract->remaining_amount) {
                return back()->with('toast_error', 'المبلغ يتجاوز المتبقي من الصك');
            }

            // Use PaymentService to create payment with all accounting logic
            $paymentService = app(PaymentService::class);
            $payment = $paymentService->store($contract, [
                'amount'         => $data['amount'],
                'payment_method' => $data['payment_method'],
                'date'           => $data['date'],
                'notes'          => $data['notes'] ?? null,
                'wallet_id'      => $data['wallet_id'] ?? null,
            ]);

            // Handle file attachments after payment is created
            $attachmentPaths = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('payments/' . date('Y/m/d'), 'public');
                    $attachmentPaths[] = $path;
                }
                $payment->update([
                    'attachment_paths' => json_encode($attachmentPaths),
                    'attachments' => collect($attachmentPaths)->map(fn($p) => basename($p))->toArray(),
                ]);
            }

            return back()->with('toast_success', 'تم تسجيل الدفعة بنجاح — رقم الإيصال: ' . $payment->receipt_number);
        } catch (\Throwable $e) {
            return back()->with('toast_error', 'خطأ عند تسجيل الدفعة: ' . $e->getMessage());
        }
    }

    public function edit(Payment $payment)
    {
        $payment->load('contract.customer');

        // Get all contracts for the customer with remaining balance or this contract
        $contracts = $payment->contract->customer->contracts()
            ->where(function($q) use ($payment) {
                $q->where('remaining_amount', '>', 0)
                  ->orWhere('id', $payment->contract_id);
            })
            ->get();

        $wallets = Wallet::where('is_active', true)->orderBy('name')->get();

        return view('udhiya.collections.edit', compact('payment', 'contracts', 'wallets'));
    }

    public function update(Request $request, Payment $payment)
    {
        $data = $request->validate([
            'contract_id' => 'required|exists:contracts,id',
            'amount'      => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank,transfer',
            'wallet_id'   => 'nullable|exists:wallets,id',
            'date'        => 'required|date',
            'notes'       => 'nullable|string',
            'receipt_number' => 'nullable|string|max:100',
            'reference_number' => 'nullable|string|max:100',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|mimes:pdf,jpg,jpeg,png,gif|max:5120',
            'remove_attachments' => 'nullable|array',
        ]);

        try {
            $contract = \App\Models\Contract::find($data['contract_id']);

            // Verify contract belongs to the same customer
            if ($contract->customer_id != $payment->contract->customer_id) {
                return back()->with('toast_error', 'الصك لا ينتمي لنفس العميل');
            }

            // Use PaymentService to update payment with accounting logic
            $paymentService = app(PaymentService::class);
            $payment = $paymentService->update($payment, [
                'contract_id'     => $data['contract_id'],
                'amount'          => $data['amount'],
                'payment_method'  => $data['payment_method'],
                'wallet_id'       => $data['wallet_id'] ?? null,
                'date'            => $data['date'],
                'notes'           => $data['notes'] ?? null,
                'receipt_number'  => $data['receipt_number'] ?? null,
                'reference_number' => $data['reference_number'] ?? null,
            ]);

            // Handle file attachments after payment is updated
            $attachmentPaths = $payment->attachments ? (array) $payment->attachments : [];

            // Remove selected attachments
            if ($request->has('remove_attachments')) {
                $indicesToRemove = array_flip($request->input('remove_attachments', []));
                $attachmentPaths = array_diff_key($attachmentPaths, $indicesToRemove);
            }

            // Add new attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('payments/' . date('Y/m/d'), 'public');
                    $attachmentPaths[] = $path;
                }
            }

            if (!empty($attachmentPaths)) {
                $payment->update([
                    'attachment_paths' => json_encode($attachmentPaths),
                    'attachments' => collect($attachmentPaths)->map(fn($p) => basename($p))->toArray(),
                ]);
            } else {
                $payment->update([
                    'attachment_paths' => null,
                    'attachments' => null,
                ]);
            }

            return redirect()->route('udhiya.collections.index')
                ->with('toast_success', 'تم تعديل الدفعة بنجاح — إيصال #' . $payment->receipt_number);
        } catch (\Throwable $e) {
            return back()->with('toast_error', 'خطأ عند تعديل الدفعة: ' . $e->getMessage());
        }
    }

    public function destroy(Payment $payment)
    {
        try {
            $paymentService = app(PaymentService::class);
            $paymentService->delete($payment);

            return back()->with('toast_success', 'تم حذف الدفعة وتحديث الحسابات.');
        } catch (\Throwable $e) {
            return back()->with('toast_error', 'خطأ عند حذف الدفعة: ' . $e->getMessage());
        }
    }

    public function print(Payment $payment)
    {
        $payment->load('contract.customer', 'wallet');
        return view('udhiya.collections.print', compact('payment'));
    }
}
