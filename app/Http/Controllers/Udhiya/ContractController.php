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

    public function index()
    {
        $contracts = Contract::with('customer')->latest()->paginate(15);
        return view('udhiya.contracts.index', compact('contracts'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $animals   = Animal::with('product.mainCategory', 'shareSetting')
            ->whereIn('status', ['available', 'partially_allocated'])
            ->get();
        $groupsJson = SlaughterGroup::with('members')->get()->map(fn($g) => [
            'id'         => $g->id,
            'name'       => $g->name,
            'animal_id'  => $g->animal_id,
            'share_type' => $g->share_type,
            'total'      => $g->totalSlots(),
            'used'       => $g->usedSlots(),
            'remaining'  => $g->remainingSlots(),
        ])->values()->toArray();

        return view('udhiya.contracts.create', compact('customers', 'animals', 'groupsJson'));
    }

    public function store(StoreContractRequest $request)
    {
        try {
            $contract = $this->service->store($request->validated());
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
