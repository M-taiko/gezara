<?php

namespace App\Http\Controllers\Udhiya;

use App\Http\Controllers\Controller;
use App\Models\Advance;
use App\Models\AdvanceTransaction;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Wallet;
use App\Services\Udhiya\GeneralLedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class AdvanceController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->input('type');
        $status = $request->input('status');
        $search = $request->input('search');

        $advances = Advance::with('customer', 'supplier', 'wallet', 'transactions')
            ->when($type, fn($q) => $q->where('type', $type))
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($search, fn($q) => $q->where(function ($q2) use ($search) {
                $q2->where('advance_number', 'like', "%{$search}%")
                   ->orWhereHas('customer', fn($q3) => $q3->where('name', 'like', "%{$search}%"))
                   ->orWhereHas('supplier', fn($q3) => $q3->where('name', 'like', "%{$search}%"));
            }))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('udhiya.advances.index', compact('advances', 'type', 'status', 'search'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();
        $wallets = Wallet::where('is_active', true)->get();

        return view('udhiya.advances.create', compact('customers', 'suppliers', 'wallets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:customer,supplier',
            'customer_id' => 'required_if:type,customer|nullable|exists:customers,id',
            'supplier_id' => 'required_if:type,supplier|nullable|exists:suppliers,id',
            'wallet_id' => 'required|exists:wallets,id',
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string',
            'date' => 'required|date',
        ]);

        $advance = DB::transaction(function () use ($validated) {
            $advance = Advance::create([
                'type' => $validated['type'],
                'customer_id' => $validated['customer_id'] ?? null,
                'supplier_id' => $validated['supplier_id'] ?? null,
                'wallet_id' => $validated['wallet_id'],
                'amount' => $validated['amount'],
                'remaining' => $validated['amount'],
                'notes' => $validated['notes'],
                'status' => 'active',
                'date' => $validated['date'],
            ]);

            // Deduct from wallet
            $wallet = Wallet::lockForUpdate()->find($validated['wallet_id']);
            $wallet->decrement('balance', $validated['amount']);

            return $advance;
        });

        return redirect()->route('udhiya.advances.show', $advance)
            ->with('toast_success', 'تم تسجيل السلفة بنجاح');
    }

    public function show(Advance $advance)
    {
        $advance->load('customer', 'supplier', 'wallet', 'transactions');
        $wallets = Wallet::where('is_active', true)->get();

        return view('udhiya.advances.show', compact('advance', 'wallets'));
    }

    public function addTransaction(Request $request, Advance $advance)
    {
        $validated = $request->validate([
            'type' => 'required|in:receipt,return',
            'wallet_id' => 'required|exists:wallets,id',
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string',
        ]);

        if ($validated['amount'] > $advance->remaining) {
            return back()->with('toast_error', 'المبلغ أكبر من المتبقي من السلفة');
        }

        DB::transaction(function () use ($advance, $validated) {
            $transaction = AdvanceTransaction::create([
                'advance_id' => $advance->id,
                'type' => $validated['type'],
                'wallet_id' => $validated['wallet_id'],
                'amount' => $validated['amount'],
                'notes' => $validated['notes'],
                'date' => now(),
            ]);

            // Update wallet
            $wallet = Wallet::lockForUpdate()->find($validated['wallet_id']);
            if ($validated['type'] === 'receipt') {
                $wallet->increment('balance', $validated['amount']);
            } else {
                $wallet->decrement('balance', $validated['amount']);
            }

            // Update remaining in advance
            if ($validated['type'] === 'receipt') {
                $advance->decrement('remaining', $validated['amount']);
            } else {
                $advance->increment('remaining', $validated['amount']);
            }

            // Close advance if settled
            if ($advance->remaining <= 0) {
                $advance->update(['status' => 'settled']);
            }
        });

        return back()->with('toast_success', 'تم تسجيل العملية بنجاح');
    }

    public function accounts(Request $request)
    {
        $service = new GeneralLedgerService();

        $filters = [
            'transaction_type' => $request->input('transaction_type'),
            'wallet_id' => $request->input('wallet_id'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ];

        // Get all transactions from service
        $allTransactions = $service->getGeneralLedger($filters);

        // Calculate totals
        $totals = $service->calculateTotals($allTransactions);

        // Paginate
        $perPage = 50;
        $page = $request->get('page', 1);
        $paginatedTransactions = new LengthAwarePaginator(
            $allTransactions->forPage($page, $perPage),
            count($allTransactions),
            $perPage,
            $page,
            ['path' => route('udhiya.accounts'), 'query' => $request->query()]
        );

        $wallets = Wallet::where('is_active', true)->get();

        return view('udhiya.advances.accounts', compact(
            'paginatedTransactions',
            'wallets',
            'totals',
            'filters'
        ));
    }

    public function destroy(Advance $advance)
    {
        if ($advance->status !== 'active') {
            return back()->with('toast_error', 'لا يمكن حذف سلفة مغلقة أو ملغاة');
        }

        DB::transaction(function () use ($advance) {
            // Return to wallet
            $wallet = Wallet::lockForUpdate()->find($advance->wallet_id);
            if ($wallet) {
                $wallet->increment('balance', $advance->amount);
            }

            $advance->update(['status' => 'cancelled']);
        });

        return redirect()->route('udhiya.advances.index')
            ->with('toast_success', 'تم إلغاء السلفة بنجاح');
    }
}
