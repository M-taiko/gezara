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
                                    <tr>
                                        <td class="px-3 py-2 font-bold text-indigo-700">{{ $item->animal->code }}</td>
                                        <td class="px-3 py-2 text-slate-600">
                                            {{ \App\Models\Animal::SHARE_LABELS[$item->share_type] ?? $item->share_type }}
                                        </td>
                                        <td class="px-3 py-2 font-bold text-slate-800">{{ number_format($item->total_price, 2) }}</td>
                                    </tr>
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
                                        <td class="px-3 py-2 text-slate-500 text-xs">{{ $payment->date }}</td>
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
    .no-print { display: none !important; }
}
</style>
@endsection
