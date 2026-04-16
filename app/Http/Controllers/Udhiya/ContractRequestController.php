<?php

namespace App\Http\Controllers\Udhiya;

use App\Http\Controllers\Controller;
use App\Models\ContractRequest;
use Illuminate\Http\Request;

class ContractRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $requests = ContractRequest::with('animal.product')
            ->latest()
            ->paginate(15);

        return view('udhiya.contract-requests.index', compact('requests'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'animal_id' => 'required|exists:animals,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email',
            'share_type' => 'required|string',
            'share_price' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        ContractRequest::create($validated);

        return back()->with('toast_success', '✅ تم استلام طلب الاشتراك بنجاح، سيتم التواصل معك قريباً');
    }

    /**
     * Update the status of contract request
     */
    public function updateStatus(Request $request, ContractRequest $contractRequest)
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $contractRequest->update($validated);

        $message = $validated['status'] === 'approved' ? '✅ تم قبول الطلب' : '❌ تم رفض الطلب';
        return back()->with('toast_success', $message);
    }

    /**
     * Convert contract request to actual contract
     */
    public function convertToContract(Request $request, ContractRequest $contractRequest)
    {
        $validated = $request->validate([
            'animal_id' => 'required|exists:animals,id',
            'group_id' => 'nullable|exists:slaughter_groups,id',
        ]);

        try {
            // Create or get customer
            $customer = \App\Models\Customer::firstOrCreate(
                ['phone' => $contractRequest->customer_phone],
                [
                    'name' => $contractRequest->customer_name,
                    'email' => $contractRequest->customer_email ?? null,
                    'address' => '',
                ]
            );

            // Create contract using ContractService
            $contractService = app(\App\Services\Udhiya\ContractService::class);

            $contractData = [
                'customer_id' => $customer->id,
                'items' => [
                    [
                        'animal_id' => $validated['animal_id'],
                        'share_type' => $contractRequest->share_type,
                        'shares_count' => 1,
                        'unit_price' => $contractRequest->share_price ?? 0,
                        'group_id' => $validated['group_id'] ?? null,
                    ]
                ],
                'payment_amount' => 0,
                'payment_method' => 'cash',
            ];

            $contract = $contractService->store($contractData);

            // Update request status
            $contractRequest->update([
                'status' => 'converted',
                'animal_id' => $validated['animal_id'],
            ]);

            return back()->with('toast_success', '✅ تم تحويل الطلب إلى صك بنجاح - #' . $contract->code);
        } catch (\Exception $e) {
            return back()->with('toast_error', '❌ خطأ: ' . $e->getMessage());
        }
    }
}
