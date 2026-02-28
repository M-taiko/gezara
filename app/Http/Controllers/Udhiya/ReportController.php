<?php

namespace App\Http\Controllers\Udhiya;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use App\Models\Contract;
use App\Models\Customer;
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
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->get();

        $summary = [
            'total'             => $animals->count(),
            'available'         => $animals->where('status', 'available')->count(),
            'partially'         => $animals->where('status', 'partially_allocated')->count(),
            'fully'             => $animals->where('status', 'fully_allocated')->count(),
            'slaughtered'       => $animals->where('status', 'slaughtered')->count(),
        ];

        return view('udhiya.reports.animals', compact('animals', 'summary'));
    }

    public function profit(Request $request)
    {
        $contracts = Contract::with('customer', 'items.animal')
            ->whereIn('status', ['active', 'completed'])
            ->get();

        $totalRevenue  = $contracts->sum('total_amount');
        $totalCost     = $contracts->sum(fn($c) => $c->items->sum(fn($i) => $i->animal->cost));
        $totalProfit   = $totalRevenue - $totalCost;
        $totalCollected = $contracts->sum('paid_amount');

        return view('udhiya.reports.profit', compact('contracts', 'totalRevenue', 'totalCost', 'totalProfit', 'totalCollected'));
    }

    public function slaughter(Request $request)
    {
        $contracts = Contract::with('customer', 'items.animal.product.mainCategory')
            ->whereNotNull('slaughter_day')
            ->orderBy('slaughter_day')
            ->orderBy('slaughter_order')
            ->get()
            ->groupBy('slaughter_day');

        return view('udhiya.reports.slaughter', compact('contracts'));
    }

    public function customer(Customer $customer)
    {
        $customer->load('contracts.items.animal', 'contracts.payments');
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
