@extends('layouts.master')

@section('page-header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 mb-8 mt-2">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
            <span class="text-emerald-600 text-4xl">💳</span> تسجيل دفعة جديدة
        </h1>
        <p class="text-slate-500 font-medium text-sm mt-1">
            <a href="{{ route('udhiya.collections.index') }}" class="text-emerald-500 hover:text-emerald-700 hover:underline">تحصيل الدفعات</a> / إضافة
        </p>
    </div>
</div>
@endsection

@section('content')

<form action="{{ route('udhiya.collections.store') }}" method="POST" enctype="multipart/form-data" id="paymentForm"
      class="flex flex-col lg:flex-row gap-6 pb-16">
    @csrf

    {{-- ═══════════ LEFT: MAIN FORM ═══════════ --}}
    <div class="flex-1 space-y-6">

        {{-- STEP 1: CUSTOMER & CONTRACT --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-emerald-100 bg-gradient-to-b from-emerald-50 to-white">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center font-black text-sm flex-shrink-0">1</div>
                    <h6 class="text-base font-black text-emerald-900 m-0">العميل والصك</h6>
                </div>
            </div>

            <div class="px-6 py-6 space-y-5">
                {{-- Customer Select --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-widest">اختر العميل</label>
                    <div class="flex gap-3">
                        <select name="customer_id" required id="customerSelect"
                                class="flex-1 rounded-xl border border-slate-200 bg-white focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                            <option value="">— اختر عميل —</option>
                            @foreach($customers as $customer)
                            <option value="{{ $customer->id }}"
                                    data-contracts='@json($customer->contracts->where("remaining_amount", ">", 0)->values())'
                                    data-phone="{{ e($customer->phone) }}"
                                    {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                {{ e($customer->name) }}
                            </option>
                            @endforeach
                        </select>
                        <button type="button" id="toggleCustomerForm"
                                class="px-4 py-2.5 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-600 font-bold hover:bg-emerald-100 transition-colors text-sm whitespace-nowrap">
                            ➕ عميل جديد
                        </button>
                    </div>
                    @error('customer_id')<p class="text-rose-500 text-xs mt-2">{{ $message }}</p>@enderror
                </div>

                {{-- Customer Details Display --}}
                <div id="customerDetails" class="p-4 rounded-2xl border border-emerald-100 bg-emerald-50/50 hidden">
                    <div class="grid grid-cols-3 gap-4 text-center text-sm">
                        <div>
                            <p class="text-xs font-bold text-emerald-600 mb-1">الاسم</p>
                            <p class="font-bold text-slate-900" id="customerName">—</p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-emerald-600 mb-1">الهاتف</p>
                            <p class="font-bold text-slate-900" id="customerPhone">—</p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-emerald-600 mb-1">عدد الصكوك</p>
                            <p class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-700 font-bold text-sm" id="customerContracts">0</p>
                        </div>
                    </div>
                </div>

                {{-- Contract Select --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-widest">اختر الصك</label>
                    <select name="contract_id" required id="contractSelect"
                            class="w-full rounded-xl border border-slate-200 bg-white focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                        <option value="">— اختر الصك —</option>
                    </select>
                    @error('contract_id')<p class="text-rose-500 text-xs mt-2">{{ $message }}</p>@enderror
                </div>

                {{-- No Contracts Alert --}}
                <div id="noContractsMessage" class="p-4 rounded-2xl border border-rose-200 bg-rose-50 hidden">
                    <p class="text-rose-700 font-semibold text-sm mb-3">⚠️ هذا العميل لا يمتلك أي صكوك متاحة</p>
                    <button type="button" id="createContractBtn"
                            class="w-full px-4 py-2.5 rounded-xl border border-amber-200 bg-amber-50 text-amber-600 font-bold hover:bg-amber-100 transition-colors text-sm">
                        ➕ إنشاء صك جديد بسرعة
                    </button>
                </div>

                {{-- Contract Details Display --}}
                <div id="contractDetails" class="p-4 rounded-2xl border border-amber-100 bg-amber-50/50 hidden">
                    <div class="grid grid-cols-3 gap-4 text-center text-sm">
                        <div>
                            <p class="text-xs font-bold text-amber-600 mb-1">الإجمالي</p>
                            <p class="font-bold text-slate-900"><span id="totalAmount">0</span></p>
                            <p class="text-xs text-amber-600">ج.م</p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-amber-600 mb-1">المدفوع</p>
                            <p class="font-bold text-slate-900"><span id="paidAmount">0</span></p>
                            <p class="text-xs text-amber-600">ج.م</p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-amber-600 mb-1">المتبقي</p>
                            <p class="font-bold text-amber-700"><span id="remainingAmount">0</span></p>
                            <p class="text-xs text-amber-600">ج.م</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- STEP 2: PAYMENT DETAILS --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-blue-100 bg-gradient-to-b from-blue-50 to-white">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center font-black text-sm flex-shrink-0">2</div>
                    <h6 class="text-base font-black text-blue-900 m-0">بيانات الدفعة</h6>
                </div>
            </div>

            <div class="px-6 py-6 space-y-5">
                <div class="grid md:grid-cols-2 gap-5">
                    {{-- Amount --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-widest">المبلغ (ج.م)</label>
                        <input type="number" name="amount" required min="0.01" step="0.01"
                               id="amountInput" placeholder="0.00" value="{{ old('amount') }}"
                               class="w-full rounded-xl border border-slate-200 bg-white focus:border-blue-400 focus:ring-2 focus:ring-blue-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                        @error('amount')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Payment Method --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-widest">طريقة الدفع</label>
                        <select name="payment_method" required
                                class="w-full rounded-xl border border-slate-200 bg-white focus:border-blue-400 focus:ring-2 focus:ring-blue-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                            <option value="">— اختر الطريقة —</option>
                            <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>💵 نقدي</option>
                            <option value="bank" {{ old('payment_method') == 'bank' ? 'selected' : '' }}>🏦 بنك</option>
                            <option value="transfer" {{ old('payment_method') == 'transfer' ? 'selected' : '' }}>📲 تحويل</option>
                        </select>
                        @error('payment_method')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Wallet & Date --}}
                <div class="grid md:grid-cols-2 gap-5">
                    {{-- Wallet --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-widest">الخزينة</label>
                        <select name="wallet_id" id="walletSelect"
                                class="w-full rounded-xl border border-slate-200 bg-white focus:border-blue-400 focus:ring-2 focus:ring-blue-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                            <option value="">بدون خزينة</option>
                            @foreach($wallets as $wallet)
                            <option value="{{ $wallet->id }}" data-balance="{{ $wallet->balance }}"
                                    {{ old('wallet_id') == $wallet->id ? 'selected' : '' }}>
                                {{ $wallet->getTypeLabel() }} - {{ $wallet->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Date --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-widest">التاريخ</label>
                        <input type="date" name="date" required value="{{ old('date', date('Y-m-d')) }}"
                               class="w-full rounded-xl border border-slate-200 bg-white focus:border-blue-400 focus:ring-2 focus:ring-blue-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                        @error('date')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Wallet Balance Display --}}
                <div id="walletInfo" class="p-3 rounded-xl border border-green-200 bg-green-50 hidden">
                    <p class="text-xs font-bold text-green-700 mb-1">الرصيد المتاح</p>
                    <p class="text-xl font-black text-green-700"><span id="walletBalance">0</span> ج.م</p>
                </div>
            </div>
        </div>

        {{-- STEP 3: ADDITIONAL DATA --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-indigo-100 bg-gradient-to-b from-indigo-50 to-white">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center font-black text-sm flex-shrink-0">3</div>
                    <h6 class="text-base font-black text-indigo-900 m-0">بيانات إضافية</h6>
                </div>
            </div>

            <div class="px-6 py-6 space-y-5">
                {{-- Receipt Number --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-widest">رقم الإيصال (اختياري)</label>
                    <input type="text" name="receipt_number" placeholder="سيتم إنشاء رقم تلقائي..."
                           value="{{ old('receipt_number') }}"
                           class="w-full rounded-xl border border-slate-200 bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                    <p class="text-xs text-slate-500 mt-1.5">إذا لم تكتب رقم، سيتم إنشاء رقم تلقائي (RCP-2026-0001)</p>
                </div>

                {{-- Reference Number --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-widest">رقم مرجعي (اختياري)</label>
                    <input type="text" name="reference_number" placeholder="مثال: REF-2026-0001"
                           value="{{ old('reference_number') }}"
                           class="w-full rounded-xl border border-slate-200 bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                    <p class="text-xs text-slate-500 mt-1.5">رقم مرجعي للدفعة من العميل أو البنك</p>
                </div>

                {{-- File Attachments --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-widest">ارفاق ملفات</label>
                    <input type="file" name="attachments[]" multiple accept="image/*,.pdf"
                           id="attachmentsInput"
                           class="w-full rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-4 px-3 text-sm font-semibold text-slate-600 cursor-pointer transition-colors">
                    <p class="text-xs text-slate-500 mt-1.5">✓ الصيغ: صور (JPG, PNG, GIF)، PDF | الحد الأقصى: 5 ملفات × 5MB</p>

                    {{-- Files Preview --}}
                    <div id="filesPreview" class="mt-4 grid grid-cols-2 md:grid-cols-3 gap-3 hidden">
                        <!-- معاينة الملفات هنا -->
                    </div>
                </div>

                {{-- Notes --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-widest">ملاحظات</label>
                    <textarea name="notes" rows="3" placeholder="أضف أي ملاحظات إضافية..."
                              class="w-full rounded-xl border border-slate-200 bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors resize-none">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        {{-- ACTION BUTTONS --}}
        <div class="flex gap-3">
            <button type="submit"
                    class="flex-1 rounded-xl border-0 bg-emerald-600 hover:bg-emerald-700 text-white font-black py-3 transition-colors shadow-sm hover:shadow-md">
                ✅ تسجيل الدفعة
            </button>
            <button type="button" id="whatsappBtn"
                    class="flex-1 rounded-xl border-0 bg-teal-600 hover:bg-teal-700 text-white font-black py-3 transition-colors shadow-sm hover:shadow-md flex items-center justify-center gap-2">
                <span>📲</span> WhatsApp
            </button>
            <a href="{{ route('udhiya.collections.index') }}"
               class="px-6 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold py-3 transition-colors text-center">
                ← رجوع
            </a>
        </div>

    </div>

    {{-- ═══════════ RIGHT: SIDEBAR SUMMARY ═══════════ --}}
    <div class="w-full lg:w-80 flex-shrink-0">
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden lg:sticky lg:top-24">

            {{-- Summary Header --}}
            <div class="px-6 py-5 border-b border-emerald-100 bg-gradient-to-b from-emerald-50 to-white">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">📊</span>
                    <h6 class="text-base font-black text-slate-800 m-0">ملخص الدفعة</h6>
                </div>
            </div>

            {{-- Summary Content --}}
            <div class="px-6 py-5 space-y-4 text-sm">
                <div class="pb-4 border-b border-slate-100">
                    <p class="text-xs font-bold text-slate-400 uppercase mb-1.5">العميل</p>
                    <p class="font-bold text-slate-800 truncate" id="summaryCustomer">—</p>
                </div>

                <div class="pb-4 border-b border-slate-100">
                    <p class="text-xs font-bold text-slate-400 uppercase mb-1.5">الصك</p>
                    <p class="font-bold text-slate-800 truncate" id="summaryContract">—</p>
                </div>

                <div class="pb-4 border-b border-slate-100">
                    <p class="text-xs font-bold text-slate-400 uppercase mb-1.5">الطريقة</p>
                    <p class="font-bold text-slate-800" id="summaryMethod">—</p>
                </div>

                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-100 text-center">
                    <p class="text-xs font-bold text-emerald-600 uppercase mb-2">المبلغ</p>
                    <p class="text-3xl font-black text-emerald-700"><span id="summaryAmount">0</span></p>
                    <p class="text-xs text-emerald-600 mt-1">ج.م</p>
                </div>
            </div>

            {{-- Tip Box --}}
            <div class="px-6 py-5 border-t border-slate-100 bg-slate-50">
                <p class="text-xs font-bold text-slate-600 mb-2.5 uppercase">💡 نصائح</p>
                <ul class="space-y-1.5 text-xs text-slate-600">
                    <li class="flex gap-2 items-start">
                        <span class="text-emerald-600 font-bold flex-shrink-0">✓</span>
                        <span>اختر العميل والصك أولاً</span>
                    </li>
                    <li class="flex gap-2 items-start">
                        <span class="text-emerald-600 font-bold flex-shrink-0">✓</span>
                        <span>المبلغ لا يتجاوز المتبقي</span>
                    </li>
                    <li class="flex gap-2 items-start">
                        <span class="text-emerald-600 font-bold flex-shrink-0">✓</span>
                        <span>ارفاق الملفات اختياري</span>
                    </li>
                    <li class="flex gap-2 items-start">
                        <span class="text-emerald-600 font-bold flex-shrink-0">✓</span>
                        <span>الإيصال يُنشأ تلقائياً</span>
                    </li>
                </ul>
            </div>

        </div>
    </div>

</form>

{{-- Add Customer Form Modal --}}
<div id="customerFormContainer" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-8 shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-black text-slate-900">➕ عميل جديد</h2>
            <button type="button" id="closeCustomerForm" class="text-slate-400 hover:text-slate-600 text-2xl font-bold">✕</button>
        </div>

        <form id="customerForm" action="{{ route('udhiya.customers.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-widest">الاسم</label>
                    <input type="text" name="name" required placeholder="محمد أحمد"
                           class="w-full rounded-xl border border-slate-200 bg-white focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-widest">الهاتف</label>
                    <input type="tel" name="phone" required placeholder="01234567890"
                           class="w-full rounded-xl border border-slate-200 bg-white focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-widest">العنوان</label>
                    <input type="text" name="address" placeholder="القاهرة"
                           class="w-full rounded-xl border border-slate-200 bg-white focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-widest">ملاحظات</label>
                    <input type="text" name="notes" placeholder="ملاحظات..."
                           class="w-full rounded-xl border border-slate-200 bg-white focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                </div>
            </div>

            <div class="flex gap-3 pt-4 border-t border-slate-200">
                <button type="submit" class="flex-1 px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg transition">
                    ✅ إضافة
                </button>
                <button type="button" id="cancelCustomerForm" class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold rounded-lg transition">
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Quick Contract Modal --}}
<div id="contractFormContainer" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-8 shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-black text-slate-900">⚡ صك جديد بسرعة</h2>
            <button type="button" id="closeContractForm" class="text-slate-400 hover:text-slate-600 text-2xl font-bold">✕</button>
        </div>

        <form id="quickContractForm" class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-widest">العميل</label>
                <input type="text" id="contractCustomerName" readonly
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 px-3 text-sm font-semibold text-slate-600">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-widest">الإجمالي (ج.م) <span class="text-rose-600">*</span></label>
                <input type="number" id="contractAmount" required min="0.01" step="0.01" placeholder="0.00"
                       class="w-full rounded-xl border border-slate-200 bg-white focus:border-amber-400 focus:ring-2 focus:ring-amber-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-widest">النوع <span class="text-rose-600">*</span></label>
                <select id="contractShareType" required
                        class="w-full rounded-xl border border-slate-200 bg-white focus:border-amber-400 focus:ring-2 focus:ring-amber-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                    <option value="">— اختر —</option>
                    <option value="full">كامل</option>
                    <option value="half">نصف</option>
                    <option value="quarter">ربع</option>
                    <option value="third">ثلث</option>
                    <option value="six">سدس</option>
                    <option value="five">خمس</option>
                    <option value="seven">سبع</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-widest">ملاحظات</label>
                <textarea id="contractNotes" rows="2" placeholder="ملاحظات..."
                          class="w-full rounded-xl border border-slate-200 bg-white focus:border-amber-400 focus:ring-2 focus:ring-amber-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors resize-none"></textarea>
            </div>

            <div class="flex gap-3 pt-4 border-t border-slate-200">
                <button type="submit" class="flex-1 px-6 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-lg transition">
                    ✅ إنشاء
                </button>
                <button type="button" id="cancelContractForm" class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold rounded-lg transition">
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const customerSelect = document.getElementById('customerSelect');
    const contractSelect = document.getElementById('contractSelect');
    const amountInput = document.getElementById('amountInput');
    const walletSelect = document.getElementById('walletSelect');
    const paymentMethodSelect = document.querySelector('select[name="payment_method"]');
    const attachmentsInput = document.getElementById('attachmentsInput');
    const filesPreview = document.getElementById('filesPreview');
    const whatsappBtn = document.getElementById('whatsappBtn');

    // Customer Selection
    customerSelect.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        const contractsData = selected.getAttribute('data-contracts');

        contractSelect.innerHTML = '<option value="">— اختر الصك —</option>';
        document.getElementById('contractDetails').classList.add('hidden');
        document.getElementById('customerDetails').classList.add('hidden');
        document.getElementById('noContractsMessage').classList.add('hidden');
        amountInput.value = '';
        document.getElementById('summaryAmount').textContent = '0';

        let contracts = [];
        try {
            contracts = contractsData ? JSON.parse(contractsData) : [];
        } catch (e) {
            console.error('Error parsing contracts:', e);
            return;
        }

        const customerName = selected.textContent;
        const customerPhone = selected.getAttribute('data-phone') || '—';

        document.getElementById('customerName').textContent = customerName;
        document.getElementById('customerPhone').textContent = customerPhone;
        document.getElementById('customerContracts').textContent = contracts.length;
        document.getElementById('customerDetails').classList.remove('hidden');
        document.getElementById('summaryCustomer').textContent = customerName;

        if (contracts.length > 0) {
            contracts.forEach(contract => {
                const option = document.createElement('option');
                option.value = contract.id;
                option.textContent = `${contract.contract_number} — متبقي ${contract.remaining_amount} ج.م`;
                option.setAttribute('data-total', contract.total_amount);
                option.setAttribute('data-paid', contract.paid_amount);
                option.setAttribute('data-remaining', contract.remaining_amount);
                contractSelect.appendChild(option);
            });
        } else {
            document.getElementById('noContractsMessage').classList.remove('hidden');
            document.getElementById('contractCustomerName').value = customerName;
        }
    });

    // Contract Selection
    contractSelect.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];

        if (!selected || !selected.hasAttribute('data-total')) {
            document.getElementById('contractDetails').classList.add('hidden');
            document.getElementById('summaryContract').textContent = '—';
            amountInput.max = '';
            return;
        }

        const total = selected.getAttribute('data-total');
        const paid = selected.getAttribute('data-paid');
        const remaining = selected.getAttribute('data-remaining');

        document.getElementById('totalAmount').textContent = new Intl.NumberFormat('ar-EG').format(total);
        document.getElementById('paidAmount').textContent = new Intl.NumberFormat('ar-EG').format(paid);
        document.getElementById('remainingAmount').textContent = new Intl.NumberFormat('ar-EG').format(remaining);
        document.getElementById('contractDetails').classList.remove('hidden');
        amountInput.max = remaining;
        document.getElementById('summaryContract').textContent = selected.textContent.split(' —')[0];
    });

    // Amount Validation
    amountInput.addEventListener('input', function() {
        const max = parseFloat(this.max || 0);
        const value = parseFloat(this.value || 0);

        if (value > max && max > 0) {
            this.value = max;
        }

        document.getElementById('summaryAmount').textContent = new Intl.NumberFormat('ar-EG').format(this.value || 0);
    });

    // Payment Method
    paymentMethodSelect.addEventListener('change', function() {
        const methodText = {
            'cash': '💵 نقدي',
            'bank': '🏦 بنك',
            'transfer': '📲 تحويل'
        };
        document.getElementById('summaryMethod').textContent = methodText[this.value] || '—';
    });

    // Wallet Balance
    walletSelect.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        const balance = selected.getAttribute('data-balance');

        if (balance) {
            document.getElementById('walletBalance').textContent = new Intl.NumberFormat('ar-EG').format(balance);
            document.getElementById('walletInfo').classList.remove('hidden');
        } else {
            document.getElementById('walletInfo').classList.add('hidden');
        }
    });

    // Add Customer Form
    const toggleBtn = document.getElementById('toggleCustomerForm');
    const closeBtn = document.getElementById('closeCustomerForm');
    const cancelBtn = document.getElementById('cancelCustomerForm');
    const formContainer = document.getElementById('customerFormContainer');
    const customerForm = document.getElementById('customerForm');

    if (toggleBtn) {
        toggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            formContainer.classList.toggle('hidden');
        });
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', function(e) {
            e.preventDefault();
            formContainer.classList.add('hidden');
            customerForm.reset();
        });
    }

    if (cancelBtn) {
        cancelBtn.addEventListener('click', function(e) {
            e.preventDefault();
            formContainer.classList.add('hidden');
            customerForm.reset();
        });
    }

    // Customer Form Submit (AJAX)
    if (customerForm) {
        customerForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            try {
                const response = await fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    // Close modal
                    formContainer.classList.add('hidden');
                    customerForm.reset();

                    // Add new customer to dropdown
                    const newOption = document.createElement('option');
                    newOption.value = data.customer.id;
                    newOption.textContent = data.customer.name;
                    newOption.setAttribute('data-contracts', '[]');
                    newOption.setAttribute('data-phone', data.customer.phone || '');
                    newOption.selected = true;
                    customerSelect.appendChild(newOption);

                    // Trigger customer selection event to update form
                    const event = new Event('change', { bubbles: true });
                    customerSelect.dispatchEvent(event);

                    // Show success message
                    if (typeof showToast === 'function') {
                        showToast('success', 'تم إضافة العميل بنجاح');
                    }
                } else {
                    alert('حدث خطأ: ' + (data.message || 'فشل إضافة العميل'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('حدث خطأ في الإرسال');
            }
        });
    }

    // Close customer modal when clicking backdrop
    if (formContainer) {
        formContainer.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
                customerForm.reset();
            }
        });
    }

    // Quick Contract Form
    const createContractBtn = document.getElementById('createContractBtn');
    const contractFormContainer = document.getElementById('contractFormContainer');
    const closeContractBtn = document.getElementById('closeContractForm');
    const cancelContractBtn = document.getElementById('cancelContractForm');
    const quickContractForm = document.getElementById('quickContractForm');
    let quickContractCustomerId = null; // Store customer ID for quick contract

    if (createContractBtn) {
        createContractBtn.addEventListener('click', function(e) {
            e.preventDefault();
            // Store the customer ID when opening the modal
            quickContractCustomerId = customerSelect.value;
            contractFormContainer.classList.remove('hidden');
        });
    }

    if (closeContractBtn) {
        closeContractBtn.addEventListener('click', function(e) {
            e.preventDefault();
            contractFormContainer.classList.add('hidden');
            quickContractForm.reset();
        });
    }

    if (cancelContractBtn) {
        cancelContractBtn.addEventListener('click', function(e) {
            e.preventDefault();
            contractFormContainer.classList.add('hidden');
            quickContractForm.reset();
        });
    }

    if (contractFormContainer) {
        contractFormContainer.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
                quickContractForm.reset();
            }
        });
    }

    if (quickContractForm) {
        quickContractForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const customerId = quickContractCustomerId || customerSelect.value;
            const amount = document.getElementById('contractAmount').value;
            const shareType = document.getElementById('contractShareType').value;
            const notes = document.getElementById('contractNotes').value;

            if (!customerId || !amount || !shareType) {
                alert('يرجى ملء جميع الحقول المطلوبة');
                console.error('Missing fields:', { customerId, amount, shareType });
                return;
            }

            const payload = {
                customer_id: parseInt(customerId),
                total_amount: parseFloat(amount),
                share_type: shareType,
                notes: notes || ''
            };

            console.log('Sending contract data:', payload);

            try {
                const csrfElement = document.querySelector('meta[name="csrf-token"]');
                const csrfToken = csrfElement ? csrfElement.getAttribute('content') : '';

                if (!csrfToken) {
                    alert('خطأ في الأمان: لم يتم العثور على CSRF token');
                    console.error('CSRF token not found');
                    return;
                }

                const response = await fetch('{{ route("udhiya.contracts.quick") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (!response.ok) {
                    console.error('Contract creation error:', data);
                    alert('❌ خطأ: ' + (data.message || 'خطأ في إنشاء الصك'));
                    return;
                }

                const newContractId = data.contract.id;
                const newContractNumber = data.contract.contract_number;
                const newAmount = data.contract.total_amount;

                const option = document.createElement('option');
                option.value = newContractId;
                option.textContent = `${newContractNumber} — متبقي ${newAmount} ج.م`;
                option.setAttribute('data-total', newAmount);
                option.setAttribute('data-paid', 0);
                option.setAttribute('data-remaining', newAmount);
                contractSelect.appendChild(option);

                contractSelect.value = newContractId;
                contractSelect.dispatchEvent(new Event('change'));

                contractFormContainer.classList.add('hidden');
                document.getElementById('noContractsMessage').classList.add('hidden');
                quickContractForm.reset();

                alert('✅ تم إنشاء الصك بنجاح');
            } catch (error) {
                console.error('Error:', error);
                alert('خطأ في إنشاء الصك');
            }
        });
    }

    // File Attachments
    if (attachmentsInput) {
        attachmentsInput.addEventListener('change', function(e) {
            const files = this.files;
            if (files.length === 0) {
                filesPreview.classList.add('hidden');
                return;
            }

            filesPreview.innerHTML = '';
            Array.from(files).forEach((file) => {
                const isImage = file.type.startsWith('image/');
                const fileDiv = document.createElement('div');
                fileDiv.className = 'relative group';
                fileDiv.innerHTML = `
                    <div class="relative rounded-lg overflow-hidden bg-slate-100 aspect-square border border-slate-200">
                        ${isImage ? `
                            <img src="${URL.createObjectURL(file)}" alt="Preview" class="w-full h-full object-cover">
                        ` : `
                            <div class="w-full h-full flex items-center justify-center">
                                <span class="text-3xl">📄</span>
                            </div>
                        `}
                    </div>
                    <p class="text-xs text-slate-600 mt-1 truncate">${file.name}</p>
                `;
                filesPreview.appendChild(fileDiv);
            });

            filesPreview.classList.remove('hidden');
        });
    }

    // WhatsApp Button
    if (whatsappBtn) {
        whatsappBtn.addEventListener('click', function(e) {
            e.preventDefault();

            const customerPhone = document.getElementById('customerPhone').textContent;
            const customerName = document.getElementById('customerName').textContent;
            const contractNum = document.getElementById('summaryContract').textContent;
            const amount = document.getElementById('summaryAmount').textContent;
            const method = document.getElementById('summaryMethod').textContent;

            if (customerPhone === '—' || !customerPhone) {
                alert('❌ لم يتم اختيار عميل بعد');
                return;
            }

            const message = `🎯 *تفاصيل الدفعة*\n\n👤 الاسم: ${customerName}\n📄 الصك: ${contractNum}\n💰 المبلغ: ${amount} ج.م\n💳 الطريقة: ${method}\n📅 التاريخ: ${new Date().toLocaleDateString('ar-EG')}\n\nشكراً على التعاملك معنا!`;

            const encodedMessage = encodeURIComponent(message);
            const whatsappLink = `https://wa.me/2${customerPhone.replace(/^0/, '')}?text=${encodedMessage}`;

            window.open(whatsappLink, '_blank');
        });
    }
});
</script>
@endpush
