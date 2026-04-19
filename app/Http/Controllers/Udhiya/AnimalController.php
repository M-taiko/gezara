<?php

namespace App\Http\Controllers\Udhiya;

use App\Http\Controllers\Controller;
use App\Http\Requests\Udhiya\TransferAnimalRequest;
use App\Models\Animal;
use App\Models\MainCategory;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Services\Udhiya\AnimalService;
use Illuminate\Http\Request;

class AnimalController extends Controller
{
    public function __construct(private AnimalService $service) {}

    public function index(Request $request)
    {
        $query = Animal::with('product.mainCategory', 'warehouse', 'shareSetting');

        if ($request->filled('category')) {
            $query->whereHas('product.mainCategory', fn($q) => $q->where('id', $request->category));
        }
        if ($request->filled('warehouse')) {
            $query->where('warehouse_id', $request->warehouse);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where('code', 'like', '%' . $request->search . '%');
        }

        $animals        = $query->latest()->paginate(20)->withQueryString();
        $categories     = MainCategory::all();
        $warehouses     = Warehouse::all();
        $products       = Product::with('mainCategory')->where('is_active', true)->orderBy('name')->get();
        $allProducts    = Product::with('mainCategory')->withCount('animals')->orderBy('main_category_id')->orderBy('name')->get();
        $suppliers      = Supplier::orderBy('name')->get();
        $mainCategories = MainCategory::all();

        return view('udhiya.animals.index', compact('animals', 'categories', 'warehouses', 'products', 'allProducts', 'suppliers', 'mainCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code'         => 'required|string|max:50|unique:animals,code',
            'product_id'   => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'supplier_id'  => 'nullable|exists:suppliers,id',
            'weight'       => 'nullable|numeric|min:0',
            'cost'         => 'nullable|numeric|min:0',
            'price_per_kg' => 'nullable|numeric|min:0',
            'price_full'   => 'nullable|numeric|min:0',
            'price_seven'  => 'nullable|numeric|min:0',
            'price_six'    => 'nullable|numeric|min:0',
            'price_five'   => 'nullable|numeric|min:0',
            'price_quarter'=> 'nullable|numeric|min:0',
            'price_third'  => 'nullable|numeric|min:0',
            'price_half'   => 'nullable|numeric|min:0',
            'notes'        => 'nullable|string',
        ], [
            'code.required'         => 'كود الحيوان مطلوب.',
            'code.unique'           => 'هذا الكود مستخدم بالفعل.',
            'product_id.required'   => 'يجب اختيار نوع الحيوان.',
            'warehouse_id.required' => 'يجب اختيار المخزن.',
        ]);

        Animal::create([
            'code'         => $request->code,
            'product_id'   => $request->product_id,
            'warehouse_id' => $request->warehouse_id,
            'supplier_id'  => $request->supplier_id ?: null,
            'weight'       => $request->weight ?: null,
            'price_per_kg' => $request->price_per_kg ?: null,
            'cost'         => $request->cost ?? 0,
            'price_full'   => $request->price_full ?: null,
            'price_seven'  => $request->price_seven ?: null,
            'price_six'    => $request->price_six ?: null,
            'price_five'   => $request->price_five ?: null,
            'price_quarter'=> $request->price_quarter ?: null,
            'price_third'  => $request->price_third ?: null,
            'price_half'   => $request->price_half ?: null,
            'notes'        => $request->notes,
            'status'       => 'available',
        ]);

        return back()->with('toast_success', 'تم إضافة الحيوان بنجاح.');
    }

    public function byWarehouse(Request $request)
    {
        $statusFilter = $request->input('status');

        $warehouses = Warehouse::with([
            'animals' => function ($q) use ($statusFilter) {
                $q->with('product.mainCategory', 'shareSetting')
                  ->when($statusFilter, fn($q2) => $q2->where('status', $statusFilter));
            }
        ])->get();

        $summary = [
            'total'      => Animal::count(),
            'available'  => Animal::where('status', 'available')->count(),
            'partially'  => Animal::where('status', 'partially_allocated')->count(),
            'fully'      => Animal::where('status', 'fully_allocated')->count(),
            'slaughtered'=> Animal::where('status', 'slaughtered')->count(),
        ];

        return view('udhiya.animals.by-warehouse', compact('warehouses', 'summary', 'statusFilter'));
    }

    public function show(Animal $animal)
    {
        $animal->load(
            'product.mainCategory',
            'supplier',
            'purchase',
            'warehouse',
            'shareSetting',
            'contractItems.contract.customer',
            'contractItems.contract.payments',
            'transfers.fromWarehouse',
            'transfers.toWarehouse',
            'transfers.transferredBy',
            'expenses'
        );
        $warehouses = Warehouse::whereNot('id', $animal->warehouse_id)->get();
        return view('udhiya.animals.show', compact('animal', 'warehouses'));
    }

    public function setGrouped(Request $request, Animal $animal)
    {
        $request->validate([
            'share_type' => 'required|in:seven,six,five,quarter,third,half',
        ]);

        try {
            $this->service->setGrouped($animal, $request->share_type);
            return back()->with('toast_success', 'تم تفعيل نظام الأنصبة للحيوان.');
        } catch (\Throwable $e) {
            return back()->with('toast_error', $e->getMessage());
        }
    }

