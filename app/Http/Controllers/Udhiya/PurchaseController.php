<?php

namespace App\Http\Controllers\Udhiya;

use App\Http\Controllers\Controller;
use App\Http\Requests\Udhiya\StorePurchaseRequest;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\Services\Udhiya\PurchaseService;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function __construct(private PurchaseService $service) {}

    public function index()
    {
        $purchases = Purchase::with('supplier')->latest()->paginate(15);
        return view('udhiya.purchases.index', compact('purchases'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $products  = Product::with('mainCategory')->where('is_active', true)->get();
        return view('udhiya.purchases.create', compact('suppliers', 'products'));
    }

    public function store(StorePurchaseRequest $request)
    {
        try {
            $purchase = $this->service->store($request->validated());
            return redirect()->route('udhiya.purchases.show', $purchase)
                ->with('toast_success', 'تم تسجيل المشترى بنجاح وإنشاء الحيوانات.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('toast_error', $e->getMessage());
        }
    }

    public function edit(Purchase $purchase)
    {
        $purchase->load('supplier', 'items.product.mainCategory');
        $suppliers = Supplier::orderBy('name')->get();
        $products  = Product::with('mainCategory')->where('is_active', true)->get();
        return view('udhiya.purchases.edit', compact('purchase', 'suppliers', 'products'));
    }

    public function update(Request $request, Purchase $purchase)
    {
        $data = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'date'        => 'required|date',
            'notes'       => 'nullable|string',
            'items'       => 'required|array|min:1',
            'items.*.id'            => 'nullable|exists:purchase_items,id',
            'items.*.product_id'    => 'required|exists:products,id',
            'items.*.quantity'      => 'required|integer|min:1',
            'items.*.weight'        => 'nullable|numeric|min:0',
            'items.*.cost_per_unit' => 'required|numeric|min:0',
            'items.*.total'         => 'required|numeric|min:0',
            'items.*.price_full'    => 'nullable|numeric|min:0',
            'items.*.price_half'    => 'nullable|numeric|min:0',
            'items.*.price_third'   => 'nullable|numeric|min:0',
            'items.*.price_quarter' => 'nullable|numeric|min:0',
            'items.*.price_five'    => 'nullable|numeric|min:0',
            'items.*.price_six'     => 'nullable|numeric|min:0',
            'items.*.price_seven'   => 'nullable|numeric|min:0',
        ]);

        $newTotal = collect($data['items'])->sum('total');

        // Update supplier balance: remove old remaining, add new remaining
        $oldRemaining = $purchase->total - $purchase->paid;
        $newRemaining = $newTotal - $purchase->paid;
        $balanceDiff  = $newRemaining - $oldRemaining;

        $purchase->supplier->increment('balance', $balanceDiff);

        // Update purchase basic fields + total
        $purchase->update([
            'supplier_id' => $data['supplier_id'],
            'date'        => $data['date'],
            'notes'       => $data['notes'] ?? null,
            'total'       => $newTotal,
        ]);

        // Update items
        $existingIds = $purchase->items->pluck('id')->toArray();
        $updatedIds  = [];

        foreach ($data['items'] as $item) {
            $itemData = [
                'product_id'    => $item['product_id'],
                'quantity'      => $item['quantity'],
                'weight'        => $item['weight'] ?? null,
                'cost_per_unit' => $item['cost_per_unit'],
                'total'         => $item['total'],
            ];

            // Add share prices if provided
            foreach (['price_full', 'price_half', 'price_third', 'price_quarter', 'price_five', 'price_six', 'price_seven'] as $priceField) {
                if (isset($item[$priceField])) {
                    $itemData[$priceField] = $item[$priceField];
                }
            }

            if (!empty($item['id'])) {
                $purchase->items()->where('id', $item['id'])->update($itemData);
                $updatedIds[] = $item['id'];
            } else {
                $new = $purchase->items()->create($itemData);
                $updatedIds[] = $new->id;
            }
        }

        // Delete removed items
        $toDelete = array_diff($existingIds, $updatedIds);
        if ($toDelete) {
            $purchase->items()->whereIn('id', $toDelete)->delete();
        }

        return redirect()->route('udhiya.purchases.show', $purchase)
            ->with('toast_success', 'تم تحديث الفاتورة بنجاح');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load([
            'supplier',
            'items.product.mainCategory',
            'animals.product.mainCategory',
            'animals.warehouse',
            'supplierPayments' => fn($q) => $q->orderBy('paid_at')->orderBy('id'),
        ]);
        return view('udhiya.purchases.show', compact('purchase'));
    }
}
