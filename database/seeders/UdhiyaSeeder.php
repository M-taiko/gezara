<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Animal;
use App\Models\Contract;
use App\Models\ContractItem;
use App\Models\Customer;
use App\Models\MainCategory;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\Treasury;
use App\Models\Warehouse;
use App\Services\Udhiya\AccountingService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UdhiyaSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Warehouses ───────────────────────────────────────────────────
        $farm  = Warehouse::create(['name' => 'المزرعة',    'description' => 'المخزن الرئيسي للمزرعة']);
        $store = Warehouse::create(['name' => 'المحل',       'description' => 'محل البيع']);

        // ─── Main Categories ──────────────────────────────────────────────
        $cattle = MainCategory::create(['name' => 'عجول',  'code' => 'BQR']);
        $sheep  = MainCategory::create(['name' => 'خرفان', 'code' => 'GHN']);
        $goat   = MainCategory::create(['name' => 'جديان', 'code' => 'JDN']);
        $camel  = MainCategory::create(['name' => 'جمال',  'code' => 'JML']);

        // ─── Products ─────────────────────────────────────────────────────
        $prodCattle = Product::create(['main_category_id' => $cattle->id, 'name' => 'عجل عربي',      'default_price' => 8000,  'is_active' => true]);
        $prodSheep  = Product::create(['main_category_id' => $sheep->id,  'name' => 'خروف بلدي',     'default_price' => 3000,  'is_active' => true]);
        $prodGoat   = Product::create(['main_category_id' => $goat->id,   'name' => 'جدي نعيمي',     'default_price' => 2500,  'is_active' => true]);
        $prodCamel  = Product::create(['main_category_id' => $camel->id,  'name' => 'جمل عربي أصيل', 'default_price' => 25000, 'is_active' => true]);

        // ─── Chart of Accounts ────────────────────────────────────────────
        Account::insert([
            ['code' => '1000', 'name' => 'الصندوق',                    'type' => 'asset',     'is_system' => true, 'balance' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['code' => '1100', 'name' => 'المخزون — الحيوانات',       'type' => 'asset',     'is_system' => true, 'balance' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['code' => '2000', 'name' => 'ذمم دائنة — الموردون',      'type' => 'liability', 'is_system' => true, 'balance' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['code' => '3000', 'name' => 'ذمم مدينة — العملاء',       'type' => 'asset',     'is_system' => true, 'balance' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['code' => '4000', 'name' => 'إيرادات المبيعات',           'type' => 'revenue',   'is_system' => true, 'balance' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['code' => '5000', 'name' => 'تكلفة البضاعة المباعة',     'type' => 'expense',   'is_system' => true, 'balance' => 0, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ─── Suppliers ────────────────────────────────────────────────────
        $sup1 = Supplier::create(['name' => 'مزرعة الأمل',    'phone' => '0501234567', 'address' => 'القاهرة', 'balance' => 0]);
        $sup2 = Supplier::create(['name' => 'تجارة أبو يوسف', 'phone' => '0509876543', 'address' => 'الجيزة',  'balance' => 0]);
        $sup3 = Supplier::create(['name' => 'مزارع الخليج',   'phone' => '0512345678', 'address' => 'الإسكندرية', 'balance' => 0]);

        // ─── Purchases ────────────────────────────────────────────────────
        $accounting = new AccountingService();

        // Purchase 1: 2 cattle + 3 sheep
        $pur1 = Purchase::create([
            'supplier_id' => $sup1->id, 'date' => '2026-01-10',
            'notes' => 'دفعة أولى من المزرعة',
            'total' => 2 * 7500 + 3 * 2800, 'paid' => 15000, 'status' => 'confirmed',
        ]);
        PurchaseItem::create(['purchase_id' => $pur1->id, 'product_id' => $prodCattle->id, 'quantity' => 2, 'cost_per_unit' => 7500, 'total' => 15000]);
        PurchaseItem::create(['purchase_id' => $pur1->id, 'product_id' => $prodSheep->id,  'quantity' => 3, 'cost_per_unit' => 2800,  'total' => 8400]);
        for ($i = 1; $i <= 2; $i++) {
            Animal::create(['product_id' => $prodCattle->id, 'supplier_id' => $sup1->id, 'purchase_id' => $pur1->id, 'warehouse_id' => $farm->id, 'cost' => 7500, 'status' => 'available', 'code' => '2026-BQR-' . str_pad($i, 4, '0', STR_PAD_LEFT)]);
        }
        for ($i = 1; $i <= 3; $i++) {
            Animal::create(['product_id' => $prodSheep->id, 'supplier_id' => $sup1->id, 'purchase_id' => $pur1->id, 'warehouse_id' => $farm->id, 'cost' => 2800, 'status' => 'available', 'code' => '2026-GHN-' . str_pad($i, 4, '0', STR_PAD_LEFT)]);
        }
        $sup1->increment('balance', $pur1->total - $pur1->paid);
        $accounting->recordPurchase($pur1);

        // Purchase 2: 1 camel + 2 goats
        $pur2 = Purchase::create([
            'supplier_id' => $sup2->id, 'date' => '2026-01-15',
            'notes' => 'جمل وجديان',
            'total' => 1 * 22000 + 2 * 2400, 'paid' => 22000, 'status' => 'confirmed',
        ]);
        PurchaseItem::create(['purchase_id' => $pur2->id, 'product_id' => $prodCamel->id, 'quantity' => 1, 'cost_per_unit' => 22000, 'total' => 22000]);
        PurchaseItem::create(['purchase_id' => $pur2->id, 'product_id' => $prodGoat->id,  'quantity' => 2, 'cost_per_unit' => 2400,  'total' => 4800]);
        Animal::create(['product_id' => $prodCamel->id, 'supplier_id' => $sup2->id, 'purchase_id' => $pur2->id, 'warehouse_id' => $farm->id, 'cost' => 22000, 'status' => 'available', 'code' => '2026-JML-0001']);
        for ($i = 1; $i <= 2; $i++) {
            Animal::create(['product_id' => $prodGoat->id, 'supplier_id' => $sup2->id, 'purchase_id' => $pur2->id, 'warehouse_id' => $farm->id, 'cost' => 2400, 'status' => 'available', 'code' => '2026-JDN-' . str_pad($i, 4, '0', STR_PAD_LEFT)]);
        }
        $sup2->increment('balance', $pur2->total - $pur2->paid);
        $accounting->recordPurchase($pur2);

        // ─── Set Prices ───────────────────────────────────────────────────
        Animal::whereHas('product', fn($q) => $q->where('main_category_id', $cattle->id))
            ->update(['price_full' => 10000, 'price_seven' => 1500, 'price_five' => 2000, 'price_quarter' => 2500, 'price_half' => 5000]);

        Animal::whereHas('product', fn($q) => $q->whereIn('main_category_id', [$sheep->id, $goat->id]))
            ->update(['price_full' => 3500]);

        Animal::whereHas('product', fn($q) => $q->where('main_category_id', $camel->id))
            ->update(['price_full' => 28000, 'price_seven' => 4200, 'price_five' => 5600, 'price_quarter' => 7000, 'price_half' => 14000]);

        // Set one cattle as grouped (seven shares)
        $groupedCow = Animal::whereHas('product', fn($q) => $q->where('main_category_id', $cattle->id))->first();
        $groupedCow->update(['is_grouped' => true]);
        $groupedCow->shareSetting()->create(['share_type' => 'seven', 'total_shares' => 7, 'sold_shares' => 0, 'remaining_shares' => 7]);

        // Set camel as grouped (seven shares)
        $camelAnimal = Animal::whereHas('product', fn($q) => $q->where('main_category_id', $camel->id))->first();
        $camelAnimal->update(['is_grouped' => true]);
        $camelAnimal->shareSetting()->create(['share_type' => 'seven', 'total_shares' => 7, 'sold_shares' => 0, 'remaining_shares' => 7]);

        // ─── Customers ────────────────────────────────────────────────────
        $cust1 = Customer::create(['name' => 'محمد أحمد السيد', 'phone' => '0551234567', 'address' => 'القاهرة']);
        $cust2 = Customer::create(['name' => 'فاطمة علي محمود', 'phone' => '0559876543', 'address' => 'الإسكندرية']);
        $cust3 = Customer::create(['name' => 'عبدالله إبراهيم',  'phone' => '0562345678', 'address' => 'الجيزة']);

        // ─── Contract 1: Full sheep sale ──────────────────────────────────
        $sheepAnimal = Animal::whereHas('product', fn($q) => $q->where('main_category_id', $sheep->id))->first();
        $cont1 = Contract::create([
            'customer_id' => $cust1->id, 'contract_number' => 'CNT-2026-0001',
            'slaughter_day' => '2026-06-15', 'slaughter_order' => 1,
            'notes' => 'أضحية العيد', 'total_amount' => 3500, 'paid_amount' => 0, 'remaining_amount' => 3500, 'status' => 'active',
        ]);
        ContractItem::create(['contract_id' => $cont1->id, 'animal_id' => $sheepAnimal->id, 'share_type' => 'full', 'shares_count' => 1, 'unit_price' => 3500, 'total_price' => 3500]);
        $sheepAnimal->update(['status' => 'fully_allocated']);
        $accounting->recordContract($cont1);

        // ─── Contract 2: Three shares from grouped cattle ─────────────────
        $cont2 = Contract::create([
            'customer_id' => $cust2->id, 'contract_number' => 'CNT-2026-0002',
            'slaughter_day' => '2026-06-15', 'slaughter_order' => 2,
            'total_amount' => 4500, 'paid_amount' => 0, 'remaining_amount' => 4500, 'status' => 'active',
        ]);
        ContractItem::create(['contract_id' => $cont2->id, 'animal_id' => $groupedCow->id, 'share_type' => 'seven', 'shares_count' => 3, 'unit_price' => 1500, 'total_price' => 4500]);
        $groupedCow->shareSetting->update(['sold_shares' => 3, 'remaining_shares' => 4]);
        $groupedCow->update(['status' => 'partially_allocated']);
        $accounting->recordContract($cont2);

        // ─── Payments ─────────────────────────────────────────────────────
        $pmt1 = Payment::create(['contract_id' => $cont1->id, 'receipt_number' => 'RCP-2026-0001', 'amount' => 2000, 'payment_method' => 'cash', 'date' => '2026-01-20', 'notes' => 'دفعة مقدمة']);
        $cont1->update(['paid_amount' => 2000, 'remaining_amount' => 1500]);
        Treasury::create(['type' => 'in', 'amount' => 2000, 'reference_type' => Payment::class, 'reference_id' => $pmt1->id, 'description' => 'دفعة من ' . $cust1->name, 'date' => '2026-01-20']);
        $accounting->recordCustomerPayment($pmt1);

        $pmt2 = Payment::create(['contract_id' => $cont2->id, 'receipt_number' => 'RCP-2026-0002', 'amount' => 4500, 'payment_method' => 'transfer', 'date' => '2026-01-22', 'notes' => 'تحويل بنكي كامل']);
        $cont2->update(['paid_amount' => 4500, 'remaining_amount' => 0, 'status' => 'completed']);
        Treasury::create(['type' => 'in', 'amount' => 4500, 'reference_type' => Payment::class, 'reference_id' => $pmt2->id, 'description' => 'تحويل من ' . $cust2->name, 'date' => '2026-01-22']);
        $accounting->recordCustomerPayment($pmt2);

        $this->command->info('UdhiyaSeeder: Warehouses=2, Categories=4, Products=4, Accounts=6');
        $this->command->info('UdhiyaSeeder: Suppliers=3, Purchases=2, Animals=8, Customers=3, Contracts=2, Payments=2');
    }
}