    public function unsetGrouped(Animal $animal)
    {
        try {
            $this->service->unsetGrouped($animal);
            return back()->with('toast_success', 'تم إلغاء نظام الأنصبة.');
        } catch (\Throwable $e) {
            return back()->with('toast_error', $e->getMessage());
        }
    }

    public function transfer(TransferAnimalRequest $request, Animal $animal)
    {
        try {
            $this->service->transfer($animal, $request->to_warehouse_id, $request->notes);
            return back()->with('toast_success', 'تم نقل الحيوان بنجاح.');
        } catch (\Throwable $e) {
            return back()->with('toast_error', $e->getMessage());
        }
    }

    public function updatePrices(Request $request, Animal $animal)
    {
        $request->validate([
            'price_per_kg'  => 'nullable|numeric|min:0',
            'price_full'    => 'nullable|numeric|min:0',
            'price_seven'   => 'nullable|numeric|min:0',
            'price_six'     => 'nullable|numeric|min:0',
            'price_five'    => 'nullable|numeric|min:0',
            'price_quarter' => 'nullable|numeric|min:0',
            'price_third'   => 'nullable|numeric|min:0',
            'price_half'    => 'nullable|numeric|min:0',
        ]);

        $animal->update($request->only([
            'price_per_kg', 'price_full', 'price_seven', 'price_six',
            'price_five', 'price_quarter', 'price_third', 'price_half',
        ]));
        return back()->with('toast_success', 'تم تحديث أسعار الحيوان.');
    }

    public function updateCode(Request $request, Animal $animal)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:animals,code,' . $animal->id,
        ], [
            'code.required' => 'كود الحيوان مطلوب.',
            'code.unique'   => 'هذا الكود مستخدم بالفعل.',
        ]);

        $animal->update(['code' => $request->code]);
        return back()->with('toast_success', 'تم تحديث كود الحيوان.');
    }

    public function edit(Animal $animal)
    {
        $animal->load('product.mainCategory', 'warehouse', 'supplier');
        $products = Product::with('mainCategory')->where('is_active', true)->orderBy('name')->get();
        $warehouses = Warehouse::all();
        $suppliers = Supplier::orderBy('name')->get();

        return view('udhiya.animals.edit', compact('animal', 'products', 'warehouses', 'suppliers'));
    }

    public function update(Request $request, Animal $animal)
    {
        $request->validate([
            'code'         => 'required|string|max:50|unique:animals,code,' . $animal->id,
            'product_id'   => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'supplier_id'  => 'nullable|exists:suppliers,id',
            'weight'       => 'nullable|numeric|min:0',
            'cost'         => 'nullable|numeric|min:0',
            'price_per_kg' => 'nullable|numeric|min:0',
            'price_full'   => 'nullable|numeric|min:0',
            'price_seven'  => 'nullable|numeric|min:0',
            'price_six'    => 'nullable|numeric|min:0',
            'price_five'   => 'nullable|numeric|min:0',
            'price_quarter'=> 'nullable|numeric|min:0',
            'price_third'  => 'nullable|numeric|min:0',
            'price_half'   => 'nullable|numeric|min:0',
            'notes'        => 'nullable|string',
        ], [
            'code.required'         => 'كود الحيوان مطلوب.',
            'code.unique'           => 'هذا الكود مستخدم بالفعل.',
            'product_id.required'   => 'يجب اختيار نوع الحيوان.',
            'warehouse_id.required' => 'يجب اختيار المخزن.',
        ]);

        $animal->update([
            'code'         => $request->code,
            'product_id'   => $request->product_id,
            'warehouse_id' => $request->warehouse_id,
            'supplier_id'  => $request->supplier_id ?: null,
            'weight'       => $request->weight ?: null,
            'price_per_kg' => $request->price_per_kg ?: null,
            'cost'         => $request->cost ?? 0,
            'price_full'   => $request->price_full ?: null,
            'price_seven'  => $request->price_seven ?: null,
            'price_six'    => $request->price_six ?: null,
            'price_five'   => $request->price_five ?: null,
            'price_quarter'=> $request->price_quarter ?: null,
            'price_third'  => $request->price_third ?: null,
            'price_half'   => $request->price_half ?: null,
            'notes'        => $request->notes,
        ]);

        return redirect()->route('udhiya.animals.show', $animal)
            ->with('toast_success', 'تم تحديث بيانات الحيوان بنجاح.');
    }

    public function destroy(Animal $animal)
    {
        // Check if animal is being used
        if ($animal->contractItems()->exists()) {
            return back()->with('toast_error', 'لا يمكن حذف حيوان مرتبط به عقود.');
        }

        if ($animal->transfers()->exists()) {
            return back()->with('toast_error', 'لا يمكن حذف حيوان تم نقله.');
        }

        if ($animal->shareSetting()->exists()) {
            return back()->with('toast_error', 'لا يمكن حذف حيوان لديه إعدادات أنصبة.');
        }

        if ($animal->expenses()->exists()) {
            return back()->with('toast_error', 'لا يمكن حذف حيوان مرتبط به مصروفات.');
        }

        if ($animal->meatInventory()->exists()) {
            return back()->with('toast_error', 'لا يمكن حذف حيوان موجود في المخزن.');
        }

        $code = $animal->code;
        $animal->delete();

        return redirect()->route('udhiya.animals.index')
            ->with('toast_success', "تم حذف الحيوان ({$code}) بنجاح.");
    }
}
