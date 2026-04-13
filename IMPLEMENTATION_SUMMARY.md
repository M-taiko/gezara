# 💰 نظام الخزائن + أسعار النصيب — ملخص التنفيذ

**التاريخ:** 2026-04-13  
**الحالة:** ✅ اكتمل بنسبة 100%

---

## 🎯 ما تم إنجازه

### المرحلة الأولى: نظام الخزائن (التطبيق الكامل)

#### 1️⃣ جداول البيانات الجديدة
- ✅ `wallets` — خزائن مسماة (نقدي، محفظة رقمية، بنك) مع رصيد
- ✅ `wallet_transactions` — سجل دخول/خروج مع مرجعية
- ✅ `wallet_transfers` — تحويلات بين الخزائن
- ✅ `payments` — إضافة `wallet_id` FK
- ✅ `supplier_payments` — إضافة `wallet_id` FK

#### 2️⃣ Models الجديدة
```
✅ App\Models\Wallet
✅ App\Models\WalletTransaction
✅ App\Models\WalletTransfer
```

#### 3️⃣ Services
- ✅ `WalletService` — transfer / credit / debit
- ✅ `PaymentService` — محدث لتسجيل في الخزينة
- ✅ `SupplierController::pay()` — محدث لخصم من الخزينة

#### 4️⃣ Controllers و Routes
- ✅ `WalletController` — CRUD كامل + transfer
- ✅ Routes: `udhiya.wallets.*` + `udhiya.wallets.transfer`

#### 5️⃣ Views
- ✅ `resources/views/udhiya/wallets/index.blade.php` — لوحة الخزائن كاملة
- ✅ تحديث forms الدفع (3 صفحات):
  - `udhiya/reports/customer.blade.php` — إضافة wallet selector
  - `udhiya/reports/supplier.blade.php` — إضافة wallet selector
  - `udhiya/purchases/show.blade.php` — إضافة wallet selector

#### 6️⃣ Sidebar
- ✅ إضافة رابط "الخزائن" في الـ navigation

---

### المرحلة الثانية: أسعار النصيب في المشتريات (التطبيق الكامل)

#### 1️⃣ جداول البيانات
- ✅ `purchase_items` — إضافة 7 أعمدة أسعار:
  - `price_full` — كامل
  - `price_half` — نصف
  - `price_third` — ثلث
  - `price_quarter` — ربع
  - `price_five` — خمس
  - `price_six` — سدس
  - `price_seven` — سبع

#### 2️⃣ Services
- ✅ `PurchaseService::store()` — نسخ الأسعار من purchase_items إلى animals

#### 3️⃣ Controllers
- ✅ `PurchaseController::update()` — دعم كامل لتحديث الأسعار

---

## 🔄 سير العمل الكامل

### سيناريو 1: إضافة مورد جديد + مشتريات
```
1. الذهاب إلى Udhiya → الخزائن
2. إضافة خزينة جديدة (مثلاً "فودافون كاش")
3. الذهاب إلى المشتريات
4. إضافة فاتورة شراء:
   - اختيار المورد
   - إضافة أصناف مع أسعار النصيب (7 أعمدة)
   - اختيار خزينة للدفعة الأولى (اختياري)
   - حفظ → ينشئ حيوانات بالأسعار
```

### سيناريو 2: تسجيل دفعة من عميل
```
1. الذهاب إلى كشف حساب العميل
2. ملء نموذج الدفعة:
   - اختيار الصك
   - إدخال المبلغ
   - اختيار طريقة الدفع
   - **جديد:** اختيار الخزينة المستلمة
   - إرسال
3. النظام يزيد رصيد الخزينة تلقائياً
```

### سيناريو 3: دفع للمورد
```
1. كشف حساب المورد أو صفحة الفاتورة
2. نقر "تسجيل دفعة"
3. ملء النموذج:
   - اختيار الفاتورة
   - المبلغ (مع حد أقصى)
   - التاريخ
   - **جديد:** اختيار الخزينة المخصومة
4. النظام ينقص رصيد الخزينة تلقائياً
```

### سيناريو 4: تحويل بين خزائن
```
1. صفحة الخزائن
2. قسم "تحويل بين الخزائن"
3. اختيار من/إلى + المبلغ
4. يتم التحويل + تسجيل حركتين في السجل
```

---

## 🗄️ التغييرات في الملفات

### جداول + Migrations
```
2026_04_13_102641_create_wallets_table.php
2026_04_13_102644_create_wallet_transactions_table.php
2026_04_13_102644_create_wallet_transfers_table.php
2026_04_13_102704_add_wallet_id_to_payments_table.php
2026_04_13_102704_add_wallet_id_to_supplier_payments_table.php
2026_04_13_102723_add_share_prices_to_purchase_items_table.php
```

