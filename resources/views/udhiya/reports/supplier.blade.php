@extends('layouts.master')
@section('title', 'كشف حساب: ' . $supplier->name)

@section('page-header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 mb-8 mt-2">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
            <span class="text-indigo-600 text-4xl">🏭</span> كشف حساب مورد
        </h1>
        <p class="text-slate-500 font-medium text-sm mt-1">
            <a href="{{ route('udhiya.reports.index') }}" class="text-indigo-500 hover:text-indigo-700 hover:underline">التقارير</a>
            / {{ $supplier->name }}
        </p>
    </div>
    <div class="flex items-center gap-3 no-print">
        @if($balance > 0)
        <button type="button" onclick="document.getElementById('payModal').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 shadow-md shadow-emerald-200 transition-all">
            💰 تسجيل دفعة
        </button>
        @endif
        <button onclick="window.print()"
                class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 shadow-sm">
            🖨️ طباعة
        </button>
    </div>
</div>
@endsection

@section('content')

{{-- Print-only header --}}
<div class="print-only" style="display:none; direction:rtl;">
    <table style="width:100%; border:none; margin-bottom:14px;">
        <tr>
            <td style="border:none; padding:0; vertical-align:top;">
                <div style="font-size:20px; font-weight:900; color:#1e293b; margin:0 0 3px;">كشف حساب مورد</div>
                <div style="font-size:14px; font-weight:700; color:#334155;">{{ $supplier->name }}</div>
                @if($supplier->phone)
                <div style="font-size:11px; color:#64748b; direction:ltr; text-align:right;">{{ $supplier->phone }}</div>
                @endif
                @if($supplier->address)
                <div style="font-size:11px; color:#64748b;">{{ $supplier->address }}</div>
                @endif
            </td>
            <td style="border:none; padding:0; vertical-align:top; text-align:left; white-space:nowrap; font-size:11px; color:#64748b;">
                تاريخ الطباعة: {{ now()->format('Y/m/d') }}
            </td>
        </tr>
    </table>
    <table style="width:100%; border-collapse:collapse; margin-bottom:16px; background:#f8fafc;">
        <tr>
            <td style="border:1px solid #e2e8f0; padding:8px 12px; font-size:12px; font-weight:700; text-align:center;">
                إجمالي المشتريات<br><strong style="font-size:15px; color:#1e293b;">{{ number_format($totalPurchases, 0) }} ج.م</strong>
            </td>
            <td style="border:1px solid #e2e8f0; padding:8px 12px; font-size:12px; font-weight:700; text-align:center;">
                المدفوع<br><strong style="font-size:15px; color:#16a34a;">{{ number_format($totalPaid, 0) }} ج.م</strong>
            </td>
            <td style="border:1px solid #e2e8f0; padding:8px 12px; font-size:12px; font-weight:700; text-align:center;">
                المتبقي<br><strong style="font-size:15px; color:{{ $balance > 0 ? '#dc2626' : '#16a34a' }};">{{ number_format($balance, 0) }} ج.م</strong>
            </td>
            <td style="border:1px solid #e2e8f0; padding:8px 12px; font-size:12px; font-weight:700; text-align:center;">
                عدد الفواتير<br><strong style="font-size:15px; color:#4f46e5;">{{ $supplier->purchases->count() }}</strong>
            </td>
        </tr>
    </table>
    <hr style="border:0; border-top:2px solid #1e293b; margin-bottom:16px;">
</div>

{{-- ===== Supplier Info + Stats ===== --}}
<div class="flex flex-col lg:flex-row gap-6 mb-8">

    {{-- Supplier Card --}}
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden w-full lg:w-80 flex-shrink-0">
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
            <h6 class="text-base font-black text-slate-800 m-0">بيانات المورد</h6>
        </div>
        <div class="p-6 flex flex-col gap-4">
            {{-- Avatar --}}
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center font-black text-2xl border border-indigo-200 flex-shrink-0">
                    {{ mb_substr($supplier->name, 0, 1) }}
                </div>
                <div>
                    <div class="text-lg font-black text-slate-800">{{ $supplier->name }}</div>
                    @if($supplier->phone)
                    <div class="text-sm font-semibold text-slate-500 mt-0.5" dir="ltr">{{ $supplier->phone }}</div>
                    @endif
                </div>
            </div>

            @if($supplier->address)
            <div class="flex items-start gap-2 text-sm text-slate-600">
                <span class="text-slate-400 mt-0.5">📍</span>
                <span class="font-semibold">{{ $supplier->address }}</span>
            </div>
            @endif

            <div class="pt-3 border-t border-slate-100 text-xs text-slate-400 font-semibold">
                عميل منذ {{ $supplier->created_at->format('Y/m/d') }}
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="flex-1 grid grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Purchases --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 flex flex-col justify-between hover:shadow-md transition-shadow">
            <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-lg mb-4">🛒</div>
            <div>
                <div class="text-2xl font-black text-slate-800">{{ number_format($totalPurchases, 0) }}</div>
                <div class="text-xs font-bold text-slate-500 mt-1">إجمالي المشتريات (ج.م)</div>
            </div>
        </div>

        {{-- Paid --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 flex flex-col justify-between hover:shadow-md transition-shadow">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-lg mb-4">✅</div>
            <div>
                <div class="text-2xl font-black text-emerald-600">{{ number_format($totalPaid, 0) }}</div>
                <div class="text-xs font-bold text-slate-500 mt-1">المدفوع (ج.م)</div>
            </div>
        </div>

        {{-- Balance --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 flex flex-col justify-between hover:shadow-md transition-shadow">
            <div class="w-10 h-10 rounded-xl {{ $balance > 0 ? 'bg-rose-100 text-rose-600' : 'bg-emerald-100 text-emerald-600' }} flex items-center justify-center text-lg mb-4">
                {{ $balance > 0 ? '⚠️' : '✔️' }}
            </div>
            <div>
                <div class="text-2xl font-black {{ $balance > 0 ? 'text-rose-600' : 'text-emerald-600' }}">{{ number_format($balance, 0) }}</div>
                <div class="text-xs font-bold text-slate-500 mt-1">المتبقي (ج.م)</div>
            </div>
        </div>

        {{-- Purchases count --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 flex flex-col justify-between hover:shadow-md transition-shadow">
            <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-lg mb-4">📦</div>
            <div>
                <div class="text-2xl font-black text-slate-800">{{ $supplier->purchases->count() }}</div>
                <div class="text-xs font-bold text-slate-500 mt-1">عدد فواتير الشراء</div>
            </div>
        </div>
    </div>
</div>

{{-- ===== Purchases Table ===== --}}
<div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-md transition-shadow duration-300">
    <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
        <h6 class="text-base font-black text-slate-800 m-0">
            فواتير الشراء
            @if($supplier->purchases->count())
            <span class="inline-flex items-center rounded-lg px-2.5 py-1 text-xs font-bold bg-indigo-100 text-indigo-700 mr-2">
                {{ $supplier->purchases->count() }}
            </span>
            @endif
        </h6>
        @if($balance > 0)
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-black bg-rose-50 text-rose-700 border border-rose-100">
            ⚠️ متبقي: {{ number_format($balance, 0) }} ج.م
        </span>
        @endif
    </div>

    @if($supplier->purchases->count())
    <div class="overflow-x-auto">
        <table class="w-full text-right border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs font-bold">
                    <th class="px-5 py-4">#</th>
                    <th class="px-5 py-4">رقم الفاتورة</th>
                    <th class="px-5 py-4">التاريخ</th>
                    <th class="px-5 py-4">الحيوانات</th>
                    <th class="px-5 py-4 text-center">الإجمالي</th>
                    <th class="px-5 py-4 text-center">المدفوع</th>
                    <th class="px-5 py-4 text-center">المتبقي</th>
                    <th class="px-5 py-4 text-center">الحالة</th>
                    <th class="px-5 py-4 no-print"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                @foreach($supplier->purchases as $i => $purchase)
                @php $remaining = $purchase->total - $purchase->paid; @endphp
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-5 py-4 text-slate-400 font-bold text-xs">{{ $i + 1 }}</td>

                    <td class="px-5 py-4 font-black text-slate-800 text-xs" dir="ltr">
                        {{ $purchase->reference_number ?? '—' }}
                    </td>

                    <td class="px-5 py-4 text-slate-600 font-semibold text-xs whitespace-nowrap">
                        {{ \Carbon\Carbon::parse($purchase->date)->format('Y/m/d') }}
                    </td>

                    <td class="px-5 py-4">
                        <div class="flex flex-wrap gap-1">
                            @forelse($purchase->items as $item)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100 whitespace-nowrap">
                                {{ $item->product?->name ?? '—' }}
                            </span>
                            @empty
                            <span class="text-slate-300 text-xs">—</span>
                            @endforelse
                        </div>
                    </td>

                    <td class="px-5 py-4 text-center font-black text-slate-800 whitespace-nowrap">
                        {{ number_format($purchase->total, 0) }} ج.م
                    </td>

                    <td class="px-5 py-4 text-center font-bold text-emerald-600 whitespace-nowrap">
                        {{ number_format($purchase->paid, 0) }} ج.م
                    </td>

                    <td class="px-5 py-4 text-center font-bold whitespace-nowrap {{ $remaining > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                        {{ number_format($remaining, 0) }} ج.م
                    </td>

                    <td class="px-5 py-4 text-center">
                        @if($remaining <= 0)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-xs font-black bg-emerald-50 text-emerald-700 border border-emerald-100">
                            مسدد ✓
                        </span>
                        @else
                        <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-xs font-black bg-rose-50 text-rose-700 border border-rose-100">
                            متبقي
                        </span>
                        @endif
                    </td>

                    <td class="px-5 py-4 no-print">
                        <a href="{{ route('udhiya.purchases.show', $purchase) }}"
                           class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white flex items-center justify-center transition-colors text-xs" title="عرض الفاتورة">
                            👁
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>

            {{-- Footer totals --}}
            <tfoot class="bg-slate-50 border-t-2 border-slate-200 text-sm font-black">
                <tr>
                    <td colspan="4" class="px-5 py-4 text-slate-600">الإجمالي</td>
                    <td class="px-5 py-4 text-center text-slate-800">{{ number_format($totalPurchases, 0) }} ج.م</td>
                    <td class="px-5 py-4 text-center text-emerald-700">{{ number_format($totalPaid, 0) }} ج.م</td>
                    <td class="px-5 py-4 text-center {{ $balance > 0 ? 'text-rose-700' : 'text-emerald-700' }}">{{ number_format($balance, 0) }} ج.م</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>
    </div>

    @else
    <div class="flex flex-col items-center justify-center py-20 text-center">
        <div class="text-5xl mb-4">📦</div>
        <h5 class="text-lg font-black text-slate-600 mb-2">لا توجد فواتير شراء</h5>
        <p class="text-slate-400 text-sm">لم يتم تسجيل أي مشتريات لهذا المورد بعد</p>
        <a href="{{ route('udhiya.purchases.create') }}"
           class="mt-6 inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 shadow-md shadow-indigo-200 transition-all">
            ＋ إضافة فاتورة شراء
        </a>
    </div>
    @endif
</div>

{{-- ===== Payments History ===== --}}
@if($supplier->payments->count())
<div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-md transition-shadow duration-300 mt-8">
    <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
        <h6 class="text-base font-black text-slate-800 m-0">
            سجل الدفعات
            <span class="inline-flex items-center rounded-lg px-2.5 py-1 text-xs font-bold bg-emerald-100 text-emerald-700 mr-2">
                {{ $supplier->payments->count() }}
            </span>
        </h6>
        <span class="text-sm font-black text-emerald-700">إجمالي المدفوع: {{ number_format($supplier->payments->sum('amount'), 0) }} ج.م</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-right border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs font-bold">
                    <th class="px-5 py-4">#</th>
                    <th class="px-5 py-4">تاريخ الدفع</th>
                    <th class="px-5 py-4">الفاتورة</th>
                    <th class="px-5 py-4 text-center">المبلغ</th>
                    <th class="px-5 py-4">ملاحظات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                @foreach($supplier->payments as $i => $payment)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-5 py-4 text-slate-400 font-bold text-xs">{{ $i + 1 }}</td>
                    <td class="px-5 py-4 font-bold text-slate-800 whitespace-nowrap">
                        {{ $payment->paid_at->format('Y/m/d') }}
                    </td>
                    <td class="px-5 py-4 text-xs">
                        <a href="{{ route('udhiya.purchases.show', $payment->purchase_id) }}"
                           class="font-bold text-indigo-600 hover:underline">
                            فاتورة #{{ $payment->purchase_id }}
                            @if($payment->purchase?->date)
                            <span class="text-slate-400 font-normal"> — {{ \Carbon\Carbon::parse($payment->purchase->date)->format('Y/m/d') }}</span>
                            @endif
                        </a>
                    </td>
                    <td class="px-5 py-4 text-center font-black text-emerald-600 whitespace-nowrap">
                        {{ number_format($payment->amount, 0) }} ج.م
                    </td>
                    <td class="px-5 py-4 text-slate-500 text-xs">{{ $payment->notes ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-slate-50 border-t-2 border-slate-200 text-sm font-black">
                <tr>
                    <td colspan="3" class="px-5 py-4 text-slate-600">الإجمالي</td>
                    <td class="px-5 py-4 text-center text-emerald-700">{{ number_format($supplier->payments->sum('amount'), 0) }} ج.م</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endif

{{-- ===== Pay Modal ===== --}}
@if($balance > 0)
@php $unpaidPurchases = $supplier->purchases->filter(fn($p) => $p->total > $p->paid); @endphp
<div id="payModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 no-print">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="document.getElementById('payModal').classList.add('hidden')"></div>
    <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden flex flex-col z-10">
        <div class="px-8 py-5 border-b border-slate-100 flex justify-between items-center bg-emerald-50/50">
            <div>
                <h3 class="text-xl font-black text-slate-800">💰 تسجيل دفعة للمورد</h3>
                <p class="text-sm font-semibold text-slate-500 mt-0.5">{{ $supplier->name }} — متبقي: {{ number_format($balance, 0) }} ج.م</p>
            </div>
            <button type="button" onclick="document.getElementById('payModal').classList.add('hidden')"
                    class="text-slate-400 hover:text-rose-500 bg-white hover:bg-rose-50 rounded-xl p-2">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="{{ route('udhiya.suppliers.pay', $supplier) }}" method="POST" class="p-8 flex flex-col gap-5">
            @csrf
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">الفاتورة <span class="text-rose-500">*</span></label>
                <select name="purchase_id" id="payPurchaseId" required onchange="updatePayMax(this)"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 py-3 px-4 text-sm font-bold text-slate-800">
                    <option value="">— اختر الفاتورة —</option>
                    @foreach($unpaidPurchases as $p)
                    @php $rem = round($p->total - $p->paid, 2); @endphp
                    <option value="{{ $p->id }}" data-remaining="{{ $rem }}">
                        #{{ $p->id }} — {{ \Carbon\Carbon::parse($p->date)->format('Y/m/d') }} — متبقي: {{ number_format($rem, 0) }} ج.م
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    المبلغ (ج.م) <span class="text-rose-500">*</span>
                    <span id="payMaxHint" class="text-slate-400 font-normal text-xs hidden">— الحد الأقصى: <span id="payMaxVal"></span> ج.م</span>
                </label>
                <input type="number" name="amount" id="payAmount" min="1" step="0.01" required placeholder="0.00"
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 py-3 px-4 text-sm font-bold text-slate-800 shadow-inner" dir="ltr">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">تاريخ الدفع</label>
                <input type="date" name="paid_at" value="{{ date('Y-m-d') }}"
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 py-3 px-4 text-sm font-bold text-slate-800 shadow-inner">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">ملاحظات</label>
                <input type="text" name="notes" placeholder="اختياري..."
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-slate-400 py-3 px-4 text-sm font-semibold text-slate-800 shadow-inner">
            </div>
            <div class="flex justify-end gap-3 pt-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('payModal').classList.add('hidden')"
                        class="px-6 py-2.5 rounded-xl text-sm font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50">إلغاء</button>
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-200 transition-all">
                    ✅ تسجيل الدفعة
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<style>
@media print {
    /* ─── Hide chrome ─── */
    #sidebar,
    header,
    .no-print,
    #toast-container { display: none !important; }

    /* ─── Page setup ─── */
    @page { margin: 1.5cm; size: A4 portrait; }

    body {
        background: #fff !important;
        font-family: 'Cairo', sans-serif !important;
        font-size: 12px !important;
        color: #1e293b !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    /* ─── Break the shell layout ─── */
    body > div:first-child {
        display: block !important;
        height: auto !important;
        overflow: visible !important;
    }
    body > div:first-child > div:last-child {
        display: block !important;
        overflow: visible !important;
        height: auto !important;
        position: static !important;
    }
    main {
        display: block !important;
        width: 100% !important;
        overflow: visible !important;
    }
    main > div {
        max-width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    /* ─── Show print header ─── */
    .print-only { display: block !important; }

    /* ─── Hide screen-only sections ─── */
    .flex.flex-col.lg\:flex-row.gap-6.mb-8 > .w-full.lg\:w-80 { display: none !important; }

    /* ─── Stat cards grid ─── */
    .grid { display: grid !important; }
    .grid-cols-2 { grid-template-columns: repeat(4, 1fr) !important; }
    .lg\:grid-cols-4 { grid-template-columns: repeat(4, 1fr) !important; }
    .gap-4 { gap: 6px !important; }
    .gap-6 { gap: 10px !important; }
    .mb-8 { margin-bottom: 10px !important; }
    .mt-8 { margin-top: 10px !important; }
    .p-6 { padding: 8px !important; }
    .px-6 { padding-left: 8px !important; padding-right: 8px !important; }
    .py-5 { padding-top: 6px !important; padding-bottom: 6px !important; }

    /* ─── Cards & decoration ─── */
    .rounded-3xl, .rounded-2xl, .rounded-xl { border-radius: 4px !important; }
    .shadow-sm, .shadow-md { box-shadow: none !important; }
    .bg-white { background: #fff !important; }
    .bg-slate-50\/50, .bg-indigo-50\/60 { background: #f8fafc !important; }

    /* ─── Tables ─── */
    .overflow-x-auto { overflow: visible !important; }
    table {
        width: 100% !important;
        border-collapse: collapse !important;
        font-size: 11px !important;
    }
    thead { display: table-header-group; }
    tfoot { display: table-footer-group; }
    tr { page-break-inside: avoid; }
    th, td {
        border: 1px solid #cbd5e1 !important;
        padding: 5px 8px !important;
        text-align: right !important;
    }
    thead tr {
        background-color: #f1f5f9 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    tfoot tr {
        background-color: #f1f5f9 !important;
        font-weight: 900 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    /* ─── Status colors ─── */
    .text-emerald-600, .text-emerald-700 { color: #16a34a !important; }
    .text-rose-600, .text-rose-700 { color: #dc2626 !important; }
    .text-indigo-600, .text-indigo-700 { color: #4f46e5 !important; }
    .text-slate-400, .text-slate-500 { color: #64748b !important; }

    /* ─── Links ─── */
    a { color: #1e293b !important; text-decoration: none !important; }

    /* ─── Inline badges → plain text ─── */
    .inline-flex.items-center.px-2\.5 { background: none !important; border: none !important; padding: 0 !important; }

    /* ─── Page breaks ─── */
    .bg-white { break-inside: avoid; }
}
</style>
@push('js')
<script>
function updatePayMax(sel) {
    var opt = sel.options[sel.selectedIndex];
    var rem = parseFloat(opt.dataset.remaining || 0);
    var amountInput = document.getElementById('payAmount');
    var hint = document.getElementById('payMaxHint');
    var maxVal = document.getElementById('payMaxVal');
    if (rem > 0) {
        amountInput.max = rem;
        amountInput.value = rem;
        maxVal.textContent = rem.toLocaleString('ar-EG');
        hint.classList.remove('hidden');
    } else {
        amountInput.removeAttribute('max');
        amountInput.value = '';
        hint.classList.add('hidden');
    }
}
</script>
@endpush
@endsection
