@extends('layouts.master')
@section('page-header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 mb-8 mt-2">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
            <span class="text-indigo-600 text-4xl">💰</span> إضافة سلفة جديدة
        </h1>
        <p class="text-slate-500 font-medium text-sm mt-1">
            <a href="{{ route('udhiya.advances.index') }}" class="text-indigo-500 hover:text-indigo-700 hover:underline">السلف</a>
            / جديدة
        </p>
    </div>
</div>
@endsection

@section('content')
<form action="{{ route('udhiya.advances.store') }}" method="POST" class="flex flex-col lg:flex-row gap-6 pb-16">
    @csrf

    {{-- Right Sidebar --}}
    <div class="w-full lg:w-80">
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden lg:sticky lg:top-24 space-y-6">
            {{-- Type Selection --}}
            <div class="px-6 py-5 border-b border-slate-100">
                <label class="block text-sm font-black text-slate-800 mb-4">نوع السلفة <span class="text-rose-500">*</span></label>
                <div class="space-y-3">
                    <label class="flex items-center p-3 rounded-xl border-2 border-slate-200 cursor-pointer hover:border-indigo-400 hover:bg-indigo-50 transition-all"
                           onclick="switchType('customer')">
                        <input type="radio" name="type" value="customer" required id="typeCustomer" class="w-4 h-4">
                        <span class="mr-3 text-base font-black text-slate-700">👤 سلفة عميل</span>
                    </label>
                    <label class="flex items-center p-3 rounded-xl border-2 border-slate-200 cursor-pointer hover:border-indigo-400 hover:bg-indigo-50 transition-all"
                           onclick="switchType('supplier')">
                        <input type="radio" name="type" value="supplier" required id="typeSupplier" class="w-4 h-4">
                        <span class="mr-3 text-base font-black text-slate-700">🏢 سلفة مورد</span>
                    </label>
                </div>
                @error('type')<p class="text-rose-500 text-xs font-bold mt-2">{{ $message }}</p>@enderror
            </div>

            {{-- Name Selection --}}
            <div id="customerSection" style="display:none;" class="px-6 py-5 border-b border-slate-100">
                <label class="block text-xs font-bold text-slate-600 mb-2">العميل <span class="text-rose-500">*</span></label>
                <select name="customer_id" id="customerSelect" class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                    <option value="">-- اختر العميل --</option>
                    @foreach($customers as $c)
                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
                @error('customer_id')<p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p>@enderror
            </div>

            <div id="supplierSection" style="display:none;" class="px-6 py-5 border-b border-slate-100">
                <label class="block text-xs font-bold text-slate-600 mb-2">المورد <span class="text-rose-500">*</span></label>
                <select name="supplier_id" id="supplierSelect" class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                    <option value="">-- اختر المورد --</option>
                    @foreach($suppliers as $s)
                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
                @error('supplier_id')<p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Amount & Wallet --}}
            <div class="px-6 py-5 space-y-4 border-b border-slate-100">
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-2">المبلغ <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <input type="number" name="amount" step="0.01" min="0.01" required value="{{ old('amount') }}"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 pl-12 text-sm font-black text-slate-800 transition-colors">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 text-xs font-bold">ج.م</span>
                    </div>
                    @error('amount')<p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-2">الخزينة <span class="text-rose-500">*</span></label>
                    <select name="wallet_id" required class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                        <option value="">-- اختر الخزينة --</option>
                        @foreach($wallets as $w)
                        <option value="{{ $w->id }}">{{ $w->getTypeLabel() }} - {{ $w->name }}</option>
                        @endforeach
                    </select>
                    @error('wallet_id')<p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-2">التاريخ <span class="text-rose-500">*</span></label>
                    <input type="date" name="date" required value="{{ old('date', date('Y-m-d')) }}"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                    @error('date')<p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Notes --}}
            <div class="px-6 py-5 border-b border-slate-100">
                <label class="block text-xs font-bold text-slate-600 mb-2">ملاحظات</label>
                <textarea name="notes" rows="3" placeholder="ملاحظات إضافية..."
                          class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors resize-none">{{ old('notes') }}</textarea>
            </div>

            {{-- Submit --}}
            <div class="px-6 py-5 bg-slate-50/80 space-y-3">
                <button type="submit" class="w-full rounded-xl bg-indigo-600 text-white font-black py-3 hover:bg-indigo-700 shadow-lg shadow-indigo-200/60 transition-all">
                    ✅ إضافة السلفة
                </button>
                <a href="{{ route('udhiya.advances.index') }}" class="w-full block text-center rounded-xl bg-white text-slate-600 font-bold py-2.5 border border-slate-200 hover:bg-slate-50 transition-colors">
                    إلغاء
                </a>
            </div>
        </div>
    </div>

    {{-- Info Section --}}
    <div class="flex-1 min-w-0">
        <div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-3xl p-8 border border-indigo-200">
            <h3 class="text-lg font-black text-indigo-900 mb-4">📋 شروط السلفة</h3>
            <ul class="space-y-3 text-sm text-indigo-800">
                <li class="flex items-start gap-3">
                    <span class="text-xl">✓</span>
                    <span>تُخصم السلفة من الخزينة المحددة فوراً</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="text-xl">✓</span>
                    <span>يتم تتبع جميع المتحصلات والمردودات</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="text-xl">✓</span>
                    <span>عند سداد كامل السلفة، تُغلق السلفة تلقائياً</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="text-xl">✓</span>
                    <span>يمكن إضافة عمليات متعددة للسلفة الواحدة</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="text-xl">✓</span>
                    <span>جميع الخزائن مدعومة (نقدي، بنك، محفظة)</span>
                </li>
            </ul>
        </div>
    </div>
</form>

<script>
function switchType(type) {
    document.getElementById('customerSection').style.display = type === 'customer' ? '' : 'none';
    document.getElementById('supplierSection').style.display = type === 'supplier' ? '' : 'none';
}

// Initialize based on old input
if ('{{ old('type') }}' === 'customer') {
    document.getElementById('typeCustomer').checked = true;
    switchType('customer');
} else if ('{{ old('type') }}' === 'supplier') {
    document.getElementById('typeSupplier').checked = true;
    switchType('supplier');
}
</script>
@endsection
