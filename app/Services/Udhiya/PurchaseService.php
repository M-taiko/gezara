<?php

namespace App\Services\Udhiya;

use App\Models\Animal;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;

class PurchaseService
{
    public function __construct(private AccountingService $accounting) {}

    public function store(array $data): Purchase
    {
        return DB::transaction(function () use ($data) {
            $total = collect($data['items'])->sum('total');

            $purchase = Purchase::create([
                'supplier_id' => $data['supplier_id'],
                'date'        => $data['date'],
                'notes'       => $data['notes'] ?? null,
                'total'       => $total,
                'paid'        => $data['paid'] ?? 0,
                'status'      => 'confirmed',
            ]);

            // Default warehouse = المزرعة (first warehouse)
            $defaultWarehouse = Warehouse::first();

            foreach ($data['items'] as $item) {
                $itemData = [
                    'purchase_id'   => $purchase->id,
                    'product_id'    => $item['product_id'],
                    'quantity'      => $item['quantity'] ?? 1,
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

                $purchaseItem = PurchaseItem::create($itemData);

                // Auto-create animals with share prices
                $costPerAnimal = $item['cost_per_unit'];
                $qty = $item['quantity'] ?? 1;
                for ($i = 0; $i < $qty; $i++) {
                    $animalData = [
                        'product_id'   => $item['product_id'],
                        'supplier_id'  => $data['supplier_id'],
                        'purchase_id'  => $purchase->id,
                        'warehouse_id' => $defaultWarehouse->id,
                        'cost'         => $costPerAnimal,
                        'weight'       => $item['weight'] ?? null,
                        'status'       => 'available',
                    ];

                    // Copy share prices from purchase item to animal
                    foreach (['price_full', 'price_half', 'price_third', 'price_quarter', 'price_five', 'price_six', 'price_seven'] as $priceField) {
                        if (isset($item[$priceField])) {
                            $animalData[$priceField] = $item[$priceField];
                        }
                    }

                    Animal::create($animalData);
                }
            }

            // سجّل الدفعة الأولى إن وجدت
            if (!empty($data['paid']) && $data['paid'] > 0) {
                SupplierPayment::create([
                    'supplier_id' => $purchase->supplier_id,
                    'purchase_id' => $purchase->id,
                    'amount'      => $data['paid'],
                    'paid_at'     => $data['date'],
                    'notes'       => 'دفعة عند الشراء',
                ]);
            }

            // Update supplier balance (amount owed)
            $remaining = $total - ($data['paid'] ?? 0);
            Supplier::find($data['supplier_id'])->increment('balance', $remaining);

            $this->accounting->recordPurchase($purchase);

            return $purchase;
        });
    }
}
