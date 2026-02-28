<?php

namespace App\Services\Udhiya;

use App\Models\Animal;
use App\Models\AnimalShareSetting;
use App\Models\AnimalWarehouseTransfer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnimalService
{
    public function setGrouped(Animal $animal, string $shareType): AnimalShareSetting
    {
        return DB::transaction(function () use ($animal, $shareType) {
            if ($animal->status === 'fully_allocated' || $animal->status === 'slaughtered') {
                throw new \RuntimeException('لا يمكن تغيير إعدادات الحيوان في الحالة الحالية.');
            }

            $totalShares = Animal::SHARE_MAP[$shareType];

            $setting = AnimalShareSetting::updateOrCreate(
                ['animal_id' => $animal->id],
                [
                    'share_type'       => $shareType,
                    'total_shares'     => $totalShares,
                    'sold_shares'      => 0,
                    'remaining_shares' => $totalShares,
                ]
            );

            $animal->update([
                'is_grouped' => true,
                'status'     => 'available',
            ]);

            return $setting;
        });
    }

    public function unsetGrouped(Animal $animal): void
    {
        DB::transaction(function () use ($animal) {
            if ($animal->shareSetting && $animal->shareSetting->sold_shares > 0) {
                throw new \RuntimeException('لا يمكن إلغاء التجميع: هناك أنصبة مباعة بالفعل.');
            }

            $animal->shareSetting?->delete();
            $animal->update(['is_grouped' => false]);
        });
    }

    public function transfer(Animal $animal, int $toWarehouseId, ?string $notes = null): AnimalWarehouseTransfer
    {
        return DB::transaction(function () use ($animal, $toWarehouseId, $notes) {
            if ($animal->warehouse_id === $toWarehouseId) {
                throw new \RuntimeException('الحيوان موجود في نفس المخزن.');
            }

            $transfer = AnimalWarehouseTransfer::create([
                'animal_id'         => $animal->id,
                'from_warehouse_id' => $animal->warehouse_id,
                'to_warehouse_id'   => $toWarehouseId,
                'transferred_by'    => Auth::id(),
                'notes'             => $notes,
            ]);

            $animal->update(['warehouse_id' => $toWarehouseId]);

            return $transfer;
        });
    }
}
