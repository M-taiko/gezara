import re

with open('app/Services/Udhiya/ContractService.php', 'r', encoding='utf-8') as f:
    content = f.read()

update_meth = """
    public function update(Contract $contract, array $data): Contract
    {
        return DB::transaction(function () use ($contract, $data) {
            // Restore animal statuses for existing items
            foreach ($contract->items as $item) {
                $animal = $item->animal;
                if ($animal) {
                    if ($item->share_type === 'full') {
                        $animal->update(['status' => 'available']);
                    } else {
                        $setting = $animal->shareSetting()->lockForUpdate()->first();
                        if ($setting) {
                            $setting->decrement('sold_shares', $item->shares_count);
                            $setting->increment('remaining_shares', $item->shares_count);
                            $newStatus = $setting->sold_shares === 0 ? 'available' : 'partially_allocated';
                            $animal->update(['status' => $newStatus]);
                        }
                    }
                }
                if ($item->group_id) {
                    SlaughterGroupMember::where('contract_item_id', $item->id)->delete();
                }
            }

            $contract->items()->delete();

            $total = 0;
            $itemsData = [];
            foreach ($data['items'] as $item) {
                if (empty($item['animal_id'])) {
                    $sharesCount = $item['shares_count'] ?? 1;
                    $unitPrice   = (float) $item['unit_price'];
                    $totalPrice  = $unitPrice * $sharesCount;

                    $itemsData[] = [
                        'animal'       => null,
                        'share_type'   => $item['share_type'],
                        'shares_count' => $sharesCount,
                        'unit_price'   => $unitPrice,
                        'total_price'  => $totalPrice,
                        'setting'      => null,
                        'group_id'     => $item['group_id'] ?? null,
                    ];

                    $total += $totalPrice;
                    continue;
                }

                if ($item['share_type'] === 'full') {
                    $animal = Animal::lockForUpdate()->findOrFail($item['animal_id']);

                    if (!$animal->canSellFull()) {
                        throw new \RuntimeException("الحيوان #{$animal->code} غير متاح للبيع الكامل.");
                    }

                    $unitPrice  = $animal->price_full ?? $animal->cost;
                    $totalPrice = $unitPrice;

                    $itemsData[] = [
                        'animal'       => $animal,
                        'share_type'   => 'full',
                        'shares_count' => 1,
                        'unit_price'   => $unitPrice,
                        'total_price'  => $totalPrice,
                        'setting'      => null,
                        'group_id'     => $item['group_id'] ?? null,
                    ];

                    $total += $totalPrice;
                } else {
                    $animal = Animal::lockForUpdate()->findOrFail($item['animal_id']);

                    $setting = AnimalShareSetting::where('animal_id', $animal->id)
                        ->lockForUpdate()->first();

                    if (!$setting) {
                        $shareType   = $item['share_type'];
                        $totalShares = Animal::SHARE_MAP[$shareType] ?? null;
                        if (!$totalShares) {
                            throw new \RuntimeException("نوع الحصة '{$shareType}' غير صالح.");
                        }
                        $setting = AnimalShareSetting::create([
                            'animal_id'        => $animal->id,
                            'share_type'       => $shareType,
                            'total_shares'     => $totalShares,
                            'sold_shares'      => 0,
                            'remaining_shares' => $totalShares,
                        ]);
                        $animal->update(['is_grouped' => true]);
                        $animal->refresh();
                    }

                    $sharesCount = $item['shares_count'] ?? 1;

                    if ($setting->remaining_shares < $sharesCount) {
                        throw new \RuntimeException(
                            "الحيوان #{$animal->code}: الأنصبة المتاحة ({$setting->remaining_shares}) أقل من المطلوبة ({$sharesCount})."
                        );
                    }

                    $priceField = 'price_' . $setting->share_type;
                    $unitPrice  = $animal->$priceField ?? ($animal->price_full / $setting->total_shares);
                    $totalPrice = $unitPrice * $sharesCount;

                    $itemsData[] = [
                        'animal'       => $animal,
                        'share_type'   => $item['share_type'],
                        'shares_count' => $sharesCount,
                        'unit_price'   => $unitPrice,
                        'total_price'  => $totalPrice,
                        'setting'      => $setting,
                        'group_id'     => $item['group_id'] ?? null,
                    ];

                    $total += $totalPrice;
                }
            }

            if ($contract->paid_amount > $total) {
                throw new \RuntimeException("المبلغ المدفوع ({$contract->paid_amount}) أكبر من إجمالي الصك الجديد ({$total}). يرجى تسوية المبالغ أولاً.");
            }

            $contract->update([
                'customer_id'      => $data['customer_id'],
                'slaughter_day'    => $data['slaughter_day'] ?? null,
                'slaughter_order'  => $data['slaughter_order'] ?? null,
                'notes'            => $data['notes'] ?? null,
                'total_amount'     => $total,
                'remaining_amount' => $total - $contract->paid_amount,
            ]);

            foreach ($itemsData as $itemData) {
                $contractItem = ContractItem::create([
                    'contract_id'  => $contract->id,
                    'animal_id'    => $itemData['animal']?->id,
                    'group_id'     => $itemData['group_id'] ?? null,
                    'share_type'   => $itemData['share_type'],
                    'shares_count' => $itemData['shares_count'],
                    'unit_price'   => $itemData['unit_price'],
                    'total_price'  => $itemData['total_price'],
                ]);

                if (!empty($itemData['group_id'])) {
                    SlaughterGroupMember::updateOrCreate(
                        [
                            'group_id'    => $itemData['group_id'],
                            'customer_id' => $data['customer_id'],
                        ],
                        [
                            'contract_item_id' => $contractItem->id,
                            'shares_count'     => $itemData['shares_count'],
                        ]
                    );
                }

                if ($itemData['animal']) {
                    if ($itemData['share_type'] === 'full') {
                        $itemData['animal']->update(['status' => 'fully_allocated']);
                    } else {
                        $setting = $itemData['setting'];
                        $setting->increment('sold_shares', $itemData['shares_count']);
                        $setting->decrement('remaining_shares', $itemData['shares_count']);
                        $setting->refresh();

                        $newStatus = $setting->remaining_shares === 0 ? 'fully_allocated' : 'partially_allocated';
                        $itemData['animal']->update(['status' => $newStatus]);
                    }
                }
            }

            $this->accounting->updateContract($contract);
            
            return $contract;
        });
    }
"""

content = content.replace("public function cancel(Contract $contract): void", update_meth + "\n    public function cancel(Contract $contract): void")

with open('app/Services/Udhiya/ContractService.php', 'w', encoding='utf-8') as f:
    f.write(content)
