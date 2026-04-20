<?php

namespace App\Http\Controllers\Udhiya;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Treasury;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        return view('udhiya.reports.index');
    }

    public function animals(Request $request)
    {
        $animals = Animal::with('product.mainCategory', 'warehouse', 'shareSetting')
            ->withSum('expenses as linked_expenses', 'amount')
            ->withSum('contractItems as revenue', 'total_price')
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->get();

        $summary = [
            'total'       => $animals->count(),
            'available'   => $animals->where('status', 'available')->count(),
            'partially'   => $animals->where('status', 'partially_allocated')->count(),
            'fully'       => $animals->where('status', 'fully_allocated')->count(),
            'slaughtered' => $animals->where('status', 'slaughtered')->count(),
        ];

        $animalStats = $animals->keyBy('id')->map(function ($animal) {
            $purchaseCost   = $animal->cost ?? 0;
            $linkedExpenses = $animal->linked_expenses ?? 0;
            $revenue        = $animal->revenue ?? 0;
            $profit         = $revenue - $purchaseCost - $linkedExpenses;
            return compact('purchaseCost', 'linkedExpenses', 'revenue', 'profit');
        });

        return view('udhiya.reports.animals', compact('animals', 'summary', 'animalStats'));
    }

    public function profit(Request $request)
    {
        $contracts = Contract::with('customer')
            ->select('id', 'contract_number', 'customer_id', 'total_amount', 'paid_amount', 'remaining_amount', 'status')
            ->whereIn('status', ['active', 'completed'])
            ->get();

        // Get cost per contract via single JOIN query instead of PHP loop
        $contractCosts = \App\Models\ContractItem::join('animals', 'contract_items.animal_id', '=', 'animals.id')
            ->selectRaw('contract_items.contract_id, SUM(animals.cost) as total_cost')
            ->groupBy('contract_items.contract_id')
            ->pluck('total_cost', 'contract_id');

        $totalRevenue   = $contracts->sum('total_amount');
        $totalCost      = $contractCosts->sum();
        $totalCollected = $contracts->sum('paid_amount');

        // Expenses
        $expenses           = Expense::with('animal.product')->orderByDesc('date')->get();
        $totalExpenses      = $expenses->sum('amount');
        $expensesByCategory = $expenses->groupBy('category')->map->sum('amount');

        // Per-animal stats via DB aggregation instead of loading all relationships
        $animalStats = Animal::with('product.mainCategory')
            ->withSum('expenses as linked_expenses', 'amount')
            ->withSum('contractItems as revenue', 'total_price')
            ->get()
            ->filter(fn($a) => ($a->revenue ?? 0) > 0 || ($a->linked_expenses ?? 0) > 0)
            ->map(function ($animal) {
                $purchaseCost    = $animal->cost ?? 0;
                $linkedExpenses  = $animal->linked_expenses ?? 0;
                $totalCost       = $purchaseCost + $linkedExpenses;
                $revenue         = $animal->revenue ?? 0;

                return [
                    'animal'          => $animal,
                    'purchase_cost'   => $purchaseCost,
                    'linked_expenses' => $linkedExpenses,
                    'total_cost'      => $totalCost,
                    'revenue'         => $revenue,
                    'profit'          => $revenue - $totalCost,
                ];
            });

        $totalProfit = $totalRevenue - $totalCost - $totalExpenses;

        return view('udhiya.reports.profit', compact(
            'contracts', 'contractCosts', 'totalRevenue', 'totalCost', 'totalProfit',
            'totalCollected', 'expenses', 'totalExpenses', 'expensesByCategory',
            'animalStats'
        ));
    }

    public function slaughter(Request $request)
    {
        $filter = $request->input('filter', 'all'); // all | slaughtered | pending

        $groups = \App\Models\SlaughterGroup::with([
            'animal:id,code,status,slaughtered_at,product_id',
            'animal.product:id,name,main_category_id',
            'animal.product.mainCategory:id,name,code',
            'members.customer:id,name,phone',
            'members.contractItem:id,delivered_at,contract_id',
            'members.contractItem.contract:id,paid_amount,remaining_amount',
        ])
        ->whereHas('animal')
        ->when($filter === 'slaughtered', fn($q) => $q->whereHas('animal', fn($q2) => $q2->where('status', 'slaughtered')))
        ->when($filter === 'pending',     fn($q) => $q->whereHas('animal', fn($q2) => $q2->where('status', '!=', 'slaughtered')))
        ->orderByDesc(
            \App\Models\Animal::select('slaughtered_at')
                ->whereColumn('animals.id', 'slaughter_groups.animal_id')
                ->limit(1)
        )
        ->get();

        $summary = [
            'total'      => $groups->count(),
            'slaughtered'=> $groups->filter(fn($g) => $g->animal?->status === 'slaughtered')->count(),
            'pending'    => $groups->filter(fn($g) => $g->animal?->status !== 'slaughtered')->count(),
            'delivered'  => $groups->flatMap->members->filter(fn($m) => $m->contractItem?->delivered_at)->count(),
            'not_delivered' => $groups->flatMap->members->filter(fn($m) => $m->contractItem && !$m->contractItem->delivered_at)->count(),
        ];

        return view('udhiya.reports.slaughter', compact('groups', 'summary', 'filter'));
    }

    public function customer(Customer $customer)
    {
        $customer->load(
            'contracts.items.animal.product.mainCategory',
            'contracts.payments',
            'groupMembers.group.animal.product.mainCategory'
        );
        $totalAmount    = $customer->contracts->sum('total_amount');
        $paidAmount     = $customer->contracts->sum('paid_amount');
        $remainingAmount = $customer->contracts->sum('remaining_amount');

        return view('udhiya.reports.customer', compact('customer', 'totalAmount', 'paidAmount', 'remainingAmount'));
    }

    public function supplier(Supplier $supplier)
    {
        $supplier->load([
            'purchases.items.product',
            'payments' => fn($q) => $q->orderBy('paid_at')->orderBy('id'),
            'payments.purchase',
        ]);
        $totalPurchases = $supplier->purchases->sum('total');
        $totalPaid      = $supplier->purchases->sum('paid');
        $balance        = $supplier->balance;

        return view('udhiya.reports.supplier', compact('supplier', 'totalPurchases', 'totalPaid', 'balance'));
    }
}
