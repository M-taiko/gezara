<?php

namespace App\Http\Controllers\Udhiya;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\ContractItem;
use Illuminate\Http\Request;

class ContractItemController extends Controller
{
    public function update(Request $request, ContractItem $item)
    {
        $data = $request->validate([
            'unit_price'  => 'required|numeric|min:0.01',
            'shares_count' => 'required|integer|min:1',
        ]);

        // Calculate price difference for accounting
        $oldTotalPrice = $item->total_price;
        $newTotalPrice = $data['unit_price'] * $data['shares_count'];
        $priceDiff = $newTotalPrice - $oldTotalPrice;

        // Update item
        $item->unit_price = $data['unit_price'];
        $item->shares_count = $data['shares_count'];
        $item->total_price = $newTotalPrice;
        $item->save();

        // Update corresponding group member if exists
        if ($item->groupMember) {
            $item->groupMember->update(['shares_count' => $data['shares_count']]);
        }

        // Recalculate contract totals
        $contract = $item->contract;
        $newTotalAmount = $contract->items()->sum('total_price');

        // Update remaining: new_total - paid_amount
        $newRemainingAmount = $newTotalAmount - $contract->paid_amount;

        $contract->update([
            'total_amount' => $newTotalAmount,
            'remaining_amount' => $newRemainingAmount
        ]);

        return back()->with('toast_success', 'تم تحديث العنصر بنجاح. تم تحديث إجمالي الصك تلقائياً.');
    }

    public function destroy(ContractItem $item)
    {
        $contract = $item->contract;
        $animal = $item->animal;

        // Reverse animal share counts
        if ($animal) {
            if ($item->share_type === 'full') {
                $animal->update(['status' => 'available']);
            } else {
                $setting = $animal->shareSetting;
                if ($setting) {
                    $setting->decrement('sold_shares', $item->shares_count);
                    $setting->increment('remaining_shares', $item->shares_count);

                    $newStatus = $setting->sold_shares === 0 ? 'available' : 'partially_allocated';
                    $animal->update(['status' => $newStatus]);
                }
            }
        }

        // Delete the item
        $item->delete();

        // Recalculate contract totals
        $newTotalAmount = $contract->items()->sum('total_price');
        $newRemainingAmount = $newTotalAmount - $contract->paid_amount;

        $contract->update([
            'total_amount' => $newTotalAmount,
            'remaining_amount' => $newRemainingAmount
        ]);

        return back()->with('toast_success', 'تم حذف العنصر بنجاح. تم تحديث إجمالي الصك تلقائياً.');
    }
}