### Models
```
✅ app/Models/Wallet.php (جديد)
✅ app/Models/WalletTransaction.php (جديد)
✅ app/Models/WalletTransfer.php (جديد)
✅ app/Models/Payment.php (تحديث: +wallet_id)
✅ app/Models/SupplierPayment.php (تحديث: +wallet_id)
✅ app/Models/PurchaseItem.php (تحديث: +7 price columns)
```

### Services
```
✅ app/Services/Udhiya/WalletService.php (جديد)
✅ app/Services/Udhiya/PaymentService.php (تحديث)
✅ app/Services/Udhiya/PurchaseService.php (تحديث)
```

### Controllers
```
✅ app/Http/Controllers/Udhiya/WalletController.php (جديد)
✅ app/Http/Controllers/Udhiya/SupplierController.php (تحديث)
✅ app/Http/Controllers/Udhiya/PurchaseController.php (تحديث)
```

### Views
```
✅ resources/views/udhiya/wallets/index.blade.php (جديد)
✅ resources/views/udhiya/reports/customer.blade.php (تحديث)
✅ resources/views/udhiya/reports/supplier.blade.php (تحديث)
✅ resources/views/udhiya/purchases/show.blade.php (تحديث)
✅ resources/views/layouts/main-sidebar.blade.php (تحديث)
```

### Routes
```
✅ routes/web.php (تحديث: +wallet routes)
```

---

## ⚙️ المميزات الرئيسية

### نظام الخزائن
- ✅ CRUD كامل (إنشاء، تعديل، حذف)
- ✅ 3 أنواع: نقدي، محفظة رقمية، بنك
- ✅ رصيد فوري (يتحدث تلقائياً)
- ✅ سجل معاملات كامل
- ✅ تحويلات بين الخزائن بسهولة
- ✅ عرض آخر 3 معاملات على كل خزينة
- ✅ حذف آمن (فقط لو الرصيد صفر)

### أسعار النصيب
- ✅ 7 أسعار لكل حيوان: كامل، نصف، ثلث، ربع، خمس، سدس، سبع
- ✅ إدخال سهل في نموذج الشراء (7 أعمدة في الجدول)
- ✅ تحديث كامل عند تعديل الفاتورة
- ✅ الأسعار تُنسخ تلقائياً للحيوانات

### ربط الخزائن بالدفعات
- ✅ اختيار الخزينة عند تسجيل دفعة من عميل
- ✅ اختيار الخزينة عند دفع المورد
- ✅ سجل كامل: من دفع، كم، إلى أي خزينة، متى
- ✅ backwards compatible: لو ما اخترت خزينة تشتغل بدون مشكلة

---

## ✨ النقاط الحساسة والميزات الأمان

1. **Wallet Balance Integrity**
   - الرصيد يتحدث في transaction واحدة
   - التحويل ينقص من محفظة ويزيد الأخرى في نفس الوقت

2. **Payment Validation**
   - يتحقق من الرصيد الكافي قبل التحويل
   - يمنع دفع أكثر من المتبقي

3. **Backwards Compatibility**
   - لو ما اخترت wallet_id الدفعة تسجل بدون مشكلة
   - المنطق القديم (Treasury) يشتغل لو ما حددت خزينة

4. **Data Integrity**
   - كل دفعة مرتبطة بـ reference_type و reference_id
   - يمكن تتبع أي دفعة لأصلها

---

## 🧪 الاختبار

```bash
# تشغيل الـ tinker
php artisan tinker

# إنشاء خزينة
$w = App\Models\Wallet::create(['name' => 'فودافون كاش', 'type' => 'mobile', 'balance' => 10000]);

# إضافة معاملة
App\Models\WalletTransaction::create([
    'wallet_id' => $w->id,
    'type' => 'in',
    'amount' => 5000,
    'date' => '2026-04-13',
    'description' => 'دفعة من عميل'
]);

# التحقق
$w->refresh();
echo $w->balance; // 15000
```

---

## 📝 ملاحظات مهمة

1. **الأسعار في المشتريات** تُحفظ في `purchase_items` **وتُنسخ إلى animals**
   - لو حدثت animal.price_* يدوياً (بخلاف الشراء) فالسعر ما يُحدّث في purchase_items
   - لكن هذا نادر — الأسعار عادة تُدخل أول مرة في الشراء

2. **الخزائن الخالية** لا يمكن حذفها إلا لو الرصيد = 0

3. **wallet_id اختياري** في الدفعات والمشتريات للـ backwards compatibility

4. **الأسعار الـ 7** كلها optional في purchase_items — ممكن تدخل بعضها بس بدون الكل

---

## 🚀 الخطوات التالية (اختياري)

- [ ] إضافة تقرير الخزائن (رصيد يومي، معاملات شهرية)
- [ ] إصدار/طباعة كشف خزينة مفصل
- [ ] إضافة reconciliation (تصفية) الخزائن
- [ ] تصدير معاملات الخزائن لـ Excel
- [ ] إضافة إشعارات عند نقصان الرصيد

---

**الحالة النهائية:** ✅ نظام متكامل جاهز للاستخدام الفوري
