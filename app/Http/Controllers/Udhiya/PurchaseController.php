<?php

namespace App\Http\Controllers\Udhiya;

use App\Http\Controllers\Controller;
use App\Http\Requests\Udhiya\StorePurchaseRequest;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Services\Udhiya\PurchaseService;

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

    public function show(Purchase $purchase)
    {
        $purchase->load('supplier', 'items.product.mainCategory', 'animals');
        return view('udhiya.purchases.show', compact('purchase'));
    }
}
