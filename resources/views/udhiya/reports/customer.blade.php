@extends('layouts.master')

@section('page-header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 mb-8 mt-2">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
            <span class="text-indigo-600 text-4xl">🙋</span> {{ $customer->name }}
        </h1>
        <p class="text-slate-500 font-medium text-sm mt-1">
            <a href="{{ route('udhiya.reports.index') }}" class="text-indigo-500 hover:underline">التقارير</a> /
            <a href="{{ route('udhiya.customers.index') }}" class="text-indigo-500 hover:underline">العملاء</a> /
            {{ $customer->name }}
        </p>
    </div>
    <div class="flex items-center gap-3">
        <button onclick="window.print()" class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-bold rounded-xl bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 shadow-sm no-print">
            🖨️ طباعة الكشف
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
                <div style="font-size:20px; font-weight:900; color:#1e293b; margin:0 0 3px;">كشف حساب عميل</div>
                <div style="font-size:14px; font-weight:700; color:#334155;">{{ $customer->name }}</div>
                @if($customer->phone)
                <div style="font-size:11px; color:#64748b; direction:ltr; text-align:right;">{{ $customer->phone }}</div>
                @endif
                @if($customer->address)
                <div style="font-size:11px; color:#64748b;">{{ $customer->address }}</div>
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
                إجمالي الصكوك<br><strong style="font-size:15px; color:#1e293b;">{{ number_format($totalAmount, 2) }} ج.م</strong>
            </td>
            <td style="border:1px solid #e2e8f0; padding:8px 12px; font-size:12px; font-weight:700; text-align:center;">
                المدفوع<br><strong style="font-size:15px; color:#16a34a;">{{ number_format($paidAmount, 2) }} ج.م</strong>
            </td>
            <td style="border:1px solid #e2e8f0; padding:8px 12px; font-size:12px; font-weight:700; text-align:center;">
                المتبقي<br><strong style="font-size:15px; color:{{ $remainingAmount > 0 ? '#dc2626' : '#16a34a' }};">{{ number_format($remainingAmount, 2) }} ج.م</strong>
            </td>
            <td style="border:1px solid #e2e8f0; padding:8px 12px; font-size:12px; font-weight:700; text-align:center;">
                عدد الصكوك<br><strong style="font-size:15px; color:#4f46e5;">{{ $customer->contracts->count() }}</strong>
            </td>
        </tr>
    </table>
    <hr style="border:0; border-top:2px solid #1e293b; margin-bottom:16px;">
</div>

{{-- Stat Cards --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 text-center hover:shadow-md transition-shadow">
        <div class="text-2xl font-black text-slate-800">{{ number_format($totalAmount, 2) }}</div>
        <div class="text-xs text-slate-500 font-semibold mt-1">إجمالي الصكوك (ج.م)</div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 text-center hover:shadow-md transition-shadow">
        <div class="text-2xl font-black text-emerald-600">{{ number_format($paidAmount, 2) }}</div>
        <div class="text-xs text-slate-500 font-semibold mt-1">المدفوع (ج.م)</div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 text-center hover:shadow-md transition-shadow">
        <div class="text-2xl font-black {{ $remainingAmount > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
            {{ number_format($remainingAmount, 2) }}
        </div>
        <div class="text-xs text-slate-500 font-semibold mt-1">المتبقي (ج.م)</div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 text-center hover:shadow-md transition-shadow">
        <div class="text-2xl font-black text-indigo-700">{{ $customer->contracts->count() }}</div>
        <div class="text-xs text-slate-500 font-semibold mt-1">عدد الصكوك</div>
    </div>
</div>

<div class="flex flex-col lg:flex-row gap-8 pb-16">

    {{-- ===== RIGHT SIDEBAR ===== --}}
    <div class="w-full lg:w-72 flex flex-col gap-6 no-print">

        {{-- Customer Info --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                <h6 class="text-base font-black text-slate-800 m-0">👤 بيانات العميل</h6>
            </div>
            <div class="p-5 space-y-3 text-sm">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-black text-xl border border-indigo-100 flex-shrink-0">
                        {{ mb_substr($customer->name, 0, 1) }}
                    </div>
                    <div>
                        <strong class="text-slate-800 block">{{ $customer->name }}</strong>
                        @if($customer->phone)
                        <span class="text-slate-500 text-xs" dir="ltr">{{ $customer->phone }}</span>
                        @endif
                    </div>
                </div>
                @if($customer->address)
                <div class="text-slate-500 text-xs bg-slate-50 rounded-lg p-2">📍 {{ $customer->address }}</div>
                @endif
                @if($customer->notes)
                <div class="text-slate-500 text-xs bg-slate-50 rounded-lg p-2">💬 {{ $customer->notes }}</div>
                @endif
            </div>
        </div>

        {{-- Groups --}}
        @if($customer->groupMembers->count())
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                <h6 class="text-base font-black text-slate-800 m-0">👥 المجموعات</h6>
            </div>
            <div class="p-5 flex flex-col gap-3">
                @foreach($customer->groupMembers as $member)
                @php
                    $cat   = $member->group->animal?->product?->mainCategory;
                    $emoji = match($cat?->code) { 'BQR'=>'🐄','GHN'=>'🐑','JDN'=>'🐐','JML'=>'🐪', default=>'🐾' };
                @endphp
                <a href="{{ route('udhiya.groups.show', $member->group_id) }}"
                   class="flex items-center justify-between p-3 rounded-xl bg-purple-50 border border-purple-100 hover:bg-purple-100 transition-colors">
                    <div>
                        <span class="font-black text-purple-800 text-sm block">{{ $emoji }} {{ $member->group->name }}</span>
                        <span class="text-purple-600 text-xs">{{ $member->group->shareLabel() }} — {{ $member->shares_count }} نصيب</span>
                    </div>
                    <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Quick Payment Card --}}
        @php $activeContracts = $customer->contracts->where('remaining_amount', '>', 0); @endphp
        @if($activeContracts->count())
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                <h6 class="text-base font-black text-slate-800 m-0">💵 تسجيل دفعة</h6>
            </div>
            <div class="p-5">
                <form action="{{ route('udhiya.payments.store') }}" method="POST" class="flex flex-col gap-4" id="quickPayForm">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">الصك <span class="text-rose-500">*</span></label>
                        <select name="contract_id" required
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 py-2.5 px-3 text-sm font-semibold text-slate-800">
                            @foreach($activeContracts as $c)
                            <option value="{{ $c->id }}">
                                {{ $c->contract_number }} — متبقي {{ number_format($c->remaining_amount, 0) }} ج.م
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">المبلغ <span class="text-rose-500">*</span></label>
                        <input type="number" name="amount" min="0.01" step="0.01" required
                               placeholder="0.00"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 py-2.5 px-3 text-sm font-black text-slate-800 text-center">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">طريقة الدفع <span class="text-rose-500">*</span></label>
                        <select name="payment_method" required
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 py-2.5 px-3 text-sm font-semibold text-slate-800">
                            <option value="cash">💵 نقدي</option>
                            <option value="bank">🏦 بنك</option>
                            <option value="transfer">📲 تحويل</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">الخزينة</label>
                        <select name="wallet_id"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 py-2.5 px-3 text-sm font-semibold text-slate-800">
                            <option value="">— بدون خزينة —</option>
                            @foreach(\App\Models\Wallet::where('is_active', true)->orderBy('name')->get() as $wallet)
                            <option value="{{ $wallet->id }}">{{ $wallet->getTypeLabel() }} {{ $wallet->name }} ({{ number_format($wallet->balance, 2) }} ج.م)</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">التاريخ <span class="text-rose-500">*</span></label>
                        <input type="date" name="date" required value="{{ date('Y-m-d') }}"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 py-2.5 px-3 text-sm font-semibold text-slate-800">
                    </div>
                    <button type="submit"
                            class="w-full inline-flex justify-center items-center px-4 py-3 text-sm font-black rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 transition-all shadow-sm shadow-emerald-200">
                        ✅ تسجيل الدفعة
                    </button>
                </form>
            </div>
        </div>
        @endif

    </div>

    {{-- ===== CONTRACTS LIST ===== --}}
    <div class="flex-1 flex flex-col gap-6">
        @forelse($customer->contracts as $contract)
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-md transition-shadow">
            {{-- Contract Header --}}
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <div class="flex items-center gap-3">
                    <span class="font-black text-slate-800 text-base">📄 {{ $contract->contract_number }}</span>
                    @php
                        $colors = ['active'=>'amber','completed'=>'emerald','cancelled'=>'rose'];
                        $labels = ['active'=>'نشط','completed'=>'مكتمل','cancelled'=>'ملغى'];
                        $c = $colors[$contract->status] ?? 'slate';
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-{{ $c }}-100 text-{{ $c }}-700 border border-{{ $c }}-200">
                        {{ $labels[$contract->status] ?? $contract->status }}
                    </span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-slate-400 text-xs font-semibold">{{ $contract->created_at->format('Y/m/d') }}</span>
                    <a href="{{ route('udhiya.contracts.show', $contract) }}"
                       class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100 hover:bg-indigo-100 transition-colors">
                        عرض الصك
                    </a>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- Items Table --}}
                    <div>
                        <h6 class="text-xs font-black text-slate-500 uppercase mb-3">بنود الصك</h6>
                        <div class="overflow-x-auto">
                            <table class="w-full text-right text-sm">
                                <thead>
                                    <tr class="bg-slate-50 text-slate-500 text-xs">
                                        <th class="px-3 py-2 font-bold rounded-r-lg">الحيوان</th>
                                        <th class="px-3 py-2 font-bold">الحصة</th>
                                        <th class="px-3 py-2 font-bold rounded-l-lg">المبلغ</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($contract->items as $item)
                                    @if($item->animal)
                                    <tr>
                                        <td class="px-3 py-2 font-bold text-indigo-700">{{ $item->animal->code }}</td>
                                        <td class="px-3 py-2 text-slate-600">
                                            {{ \App\Models\Animal::SHARE_LABELS[$item->share_type] ?? $item->share_type }}
                                        </td>
                                        <td class="px-3 py-2 font-bold text-slate-800">{{ number_format($item->total_price, 2) }}</td>
                                    </tr>
                                    @else
                                    <tr>
                                        <td class="px-3 py-2 font-bold text-slate-400">(حيوان مُحذوف)</td>
                                        <td class="px-3 py-2 text-slate-600">
                                            {{ \App\Models\Animal::SHARE_LABELS[$item->share_type] ?? $item->share_type }}
                                        </td>
                                        <td class="px-3 py-2 font-bold text-slate-800">{{ number_format($item->total_price, 2) }}</td>
                                    </tr>
                                    @endif
                                    @endforeach
                                    <tr class="border-t-2 border-slate-200">
                                        <td colspan="2" class="px-3 py-2 font-black text-slate-700 text-xs">الإجمالي</td>
                                        <td class="px-3 py-2 font-black text-slate-800">{{ number_format($contract->total_amount, 2) }} ج.م</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Payments + Summary --}}
                    <div>
                        <h6 class="text-xs font-black text-slate-500 uppercase mb-3">الدفعات</h6>
                        @if($contract->payments->count())
                        <div class="overflow-x-auto mb-3">
                            <table class="w-full text-right text-sm">
                                <thead>
                                    <tr class="bg-emerald-50 text-emerald-700 text-xs">
                                        <th class="px-3 py-2 font-bold rounded-r-lg">التاريخ</th>
                                        <th class="px-3 py-2 font-bold">الطريقة</th>
                                        <th class="px-3 py-2 font-bold rounded-l-lg">المبلغ</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($contract->payments as $payment)
                                    <tr>
                                        <td class="px-3 py-2 text-slate-500 text-xs">{{ $payment->date ? $payment->date->format('Y/m/d') : '—' }}</td>
                                        <td class="px-3 py-2 text-slate-600 text-xs">
                                            {{ ['cash'=>'💵 نقدي','bank'=>'🏦 بنك','transfer'=>'📲 تحويل'][$payment->payment_method] ?? $payment->payment_method }}
                                        </td>
                                        <td class="px-3 py-2 font-black text-emerald-600">{{ number_format($payment->amount, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center text-slate-400 text-xs py-4 bg-slate-50 rounded-xl border border-slate-100 border-dashed mb-3">
                            لا توجد دفعات بعد
                        </div>
                        @endif

                        {{-- Balance Summary --}}
                        <div class="flex flex-col gap-2 text-sm bg-slate-50 rounded-xl p-3 border border-slate-100">
                            <div class="flex justify-between">
                                <span class="text-slate-500 font-semibold">الإجمالي</span>
                                <span class="font-black text-slate-800">{{ number_format($contract->total_amount, 2) }} ج.م</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500 font-semibold">المدفوع</span>
                                <span class="font-black text-emerald-600">{{ number_format($contract->paid_amount, 2) }} ج.م</span>
                            </div>
                            <div class="flex justify-between border-t border-slate-200 pt-2">
                                <span class="text-slate-500 font-semibold">المتبقي</span>
                                <span class="font-black {{ $contract->remaining_amount > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                    {{ number_format($contract->remaining_amount, 2) }} ج.م
                                </span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 flex flex-col items-center justify-center py-16 text-center">
            <div class="text-5xl mb-4">📄</div>
            <h5 class="text-lg font-black text-slate-600 mb-2">لا توجد صكوك</h5>
            <p class="text-slate-400 text-sm">لم يتم إصدار أي صك لهذا العميل بعد</p>
        </div>
        @endforelse
    </div>

</div>

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

    /* ─── Stat cards: 4 per row ─── */
    .grid { display: grid !important; }
    .grid-cols-2 { grid-template-columns: repeat(4, 1fr) !important; }
    .md\:grid-cols-4 { grid-template-columns: repeat(4, 1fr) !important; }
    .md\:grid-cols-2 { grid-template-columns: repeat(2, 1fr) !important; }
    .gap-4 { gap: 6px !important; }
    .gap-6 { gap: 10px !important; }
    .gap-8 { gap: 12px !important; }
    .mb-8 { margin-bottom: 10px !important; }
    .pb-16 { padding-bottom: 0 !important; }

    /* ─── Flex overrides ─── */
    .lg\:flex-row { flex-direction: row !important; }
    .sm\:flex-row { flex-direction: row !important; }
    .flex-1 { flex: 1 !important; }

    /* ─── Cards & decoration ─── */
    .rounded-3xl, .rounded-2xl, .rounded-xl { border-radius: 4px !important; }
    .shadow-sm, .shadow-md { box-shadow: none !important; }
    .bg-white { background: #fff !important; }
    .bg-slate-50\/50 { background: #f8fafc !important; }
    .bg-slate-50 { background: #f8fafc !important; }
    .p-5, .p-6 { padding: 8px !important; }
    .px-6 { padding-left: 8px !important; padding-right: 8px !important; }
    .py-4, .py-5 { padding-top: 6px !important; padding-bottom: 6px !important; }

    /* ─── Tables ─── */
    .overflow-x-auto { overflow: visible !important; }
    table {
        width: 100% !important;
        border-collapse: collapse !important;
        font-size: 11px !important;
    }
    thead { display: table-header-group; }
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
    .text-emerald-600 { color: #16a34a !important; }
    .text-rose-600 { color: #dc2626 !important; }
    .text-indigo-600, .text-indigo-700 { color: #4f46e5 !important; }
    .text-slate-400, .text-slate-500 { color: #64748b !important; }
    .text-purple-800 { color: #581c87 !important; }

    /* ─── Badges → plain ─── */
    span[class*="bg-amber"], span[class*="bg-emerald"], span[class*="bg-rose"] {
        background: none !important;
        border: none !important;
        padding: 0 !important;
        font-weight: 700 !important;
    }

    /* ─── Links ─── */
    a { color: #1e293b !important; text-decoration: none !important; }

    /* ─── Contract cards page breaks ─── */
    .flex-1.flex.flex-col.gap-6 > div { break-inside: avoid; margin-bottom: 12px !important; }

    /* ─── Balance summary box ─── */
    .bg-slate-50.rounded-xl { background: #f8fafc !important; border: 1px solid #e2e8f0 !important; }
}
</style>
@endsection
