<?php

namespace App\Http\Controllers\Udhiya;

use App\Http\Controllers\Controller;
use App\Http\Requests\Udhiya\StoreContractRequest;
use App\Models\Animal;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\SlaughterGroup;
use App\Services\Udhiya\ContractService;

class ContractController extends Controller
{
    public function __construct(private ContractService $service) {}

    public function index(\Illuminate\Http\Request $request)
    {
        $search = $request->input('search');
        $contracts = Contract::with('customer', 'items.animal.product')
            ->when($search, function ($q) use ($search) {
                $q->whereHas('customer', function ($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%")
                       ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();
        return view('udhiya.contracts.index', compact('contracts', 'search'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $animals   = Animal::with('product.mainCategory', 'shareSetting')
            ->whereIn('status', ['available', 'partially_allocated'])
            ->get();
        $groupsJson = SlaughterGroup::with('members.customer', 'animal')->get()->map(function ($g) {
            $priceField    = 'price_' . $g->share_type;
            $pricePerShare = $g->animal ? (float) ($g->animal->$priceField ?? 0) : 0;
            return [
                'id'              => $g->id,
                'name'            => $g->name,
                'animal_id'       => $g->animal_id,
                'animal_code'     => $g->animal?->code,
                'share_type'      => $g->share_type,
                'total'           => $g->totalSlots(),
                'used'            => $g->usedSlots(),
                'remaining'       => $g->remainingSlots(),
                'price_per_share' => $pricePerShare,
                'members'         => $g->members->map(fn ($m) => [
                    'customer_id'    => $m->customer_id,
                    'customer_name'  => $m->customer?->name ?? '',
                    'customer_phone' => $m->customer?->phone ?? '',
                    'shares_count'   => $m->shares_count,
                    'has_contract'   => (bool) $m->contract_item_id,
                ])->values()->toArray(),
            ];
        })->values()->toArray();

        return view('udhiya.contracts.create', compact('customers', 'animals', 'groupsJson'));
    }

    public function store(StoreContractRequest $request)
    {
        try {
            $contract   = $this->service->store($request->validated());
            $printAfter = $request->input('print_after') === '1';

            if ($printAfter) {
                return redirect()->route('udhiya.contracts.print', $contract)
                    ->with('toast_success', 'تم إنشاء الصك #' . $contract->contract_number . ' — جاهز للطباعة.');
            }

            return redirect()->route('udhiya.contracts.show', $contract)
                ->with('toast_success', 'تم إنشاء الصك #' . $contract->contract_number . ' بنجاح.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('toast_error', $e->getMessage());
        }
    }

    public function show(Contract $contract)
    {
        $contract->load('customer', 'items.animal.product.mainCategory', 'payments');
        return view('udhiya.contracts.show', compact('contract'));
    }

    public function destroy(Contract $contract)
    {
        try {
            $this->service->cancel($contract);
            return redirect()->route('udhiya.contracts.index')
                ->with('toast_success', 'تم إلغاء الصك.');
        } catch (\Throwable $e) {
            return back()->with('toast_error', $e->getMessage());
        }
    }

    public function printView(Contract $contract)
    {
        $contract->load('customer', 'items.animal.product.mainCategory', 'payments');
        return view('udhiya.print.contract', compact('contract'));
    }
}
