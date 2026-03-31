<?php

namespace App\Http\Controllers\Udhiya;

use App\Http\Controllers\Controller;
use App\Models\MeatInventory;
use App\Models\MeatSale;
use Illuminate\Http\Request;

class MeatSaleController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'meat_inventory_id' => 'required|exists:meat_inventory,id',
            'customer_name'     => 'required|string|max:255',
            'customer_phone'    => 'nullable|string|max:20',
            'weight_kg'         => 'required|numeric|min:0.1',
            'price_per_kg'      => 'required|numeric|min:0',
            'sale_date'         => 'required|date',
            'notes'             => 'nullable|string',
        ]);

        $batch = MeatInventory::findOrFail($data['meat_inventory_id']);

        if ($data['weight_kg'] > $batch->remainingWeight()) {
            return back()->with('toast_error', "الوزن المطلوب ({$data['weight_kg']} كجم) أكبر من المتاح (" . number_format($batch->remainingWeight(), 1) . ' كجم)');
        }

        $data['total_amount'] = round($data['weight_kg'] * $data['price_per_kg'], 2);

        MeatSale::create($data);

        $newSold = $batch->sold_weight_kg + $data['weight_kg'];
        $batch->update(['sold_weight_kg' => $newSold]);

        return back()->with('toast_success', "✅ تم تسجيل بيع {$data['weight_kg']} كجم لـ {$data['customer_name']}");
    }

    public function destroy(MeatSale $sale)
    {
        $batch = $sale->inventory;

        $sale->delete();

        // Restore the sold weight
        $restored = max(0, $batch->sold_weight_kg - $sale->weight_kg);
        $batch->update(['sold_weight_kg' => $restored]);

        return back()->with('toast_success', 'تم حذف عملية البيع واسترجاع الوزن للمخزن');
    }
}
