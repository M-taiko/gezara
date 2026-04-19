<?php

namespace App\Http\Controllers\Udhiya;

use App\Http\Controllers\Controller;
use App\Models\Advance;
use App\Models\AdvanceTransaction;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Wallet;
use App\Models\Purchase;
use App\Models\MeatSale;
use App\Models\Payment;
use App\Models\Contract;
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
        $transaction_type = $request->input('transaction_type'); // 'advance', 'purchase', 'sale', 'payment'
        $wallet_id = $request->input('wallet_id');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');

        // Build base date queries
        $dateFilter = function($query) use ($start_date, $end_date) {
            if ($start_date) $query->whereDate('created_at', '>=', $start_date);
            if ($end_date) $query->whereDate('created_at', '<=', $end_date);
            return $query;
        };

        $allTransactions = collect();

        // Advance Transactions
        if (!$transaction_type || $transaction_type === 'advance') {
            $advances = AdvanceTransaction::with('advance.customer', 'advance.supplier', 'wallet')
                ->when($wallet_id, fn($q) => $q->where('wallet_id', $wallet_id))
                ->when($start_date, fn($q) => $q->whereDate('date', '>=', $start_date))
                ->when($end_date, fn($q) => $q->whereDate('date', '<=', $end_date))
                ->get()
                ->map(function ($t) {
                    return [
                        'id' => $t->id,
                        'type' => 'advance',
                        'transaction_type' => $t->type === 'receipt' ? 'استلام' : 'رد',
                        'date' => $t->date,
                        'description' => ($t->advance->type === 'customer' ? 'سلف عميل' : 'سلف مورد') . ' - ' . $t->advance->getName(),
                        'reference' => $t->advance->advance_number,
                        'reference_url' => route('udhiya.advances.show', $t->advance),
                        'debit' => $t->type === 'receipt' ? $t->amount : 0,
                        'credit' => $t->type === 'return' ? $t->amount : 0,
                        'wallet_name' => $t->wallet?->name ?? '—',
                        'wallet_id' => $t->wallet_id,
                        'notes' => $t->notes ?? '',
                    ];
                });
            $allTransactions = $allTransactions->concat($advances);
        }

        // Purchase Transactions
        if (!$transaction_type || $transaction_type === 'purchase') {
            $purchases = Purchase::with('supplier')
                ->when($wallet_id, fn($q) => $q->where('wallet_id', $wallet_id ?? null))
                ->when($start_date, fn($q) => $q->whereDate('date', '>=', $start_date))
                ->when($end_date, fn($q) => $q->whereDate('date', '<=', $end_date))
                ->get()
                ->map(function ($p) {
                    return [
                        'id' => $p->id,
                        'type' => 'purchase',
                        'transaction_type' => 'شراء',
                        'date' => $p->date,
                        'description' => 'فاتورة شراء - ' . ($p->supplier?->name ?? '—'),
                        'reference' => $p->id,
                        'reference_url' => route('udhiya.purchases.show', $p),
                        'debit' => $p->total,
                        'credit' => 0,
                        'wallet_name' => '—',
                        'wallet_id' => null,
                        'notes' => $p->notes ?? '',
                    ];
                });
            $allTransactions = $allTransactions->concat($purchases);
        }

        // Sale Transactions
        if (!$transaction_type || $transaction_type === 'sale') {
            $sales = MeatSale::with('inventory')
                ->when($start_date, fn($q) => $q->whereDate('sale_date', '>=', $start_date))
                ->when($end_date, fn($q) => $q->whereDate('sale_date', '<=', $end_date))
                ->get()
                ->map(function ($s) {
                    return [
                        'id' => $s->id,
                        'type' => 'sale',
                        'transaction_type' => 'بيع',
                        'date' => $s->sale_date,
                        'description' => 'بيع لحوم - ' . $s->customer_name,
                        'reference' => $s->id,
                        'reference_url' => null,
                        'debit' => 0,
                        'credit' => $s->total_amount,
                        'wallet_name' => '—',
                        'wallet_id' => null,
                        'notes' => $s->notes ?? '',
                    ];
                });
            $allTransactions = $allTransactions->concat($sales);
        }

        // Payment Transactions
        if (!$transaction_type || $transaction_type === 'payment') {
            $payments = Payment::with('wallet', 'contract')
                ->when($wallet_id, fn($q) => $q->where('wallet_id', $wallet_id))
                ->when($start_date, fn($q) => $q->whereDate('date', '>=', $start_date))
                ->when($end_date, fn($q) => $q->whereDate('date', '<=', $end_date))
                ->get()
                ->map(function ($p) {
                    return [
                        'id' => $p->id,
                        'type' => 'payment',
                        'transaction_type' => 'دفع',
                        'date' => $p->date,
                        'description' => 'دفع صك #' . ($p->contract?->contract_number ?? '—'),
                        'reference' => $p->receipt_number,
                        'reference_url' => $p->contract ? route('udhiya.contracts.show', $p->contract) : null,
                        'debit' => 0,
                        'credit' => $p->amount,
                        'wallet_name' => $p->wallet?->name ?? '—',
                        'wallet_id' => $p->wallet_id,
                        'notes' => $p->notes ?? '',
                    ];
                });
            $allTransactions = $allTransactions->concat($payments);
        }

        // Contract Transactions (صكوك العملاء)
        if (!$transaction_type || $transaction_type === 'contract') {
            $contracts = Contract::with('customer')
                ->when($start_date, fn($q) => $q->whereDate('created_at', '>=', $start_date))
                ->when($end_date, fn($q) => $q->whereDate('created_at', '<=', $end_date))
                ->get()
                ->map(function ($c) {
                    return [
                        'id' => $c->id,
                        'type' => 'contract',
                        'transaction_type' => 'صك عميل',
                        'date' => $c->created_at,
                        'description' => 'صك للعميل - ' . $c->customer?->name ?? '—',
                        'reference' => $c->contract_number,
                        'reference_url' => route('udhiya.contracts.show', $c),
                        'debit' => $c->total_amount,
                        'credit' => 0,
                        'wallet_name' => '—',
                        'wallet_id' => null,
                        'notes' => $c->notes ?? '',
                    ];
                });
            $allTransactions = $allTransactions->concat($contracts);
        }

        // Sort by date descending, paginate
        $allTransactions = $allTransactions->sortByDesc('date')->values();
        $perPage = 50;
        $page = request()->get('page', 1);
        $total = count($allTransactions);
        $paginatedTransactions = new \Illuminate\Pagination\LengthAwarePaginator(
            $allTransactions->forPage($page, $perPage),
            $total,
            $perPage,
            $page,
            ['path' => route('udhiya.accounts'), 'query' => $request->query()]
        );

        $wallets = Wallet::where('is_active', true)->get();

        // Calculate totals
        $totalDebits = $allTransactions->sum('debit');
        $totalCredits = $allTransactions->sum('credit');
        $netAmount = $totalDebits - $totalCredits;

        return view('udhiya.advances.accounts', compact(
            'paginatedTransactions',
            'wallets',
            'transaction_type',
            'wallet_id',
            'start_date',
            'end_date',
            'totalDebits',
            'totalCredits',
            'netAmount'
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
