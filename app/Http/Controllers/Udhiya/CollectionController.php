<?php

namespace App\Http\Controllers\Udhiya;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Wallet;
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

        $customers = Customer::with([
            'contracts' => fn($q) => $q->where('remaining_amount', '>', 0),
        ])->orderBy('name')->get();

        $wallets = Wallet::where('is_active', true)->orderBy('name')->get();

        return view('udhiya.collections.index', compact('payments', 'customers', 'wallets'));
    }

    public function create()
    {
        $customers = Customer::with([
            'contracts' => fn($q) => $q->where('remaining_amount', '>', 0),
        ])->orderBy('name')->get();

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

        // Verify contract belongs to customer
        $contract = \App\Models\Contract::find($data['contract_id']);
        if ($contract->customer_id != $data['customer_id']) {
            return back()->with('toast_error', 'الصك لا ينتمي لهذا العميل');
        }

        // Verify amount doesn't exceed remaining
        if ($data['amount'] > $contract->remaining_amount) {
            return back()->with('toast_error', 'المبلغ يتجاوز المتبقي من الصك');
        }

        // Generate receipt number
        $lastReceipt = Payment::where('receipt_number', 'like', 'RCP-' . date('Y') . '-%')
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = $lastReceipt
            ? intval(substr($lastReceipt->receipt_number, -4)) + 1
            : 1;

        $data['receipt_number'] = 'RCP-' . date('Y') . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        // Handle file attachments
        $attachmentPaths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('payments/' . date('Y/m/d'), 'public');
                $attachmentPaths[] = $path;
            }
            $data['attachment_paths'] = json_encode($attachmentPaths);
            $data['attachments'] = collect($attachmentPaths)->map(fn($p) => basename($p))->toArray();
        }

        Payment::create($data);

        return back()->with('toast_success', 'تم تسجيل الدفعة بنجاح — رقم الإيصال: ' . $data['receipt_number']);
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
        ]);

        $contract = \App\Models\Contract::find($data['contract_id']);

        // Verify contract belongs to the same customer
        if ($contract->customer_id != $payment->contract->customer_id) {
            return back()->with('toast_error', 'الصك لا ينتمي لنفس العميل');
        }

        // Calculate the maximum allowed amount (remaining + current payment)
        $maxAmount = $contract->remaining_amount + $payment->amount;

        if ($data['amount'] > $maxAmount) {
            return back()->with('toast_error', 'المبلغ يتجاوز الحد المسموح');
        }

        $payment->update($data);

        return redirect()->route('udhiya.collections.index')
            ->with('toast_success', 'تم تعديل الدفعة بنجاح — إيصال #' . $payment->receipt_number);
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();
        return back()->with('toast_success', 'تم حذف الدفعة.');
    }

    public function print(Payment $payment)
    {
        $payment->load('contract.customer', 'wallet');
        return view('udhiya.collections.print', compact('payment'));
    }
}
