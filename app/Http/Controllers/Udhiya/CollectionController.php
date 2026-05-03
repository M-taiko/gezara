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
            'receipt_number'    => 'nullable|string|max:100',
            'reference_number'  => 'nullable|string|max:100',
            'date'              => 'required|date',
            'notes'             => 'nullable|string',
            'attachments'       => 'nullable|array|max:5',
            'attachments.*'     => 'nullable|max:5120',
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

            // Handle file attachments upfront
            $attachmentPaths = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('payments/' . date('Y/m/d'), 'public');
                    $attachmentPaths[] = $path;
                }
            }

            // Use PaymentService to create payment with all accounting logic
            $paymentService = app(PaymentService::class);
            $paymentData = [
                'amount'           => $data['amount'],
                'payment_method'   => $data['payment_method'],
                'receipt_number'   => $data['receipt_number'] ?? null,
                'reference_number' => $data['reference_number'] ?? null,
                'date'             => $data['date'],
                'notes'            => $data['notes'] ?? null,
                'wallet_id'        => $data['wallet_id'] ?? null,
            ];

            // Pass attachment paths to PaymentService
            if (!empty($attachmentPaths)) {
                $paymentData['attachment_paths'] = $attachmentPaths;
            }

            $payment = $paymentService->store($contract, $paymentData);

            // Sync new attachments to the contract as well
            if (!empty($attachmentPaths)) {
                $contractPaths = $contract->attachment_paths
                    ? json_decode($contract->attachment_paths, true)
                    : [];
                $contractPaths = array_values(array_unique(array_merge($contractPaths, $attachmentPaths)));
                $contract->update([
                    'attachment_paths' => json_encode($contractPaths),
                    'attachments'      => collect($contractPaths)->map(fn($p) => basename($p))->toArray(),
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
            'attachments.*' => 'nullable|max:5120',
            'remove_attachments' => 'nullable|array',
        ]);

        try {
            $contract = \App\Models\Contract::find($data['contract_id']);

            // Verify contract belongs to the same customer
            if ($contract->customer_id != $payment->contract->customer_id) {
                return back()->with('toast_error', 'الصك لا ينتمي لنفس العميل');
            }

            // Handle file attachments upfront
            $oldPaths = $payment->attachment_paths
                ? (json_decode($payment->attachment_paths, true) ?? [])
                : [];

            // Determine paths to remove
            $removedIndices = array_map('intval', $request->input('remove_attachments', []));
            $removedPaths   = array_values(array_intersect_key($oldPaths, array_flip($removedIndices)));

            // Build updated payment paths (remove selected)
            $attachmentPaths = array_values(array_diff_key($oldPaths, array_flip($removedIndices)));

            // Add newly uploaded files
            $newPaths = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    if ($file && $file->isValid()) {
                        $path          = $file->store('payments/' . date('Y/m/d'), 'public');
                        $attachmentPaths[] = $path;
                        $newPaths[]    = $path;
                    }
                }
            }
            $attachmentPaths = array_values($attachmentPaths);

            // Use PaymentService to update payment with accounting logic
            $paymentService = app(PaymentService::class);
            $paymentData = [
                'contract_id'      => $data['contract_id'],
                'amount'           => $data['amount'],
                'payment_method'   => $data['payment_method'],
                'wallet_id'        => $data['wallet_id'] ?? null,
                'date'             => $data['date'],
                'notes'            => $data['notes'] ?? null,
                'receipt_number'   => $data['receipt_number'] ?? null,
                'reference_number' => $data['reference_number'] ?? null,
                'attachment_paths' => $attachmentPaths,
            ];

            $payment = $paymentService->update($payment, $paymentData);

            // Sync attachment changes to the linked contract
            $contractPaths = $contract->attachment_paths
                ? (json_decode($contract->attachment_paths, true) ?? [])
                : [];

            // Remove from contract any paths deleted from the payment
            if (!empty($removedPaths)) {
                $contractPaths = array_values(array_filter(
                    $contractPaths,
                    fn($p) => !in_array($p, $removedPaths)
                ));
            }

            // Add new paths to contract (avoid duplicates)
            if (!empty($newPaths)) {
                $contractPaths = array_values(array_unique(array_merge($contractPaths, $newPaths)));
            }

            $contract->update([
                'attachment_paths' => !empty($contractPaths) ? json_encode($contractPaths) : null,
                'attachments'      => !empty($contractPaths)
                    ? collect($contractPaths)->map(fn($p) => basename($p))->toArray()
                    : null,
            ]);

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
