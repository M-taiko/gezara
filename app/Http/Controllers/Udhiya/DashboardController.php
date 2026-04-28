<?php

namespace App\Http\Controllers\Udhiya;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use App\Models\Contract;
use App\Models\ContractRequest;
use App\Models\Customer;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Treasury;
use App\Models\Wallet;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'animals_total'     => Animal::count(),
            'animals_available' => Animal::where('status', 'available')->count(),
            'animals_allocated' => Animal::whereIn('status', ['partially_allocated', 'fully_allocated'])->count(),
            'contracts_active'  => Contract::where('status', 'active')->count(),
            'contracts_total'   => Contract::whereIn('status', ['active', 'completed'])->count(),
            'customers_total'   => Customer::count(),
            'suppliers_total'   => Supplier::count(),
            'purchases_total'   => Purchase::count(),
            'revenue_total'     => Contract::whereIn('status', ['active', 'completed'])->sum('total_amount'),
            'collected_total'   => Contract::whereIn('status', ['active', 'completed'])->sum('paid_amount'),
            'remaining_total'   => Contract::whereIn('status', ['active', 'completed'])->sum('remaining_amount'),
            'treasury_balance'  => Wallet::sum('balance') ?: (Treasury::where('type', 'in')->sum('amount') - Treasury::where('type', 'out')->sum('amount')),
        ];

        $recentContracts = Contract::with('customer')
            ->whereIn('status', ['active', 'completed'])
            ->latest()
            ->take(5)
            ->get();

        $recentPurchases = Purchase::with('supplier')
            ->latest()
            ->take(5)
            ->get();

        // الحيوانات المتاحة (مجمعة وغير مجمعة)
        $availableAnimals = Animal::where('status', 'available')
            ->with(['product', 'shareSetting'])
            ->get();

        // طلبات الاشتراك المعلقة
        $pendingRequests = ContractRequest::where('status', 'pending')
            ->with('animal.product')
            ->latest()
            ->get();

        return view('udhiya.dashboard.index', compact(
            'stats', 'recentContracts', 'recentPurchases',
            'availableAnimals', 'pendingRequests'
        ));
    }
}
