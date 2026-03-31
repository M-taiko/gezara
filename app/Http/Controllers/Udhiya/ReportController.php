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
        $animals = Animal::with('product.mainCategory', 'warehouse', 'shareSetting', 'expenses', 'contractItems')
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
            $linkedExpenses = $animal->expenses->sum('amount');
            $revenue        = $animal->contractItems->sum('total_price');
            $profit         = $revenue - $purchaseCost - $linkedExpenses;
            return compact('purchaseCost', 'linkedExpenses', 'revenue', 'profit');
        });

        return view('udhiya.reports.animals', compact('animals', 'summary', 'animalStats'));
    }

    public function profit(Request $request)
    {
        $contracts = Contract::with('customer', 'items.animal')
            ->whereIn('status', ['active', 'completed'])
            ->get();

        $totalRevenue   = $contracts->sum('total_amount');
        $totalCost      = $contracts->sum(fn($c) => $c->items->sum(fn($i) => $i->animal->cost));
        $totalCollected = $contracts->sum('paid_amount');

        // Expenses
        $expenses           = Expense::with('animal.product')->orderByDesc('date')->get();
        $totalExpenses      = $expenses->sum('amount');
        $expensesByCategory = $expenses->groupBy('category')->map->sum('amount');

        // Per-animal cost: purchase cost + linked expenses
        $animals = Animal::with('product.mainCategory', 'shareSetting', 'expenses')->get();
        $animalStats = $animals->map(function ($animal) use ($contracts) {
            $purchaseCost    = $animal->cost ?? 0;
            $linkedExpenses  = $animal->expenses->sum('amount');
            $totalCost       = $purchaseCost + $linkedExpenses;

            // Revenue from contracts for this animal
            $revenue = $contracts->flatMap->items
                ->where('animal_id', $animal->id)
                ->sum('total_price');

            return [
                'animal'          => $animal,
                'purchase_cost'   => $purchaseCost,
                'linked_expenses' => $linkedExpenses,
                'total_cost'      => $totalCost,
                'revenue'         => $revenue,
                'profit'          => $revenue - $totalCost,
            ];
        })->filter(fn($s) => $s['revenue'] > 0 || $s['linked_expenses'] > 0);

        $totalProfit = $totalRevenue - $totalCost - $totalExpenses;

        return view('udhiya.reports.profit', compact(
            'contracts', 'totalRevenue', 'totalCost', 'totalProfit',
            'totalCollected', 'expenses', 'totalExpenses', 'expensesByCategory',
            'animalStats'
        ));
    }

    public function slaughter(Request $request)
    {
        $filter = $request->input('filter', 'all'); // all | slaughtered | pending

        $groups = \App\Models\SlaughterGroup::with([
            'animal.product.mainCategory',
            'animal.shareSetting',
            'members.customer',
            'members.contractItem.contract.payments',
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
        $supplier->load('purchases.items.product');
        $totalPurchases = $supplier->purchases->sum('total');
        $totalPaid      = $supplier->purchases->sum('paid');
        $balance        = $supplier->balance;

        return view('udhiya.reports.supplier', compact('supplier', 'totalPurchases', 'totalPaid', 'balance'));
    }
}
