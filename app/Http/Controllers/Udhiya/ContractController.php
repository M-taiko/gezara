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
        $filterType = $request->input('filter_type');
        $filterShare = $request->input('filter_share');

        $query = Contract::with('customer', 'items.animal.product', 'items.group')
            ->when($search, function ($q) use ($search) {
                $q->whereHas('customer', function ($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%")
                       ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($filterType === 'standalone', function ($q) {
                $q->whereHas('items', function ($q2) {
                    $q2->whereNull('group_id');
                });
            })
            ->when($filterType === 'grouped', function ($q) {
                $q->whereHas('items', function ($q2) {
                    $q2->whereNotNull('group_id');
                });
            })
            ->when($filterShare, function ($q) use ($filterShare) {
                $q->whereHas('items', function ($q2) use ($filterShare) {
                    $q2->where('share_type', $filterShare);
                });
            });

        // Calculate summary stats from all contracts (not paginated)
        $summary = [
            'total_contracts' => (clone $query)->count(),
            'total_amount' => (clone $query)->sum('total_amount'),
            'total_paid' => (clone $query)->sum('paid_amount'),
            'total_remaining' => (clone $query)->sum('remaining_amount'),
            'active_contracts' => (clone $query)->where('status', 'active')->count(),
            'completed_contracts' => (clone $query)->where('status', 'completed')->count(),
        ];

        $contracts = $query->latest()
            ->paginate(20)
            ->withQueryString();

        return view('udhiya.contracts.index', compact('contracts', 'search', 'filterType', 'filterShare', 'summary'));
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

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم إنشاء الصك بنجاح',
                    'contract' => [
                        'id' => $contract->id,
                        'contract_number' => $contract->contract_number,
                        'total_amount' => $contract->total_amount,
                        'paid_amount' => $contract->paid_amount,
                        'remaining_amount' => $contract->remaining_amount,
                    ]
                ]);
            }

            if ($printAfter) {
                return redirect()->route('udhiya.contracts.print', $contract)
                    ->with('toast_success', 'تم إنشاء الصك #' . $contract->contract_number . ' — جاهز للطباعة.');
            }

            return redirect()->route('udhiya.contracts.show', $contract)
                ->with('toast_success', 'تم إنشاء الصك #' . $contract->contract_number . ' بنجاح.');
        } catch (\Throwable $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }
            return back()->withInput()->with('toast_error', $e->getMessage());
        }
    }

    public function storeQuick(\Illuminate\Http\Request $request)
    {
        try {
            $validated = $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'total_amount' => 'required|numeric|min:0.01',
                'share_type' => 'required|in:full,seven,six,five,quarter,third,half',
                'notes' => 'nullable|string',
            ]);

            // Create a quick contract with a single item (no animal)
            $contractData = [
                'customer_id' => $validated['customer_id'],
                'notes' => $validated['notes'] ?? null,
                'items' => [
                    [
                        'animal_id' => null,
                        'unit_price' => (float) $validated['total_amount'],
                        'share_type' => $validated['share_type'],
                        'shares_count' => 1,
                        'group_id' => null,
                    ]
                ]
            ];

            $contract = $this->service->store($contractData);

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء الصك بنجاح',
                'contract' => [
                    'id' => $contract->id,
                    'contract_number' => $contract->contract_number,
                    'total_amount' => $contract->total_amount,
                    'paid_amount' => $contract->paid_amount,
                    'remaining_amount' => $contract->remaining_amount,
                ]
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errorMessages = [];
            foreach ($e->errors() as $field => $messages) {
                $errorMessages[] = implode(' ', $messages);
            }
            return response()->json([
                'success' => false,
                'message' => implode(' | ', $errorMessages) ?: 'خطأ في البيانات المدخلة',
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $e) {
            \Log::error('Contract storeQuick error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'خطأ في إنشاء الصك'
            ], 400);
        }
    }

    public function show(Contract $contract)
    {
        $contract->load('customer', 'items.animal.product.mainCategory', 'payments.wallet');
        $wallets = \App\Models\Wallet::where('is_active', true)->orderBy('name')->get();
        return view('udhiya.contracts.show', compact('contract', 'wallets'));
    }

    public function edit(Contract $contract)
    {
        $contract->load('customer', 'items.animal.product.mainCategory', 'items.animal.shareSetting', 'payments');

        $customers = Customer::orderBy('name')->get();
        // Get animals that are available, partially allocated, OR already part of this contract
        $contractAnimalIds = $contract->items->pluck('animal_id')->filter()->toArray();
        $animals   = Animal::with('product.mainCategory', 'shareSetting')
            ->whereIn('status', ['available', 'partially_allocated'])
            ->orWhereIn('id', $contractAnimalIds)
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

        return view('udhiya.contracts.edit', compact('contract', 'customers', 'animals', 'groupsJson'));
    }

    public function update(StoreContractRequest $request, Contract $contract)
    {
        try {
            $this->service->update($contract, $request->validated());

            return redirect()->route('udhiya.contracts.show', $contract)
                ->with('toast_success', 'تم تحديث بيانات الصك بنجاح.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('toast_error', $e->getMessage());
        }
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
