<?php

namespace App\Http\Controllers\Udhiya;

use App\Http\Controllers\Controller;
use App\Models\MeatInventory;
use App\Models\MeatSale;
use Illuminate\Http\Request;

class MeatInventoryController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status');

        $items = MeatInventory::with('animal.product.mainCategory', 'sales')
            ->when($status === 'sold_out', fn($q) => $q->whereRaw('sold_weight_kg >= weight_kg'))
            ->when($status === 'available', fn($q) => $q->whereRaw('sold_weight_kg < weight_kg')->where('status', 'available'))
            ->when(!$status, fn($q) => $q)
            ->latest()
            ->get();

        $sales = MeatSale::with('inventory.animal')
            ->latest()
            ->get();

        $totalStock     = MeatInventory::sum('weight_kg');
        $totalSoldKg    = MeatInventory::sum('sold_weight_kg');
        $totalRemaining = max(0, $totalStock - $totalSoldKg);
        $totalRevenue   = MeatSale::sum('total_amount');

        return view('udhiya.meat-inventory.index', compact(
            'items', 'sales', 'status',
            'totalStock', 'totalSoldKg', 'totalRemaining', 'totalRevenue'
        ));
    }

    public function deliver(MeatInventory $item)
    {
        if ($item->status === 'delivered') {
            return back()->with('toast_warning', 'تم تسليم هذا الصنف مسبقاً');
        }

        $item->update([
            'status'       => 'delivered',
            'delivered_at' => now(),
        ]);

        return back()->with('toast_success', '✅ تم تسجيل التسليم بنجاح');
    }

    public function destroy(MeatInventory $item)
    {
        $item->delete();
        return back()->with('toast_success', 'تم حذف الإدخال');
    }
}
