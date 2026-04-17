@extends('layouts.master')

@section('page-header')
<div class="mb-8">
    <h1 class="text-4xl font-black text-slate-900 mb-2">💳 تسجيل دفعة جديدة</h1>
    <nav class="flex items-center gap-2 text-slate-600 text-sm font-semibold">
        <a href="{{ route('udhiya.dashboard') }}" class="text-blue-600 hover:text-blue-800">الرئيسية</a>
        <span>/</span>
        <a href="{{ route('udhiya.collections.index') }}" class="text-blue-600 hover:text-blue-800">تحصيل الدفعات</a>
        <span>/</span>
        <span class="text-slate-500">دفعة جديدة</span>
    </nav>
</div>
@endsection

@section('content')

<div class="min-h-screen pb-20">

    {{-- Add Customer Form --}}
    <div id="customerFormContainer" class="bg-gradient-to-br from-blue-50 to-blue-100/80 backdrop-blur rounded-2xl p-8 border border-blue-200 mb-8 shadow-lg hidden">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-3xl font-black bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent">➕ إضافة عميل جديد</h2>
            <button type="button" id="closeCustomerForm" class="text-slate-400 hover:text-slate-600 text-3xl font-bold transition">✕</button>
        </div>

        <form id="customerForm" action="{{ route('udhiya.customers.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-wider">الاسم</label>
                    <input type="text" name="name" required placeholder="محمد أحمد"
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition font-semibold text-slate-800">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-wider">الهاتف</label>
                    <input type="tel" name="phone" required placeholder="01234567890"
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition font-semibold text-slate-800">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-wider">العنوان</label>
                    <input type="text" name="address" placeholder="القاهرة"
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition font-semibold text-slate-800">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-wider">ملاحظات</label>
                    <input type="text" name="notes" placeholder="ملاحظات..."
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition font-semibold text-slate-800">
                </div>
            </div>

            <div class="flex gap-3 pt-4 border-t border-slate-200">
                <button type="submit" class="flex-1 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition shadow-md hover:shadow-lg">
                    ✅ إضافة العميل
                </button>
                <button type="button" id="cancelCustomerForm" class="px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold rounded-xl transition">
                    إلغاء
                </button>
            </div>
        </form>
    </div>

    {{-- Main Payment Form --}}
    <form action="{{ route('udhiya.collections.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

            {{-- Main Form Content --}}
            <div class="xl:col-span-3 space-y-6">

                {{-- Customer & Contract Card --}}
                <div class="bg-gradient-to-br from-blue-50 to-cyan-50 backdrop-blur rounded-2xl p-6 shadow-sm hover:shadow-lg transition border border-blue-200/50">
                    <h3 class="text-2xl font-black bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent mb-6 flex items-center gap-2">
                        <span>👤</span> العميل والصك
                    </h3>

                    <div class="grid md:grid-cols-2 gap-4 mb-4">
                        {{-- Customer --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-wider">اختر العميل</label>
                            <select name="customer_id" required id="customerSelect"
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition font-semibold text-slate-800">
                                <option value="">اختر عميل</option>
                                @foreach($customers as $customer)
                                <option value="{{ $customer->id }}"
                                        data-contracts='@json($customer->contracts->where("remaining_amount", ">", 0)->values())'
                                        data-phone="{{ e($customer->phone) }}"
                                        {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                    {{ e($customer->name) }}
                                </option>
                                @endforeach
                            </select>
                            @error('customer_id')<p class="text-red-500 text-xs mt-2">{{ $message }}</p>@enderror
                        </div>

                        {{-- Contract --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-wider">اختر الصك</label>
                            <select name="contract_id" required id="contractSelect"
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition font-semibold text-slate-800">
                                <option value="">اختر الصك</option>
                            </select>
                            @error('contract_id')<p class="text-red-500 text-xs mt-2">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    {{-- Add Customer Button --}}
                    <button type="button" id="toggleCustomerForm" class="w-full px-4 py-3 text-sm bg-gradient-to-r from-blue-400 to-cyan-400 border  hover:from-blue-500 hover:to-cyan-500 text-dark font-bold rounded-xl transition shadow-md ">
                        + إضافة عميل جديد
                    </button>

                    {{-- No Contracts Alert --}}
                    <div id="noContractsMessage" class="mt-4 p-4 bg-red-50 rounded-xl border border-red-200 hidden">
                        <p class="text-red-700 font-semibold text-sm mb-3">⚠️ لا توجد صكوك متاحة لهذا العميل</p>
                        <button type="button" id="createContractBtn" class="w-full px-4 py-3 bg-gradient-to-r from-amber-600 to-amber-700 hover:from-amber-700 hover:to-amber-800 text-dark font-bold rounded-xl transition shadow-md">
                            ➕ إنشاء صك جديد الآن
                        </button>
                    </div>

                    {{-- Contract Details --}}
                    <div id="contractInfo" class="mt-4 p-4 bg-amber-50 rounded-xl border border-amber-200 hidden">
                        <div class="grid md:grid-cols-3 gap-4">
                            <div>
                                <p class="text-xs text-amber-600 font-bold mb-1">الإجمالي</p>
                                <p class="text-2xl font-black text-slate-900">
                                    <span id="totalAmount">0</span>
                                    <span class="text-xs text-amber-600 mr-1">ج.م</span>
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-amber-600 font-bold mb-1">المدفوع</p>
                                <p class="text-2xl font-black text-slate-900">
                                    <span id="paidAmount">0</span>
                                    <span class="text-xs text-amber-600 mr-1">ج.م</span>
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-amber-600 font-bold mb-1">المتبقي</p>
                                <p class="text-2xl font-black text-amber-600">
                                    <span id="remainingAmount">0</span>
                                    <span class="text-xs text-amber-600 mr-1">ج.م</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Payment Amount Card --}}
                <div class="bg-gradient-to-br from-emerald-50 to-green-50 backdrop-blur rounded-2xl p-6 shadow-sm hover:shadow-lg transition border border-emerald-200/50">
                    <h3 class="text-2xl font-black bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent mb-6 flex items-center gap-2">
                        <span>💰</span> المبلغ والطريقة
                    </h3>

                    <div class="grid md:grid-cols-2 gap-6">
                        {{-- Amount --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-wider">المبلغ (ج.م)</label>
                            <div class="relative">
                                <input type="number" name="amount" required min="0.01" step="0.01"
                                       id="amountInput" placeholder="0.00" value="{{ old('amount') }}"
                                       class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition font-semibold text-slate-800 text-xl">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">ج.م</span>
                            </div>
                            @error('amount')<p class="text-red-500 text-xs mt-2">{{ $message }}</p>@enderror
                        </div>

                        {{-- Payment Method --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-wider">طريقة الدفع</label>
                            <select name="payment_method" required
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition font-semibold text-slate-800">
                                <option value="">اختر الطريقة</option>
                                <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>💵 نقدي</option>
                                <option value="bank" {{ old('payment_method') == 'bank' ? 'selected' : '' }}>🏦 بنك</option>
                                <option value="transfer" {{ old('payment_method') == 'transfer' ? 'selected' : '' }}>📲 تحويل</option>
                            </select>
                            @error('payment_method')<p class="text-red-500 text-xs mt-2">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- Wallet & Date Card --}}
                <div class="bg-gradient-to-br from-purple-50 to-pink-50 backdrop-blur rounded-2xl p-6 shadow-sm hover:shadow-lg transition border border-purple-200/50">
                    <h3 class="text-2xl font-black bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent mb-6 flex items-center gap-2">
                        <span>📅</span> الخزينة والتاريخ
                    </h3>

                    <div class="grid md:grid-cols-2 gap-6">
                        {{-- Wallet --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-wider">الخزينة</label>
                            <select name="wallet_id" id="walletSelect"
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition font-semibold text-slate-800">
                                <option value="">بدون خزينة</option>
                                @foreach($wallets as $wallet)
                                <option value="{{ $wallet->id }}" data-balance="{{ $wallet->balance }}"
                                        {{ old('wallet_id') == $wallet->id ? 'selected' : '' }}>
                                    {{ $wallet->getTypeLabel() }} - {{ $wallet->name }}
                                </option>
                                @endforeach
                            </select>

                            {{-- Wallet Balance --}}
                            <div id="walletInfo" class="mt-3 p-3 bg-green-50 rounded-lg border border-green-200 hidden">
                                <p class="text-xs text-green-600 font-bold mb-1">الرصيد</p>
                                <p class="text-xl font-black text-green-700"><span id="walletBalance">0</span> ج.م</p>
                            </div>
                        </div>

                        {{-- Date --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-wider">التاريخ</label>
                            <input type="date" name="date" required value="{{ old('date', date('Y-m-d')) }}"
                                   class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition font-semibold text-slate-800">
                            @error('date')<p class="text-red-500 text-xs mt-2">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- Notes Card --}}
                <div class="bg-gradient-to-br from-orange-50 to-amber-50 backdrop-blur rounded-2xl p-6 shadow-sm hover:shadow-lg transition border border-orange-200/50">
                    <h3 class="text-2xl font-black bg-gradient-to-r from-orange-600 to-amber-600 bg-clip-text text-transparent mb-6 flex items-center gap-2">
                        <span>📝</span> ملاحظات
                    </h3>
                    <textarea name="notes" rows="3" placeholder="أضف أي ملاحظات إضافية..."
                              class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition font-semibold text-slate-800 resize-none">{{ old('notes') }}</textarea>
                </div>

                {{-- Back Button --}}
                <a href="{{ route('udhiya.collections.index') }}" class="block px-6 py-3 bg-gradient-to-r from-slate-300 to-slate-400 hover:from-slate-400 hover:to-slate-500 text-dark border font-bold rounded-xl transition text-center shadow-md hover:shadow-lg">
                    ← العودة للقائمة
                </a>

            </div>

            {{-- Sticky Summary Sidebar --}}
            <div class="xl:col-span-1">
                <div class="sticky top-28 space-y-4">

                    {{-- Summary Card --}}
                    <div class="bg-gradient-to-br from-blue-600 via-purple-600 to-pink-600 text-dark border rounded-2xl p-6 shadow-xl hover:shadow-2xl transition">
                        <h3 class="font-black text-2xl mb-6 flex items-center gap-2">
                            <span>📊</span> الملخص
                        </h3>

                        <div class="space-y-4 text-sm">
                            <div>
                                <p class="text-blue-200 text-xs font-bold uppercase tracking-wider mb-1">العميل</p>
                                <p class="font-bold text-base truncate" id="summaryCustomer">—</p>
                            </div>

                            <div>
                                <p class="text-blue-200 text-xs font-bold uppercase tracking-wider mb-1">الصك</p>
                                <p class="font-bold text-base truncate" id="summaryContract">—</p>
                            </div>

                            <div>
                                <p class="text-blue-200 text-xs font-bold uppercase tracking-wider mb-1">الطريقة</p>
                                <p class="font-bold text-base" id="summaryMethod">—</p>
                            </div>

                            <div class="pt-4 border-t border-blue-400">
                                <p class="text-blue-200 text-xs font-bold uppercase tracking-wider mb-2">المبلغ</p>
                                <p class="text-5xl font-black" id="summaryAmount">0</p>
                                <p class="text-blue-200 text-xs mt-1">ج.م</p>
                            </div>
                        </div>
                    </div>

                    {{-- Customer Info Card --}}
                    <div id="customerInfoCard" class="bg-gradient-to-br from-indigo-50 to-blue-50 backdrop-blur rounded-2xl p-6 shadow-sm border border-indigo-200/50 hidden">
                        <div class="space-y-3 text-sm">
                            <div>
                                <p class="text-slate-500 text-xs font-bold uppercase tracking-wider mb-1">الاسم</p>
                                <p class="font-bold text-slate-900" id="customerName">—</p>
                            </div>
                            <div>
                                <p class="text-slate-500 text-xs font-bold uppercase tracking-wider mb-1">الهاتف</p>
                                <p class="font-bold text-slate-900" id="customerPhone">—</p>
                            </div>
                            <div>
                                <p class="text-slate-500 text-xs font-bold uppercase tracking-wider mb-1">الصكوك</p>
                                <p class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-blue-100 text-blue-700 text-sm font-bold" id="customerContracts">0</p>
                            </div>
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit" class="w-full py-4 bg-gradient-to-r from-green-500 via-emerald-500 border to-teal-600 hover:from-green-600 hover:via-emerald-600 hover:to-teal-700 text-dark font-black text-lg rounded-2xl shadow-lg hover:shadow-xl hover:scale-105 transition transform">
                        ✅ تسجيل الدفعة
                    </button>

                </div>
            </div>

        </div>

    </form>

</div>

{{-- Create Contract Modal --}}
<div id="contractFormContainer" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-gradient-to-br from-amber-50 to-orange-100 rounded-2xl p-8 shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto animate-scale-in border border-amber-200">
        <div class="flex items-center justify-between mb-6 pb-6 border-b-2 border-amber-300">
            <h2 class="text-3xl font-black bg-gradient-to-r from-amber-600 to-orange-600 bg-clip-text text-transparent flex items-center gap-2">
                <span>⚡</span> صك بسرعة
            </h2>
            <button type="button" id="closeContractForm" class="text-amber-400 hover:text-amber-600 text-3xl font-bold transition">✕</button>
        </div>

        <form id="quickContractForm" class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-wider">العميل</label>
                <input type="text" id="contractCustomerName" readonly
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 font-semibold">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-wider">
                    الإجمالي (ج.م) <span class="text-red-600">*</span>
                </label>
                <input type="number" id="contractAmount" required min="0.01" step="0.01" placeholder="0.00"
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition font-semibold text-slate-800 text-xl">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-wider">
                    نوع المشاركة <span class="text-red-600">*</span>
                </label>
                <select id="contractShareType" required
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition font-semibold text-slate-800">
                    <option value="">اختر النوع</option>
                    <option value="full">🐑 كامل</option>
                    <option value="half">نصف</option>
                    <option value="quarter">ربع</option>
                    <option value="third">ثلث</option>
                    <option value="six">سدس</option>
                    <option value="five">خمس</option>
                    <option value="seven">سبع</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-wider">ملاحظات</label>
                <textarea id="contractNotes" rows="3" placeholder="ملاحظات (اختيارية)..."
                          class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition font-semibold text-slate-800 resize-none"></textarea>
            </div>

            <div class="pt-4 space-y-3 border-t border-slate-200">
                <button type="submit" class="w-full px-6 py-3 bg-gradient-to-r from-amber-600 to-amber-700 hover:from-amber-700 hover:to-amber-800 text-white font-bold rounded-xl transition shadow-md hover:shadow-lg">
                    ✅ إنشاء الصك
                </button>
                <button type="button" id="cancelContractForm" class="w-full px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold rounded-xl transition">
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('js')
<style>
    @keyframes scaleIn {
        from {
            transform: scale(0.9);
            opacity: 0;
        }
        to {
            transform: scale(1);
            opacity: 1;
        }
    }

    .animate-scale-in {
        animation: scaleIn 0.2s ease;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const customerSelect = document.getElementById('customerSelect');
    const contractSelect = document.getElementById('contractSelect');
    const amountInput = document.getElementById('amountInput');
    const walletSelect = document.getElementById('walletSelect');
    const paymentMethodSelect = document.querySelector('select[name="payment_method"]');
    const customerInfoCard = document.getElementById('customerInfoCard');

    // ✅ 1. Customer Selection
    customerSelect.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        const contractsData = selected.getAttribute('data-contracts');

        contractSelect.innerHTML = '<option value="">اختر الصك</option>';
        document.getElementById('contractInfo').classList.add('hidden');
        customerInfoCard.classList.add('hidden');
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
        customerInfoCard.classList.remove('hidden');
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

    // ✅ 2. Contract Selection
    contractSelect.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];

        if (!selected || !selected.hasAttribute('data-total')) {
            document.getElementById('contractInfo').classList.add('hidden');
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
        document.getElementById('contractInfo').classList.remove('hidden');
        amountInput.max = remaining;
        document.getElementById('summaryContract').textContent = selected.textContent.split(' —')[0];
    });

    // ✅ 3. Amount Input Validation
    amountInput.addEventListener('input', function() {
        const max = parseFloat(this.max || 0);
        const value = parseFloat(this.value || 0);

        if (value > max && max > 0) {
            this.value = max;
        }

        document.getElementById('summaryAmount').textContent = new Intl.NumberFormat('ar-EG').format(this.value || 0);
    });

    // ✅ 4. Payment Method
    paymentMethodSelect.addEventListener('change', function() {
        const methodText = {
            'cash': '💵 نقدي',
            'bank': '🏦 بنك',
            'transfer': '📲 تحويل'
        };
        document.getElementById('summaryMethod').textContent = methodText[this.value] || '—';
    });

    // ✅ 5. Wallet Balance
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

    // ✅ 6. Toggle Customer Form
    const toggleBtn = document.getElementById('toggleCustomerForm');
    const closeBtn = document.getElementById('closeCustomerForm');
    const cancelBtn = document.getElementById('cancelCustomerForm');
    const formContainer = document.getElementById('customerFormContainer');
    const customerForm = document.getElementById('customerForm');

    if (toggleBtn) {
        toggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            formContainer.classList.toggle('hidden');
            if (!formContainer.classList.contains('hidden')) {
                const firstInput = formContainer.querySelector('input[name="name"]');
                if (firstInput) firstInput.focus();
            }
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

    // ✅ 7. Contract Form Toggle & Handling
    const createContractBtn = document.getElementById('createContractBtn');
    const contractFormContainer = document.getElementById('contractFormContainer');
    const closeContractBtn = document.getElementById('closeContractForm');
    const cancelContractBtn = document.getElementById('cancelContractForm');
    const quickContractForm = document.getElementById('quickContractForm');

    if (createContractBtn) {
        createContractBtn.addEventListener('click', function(e) {
            e.preventDefault();
            contractFormContainer.classList.remove('hidden');
            document.getElementById('contractAmount').focus();
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

    // Close modal when clicking outside
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

            const customerId = customerSelect.value;
            const amount = document.getElementById('contractAmount').value;
            const shareType = document.getElementById('contractShareType').value;
            const notes = document.getElementById('contractNotes').value;

            if (!customerId || !amount || !shareType) {
                alert('يرجى ملء جميع الحقول المطلوبة');
                return;
            }

            try {
                const response = await fetch('{{ route("udhiya.contracts.quick") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        customer_id: customerId,
                        total_amount: parseFloat(amount),
                        share_type: shareType,
                        notes: notes
                    })
                });

                if (!response.ok) {
                    const data = await response.json();
                    alert(data.message || 'حدث خطأ في إنشاء الصك');
                    return;
                }

                const data = await response.json();
                const newContractId = data.contract.id;
                const newContractNumber = data.contract.contract_number;

                // Add new contract to dropdown
                const option = document.createElement('option');
                option.value = newContractId;
                option.textContent = `${newContractNumber} — متبقي ${amount} ج.م`;
                option.setAttribute('data-total', amount);
                option.setAttribute('data-paid', 0);
                option.setAttribute('data-remaining', amount);
                contractSelect.appendChild(option);

                // Select the new contract
                contractSelect.value = newContractId;
                contractSelect.dispatchEvent(new Event('change'));

                // Hide form and message
                contractFormContainer.classList.add('hidden');
                document.getElementById('noContractsMessage').classList.add('hidden');
                quickContractForm.reset();

                // Show success message
                alert('✅ تم إنشاء الصك بنجاح');
            } catch (error) {
                console.error('Error:', error);
                alert('حدث خطأ في إنشاء الصك');
            }
        });
    }
});
</script>
@endpush
