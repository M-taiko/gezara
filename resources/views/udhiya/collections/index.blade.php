@extends('layouts.master')

@section('page-header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 mb-8 mt-2">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
            <span class="text-emerald-500 text-4xl">💰</span> تحصيل الدفعات من العملاء
        </h1>
        <p class="text-slate-500 font-medium text-sm mt-1">
            <a href="{{ route('udhiya.dashboard') }}" class="text-indigo-500 hover:text-indigo-700 hover:underline">الرئيسية</a> / تحصيل الدفعات
        </p>
    </div>
    <a href="{{ route('udhiya.collections.create') }}" class="inline-flex items-center px-6 py-3 bg-emerald-600 text-white font-bold rounded-xl hover:bg-emerald-700 shadow-lg transition-all">
        ➕ دفعة جديدة
    </a>
</div>
@endsection

@section('content')

<div class="pb-16">

    {{-- ═══ Statistics Cards ═══ --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        {{-- Total Payments --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-500 mb-1">إجمالي الدفعات</p>
                    <p class="text-2xl font-black text-emerald-600">{{ number_format($payments->total()) }}</p>
                </div>
                <span class="text-3xl">💳</span>
            </div>
        </div>

        {{-- Total Amount Collected --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-500 mb-1">المبلغ المحصّل</p>
                    <p class="text-2xl font-black text-blue-600">
                        @php
                            $totalCollected = \App\Models\Payment::query();
                            if(request('customer_id')) $totalCollected->whereHas('contract', fn($q) => $q->where('customer_id', request('customer_id')));
                            if(request('from')) $totalCollected->where('date', '>=', request('from'));
                            if(request('to')) $totalCollected->where('date', '<=', request('to'));
                            $totalAmount = $totalCollected->sum('amount');
                        @endphp
                        {{ number_format($totalAmount, 2) }}
                        <span class="text-xs text-blue-400 font-normal">ج.م</span>
                    </p>
                </div>
                <span class="text-3xl">💵</span>
            </div>
        </div>

        {{-- Wallet Payments --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-500 mb-1">دفعات في خزائن</p>
                    <p class="text-2xl font-black text-purple-600">
                        @php
                            $walletPayments = \App\Models\Payment::query();
                            if(request('customer_id')) $walletPayments->whereHas('contract', fn($q) => $q->where('customer_id', request('customer_id')));
                            if(request('from')) $walletPayments->where('date', '>=', request('from'));
                            if(request('to')) $walletPayments->where('date', '<=', request('to'));
                            $walletCount = $walletPayments->whereNotNull('wallet_id')->count();
                        @endphp
                        {{ $walletCount }}
                    </p>
                </div>
                <span class="text-3xl">🏦</span>
            </div>
        </div>

        {{-- Cash Payments --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-500 mb-1">دفعات نقدية</p>
                    <p class="text-2xl font-black text-amber-600">
                        @php
                            $cashPayments = \App\Models\Payment::query();
                            if(request('customer_id')) $cashPayments->whereHas('contract', fn($q) => $q->where('customer_id', request('customer_id')));
                            if(request('from')) $cashPayments->where('date', '>=', request('from'));
                            if(request('to')) $cashPayments->where('date', '<=', request('to'));
                            $cashCount = $cashPayments->where('payment_method', 'cash')->count();
                        @endphp
                        {{ $cashCount }}
                    </p>
                </div>
                <span class="text-3xl">💸</span>
            </div>
        </div>
    </div>

    {{-- ═══ Search & Filter ═══ --}}
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 mb-6">
        <form method="GET" action="{{ route('udhiya.collections.index') }}" class="flex flex-wrap gap-3" id="filterForm">
            <div class="flex-1 min-w-[200px]">
                <select name="customer_id"
                        class="w-full rounded-xl border border-slate-200 bg-white focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                    <option value="">🔍 كل العملاء</option>
                    @foreach($customers as $customer)
                    <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                        {{ $customer->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-[180px]">
                <input type="date" name="from" value="{{ request('from') }}"
                       class="w-full rounded-xl border border-slate-200 bg-white focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors"
                       placeholder="من التاريخ">
            </div>
            <div class="flex-1 min-w-[180px]">
                <input type="date" name="to" value="{{ request('to') }}"
                       class="w-full rounded-xl border border-slate-200 bg-white focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors"
                       placeholder="إلى التاريخ">
            </div>
            <button type="submit"
                    class="inline-flex items-center px-5 py-2.5 text-sm font-bold rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 transition-all shadow-sm">
                🔍 بحث
            </button>
            @if(request()->hasAny(['customer_id','from','to']))
            <a href="{{ route('udhiya.collections.index') }}"
               class="inline-flex items-center px-5 py-2.5 text-sm font-bold rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 transition-all">
                ✕ مسح
            </a>
            @endif
        </form>
    </div>

    {{-- ═══ Payments Table ═══ --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white flex items-center justify-between">
            <h6 class="text-base font-black text-slate-800 m-0 flex items-center gap-2">
                📋 سجل الدفعات
            </h6>
            <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-emerald-100 text-emerald-700 text-xs font-bold">
                {{ $payments->total() }} دفعة
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead>
                    <tr class="bg-gradient-to-r from-slate-50 to-white border-b border-slate-100 text-slate-600 text-xs font-bold">
                        <th class="px-6 py-4 text-right">التاريخ</th>
                        <th class="px-6 py-4 text-right">الإيصال</th>
                        <th class="px-6 py-4 text-right">العميل</th>
                        <th class="px-6 py-4 text-right">الصك</th>
                        <th class="px-6 py-4 text-right">الطريقة</th>
                        <th class="px-6 py-4 text-right">المبلغ</th>
                        <th class="px-6 py-4 text-right hidden lg:table-cell">الخزينة</th>
                        <th class="px-6 py-4 text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($payments as $payment)
                    <tr class="hover:bg-emerald-50/30 transition-colors">
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1 text-xs font-bold text-slate-600">
                                📅 {{ $payment->date->format('d/m/Y') }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100 font-mono">
                                #{{ $payment->receipt_number ?? '—' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('udhiya.reports.customer', $payment->contract->customer) }}"
                               class="text-indigo-600 hover:text-indigo-800 font-semibold hover:underline transition-colors">
                                👤 {{ $payment->contract->customer->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('udhiya.contracts.show', $payment->contract) }}"
                               class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-50 text-amber-700 border border-amber-100 hover:bg-amber-100 transition-colors">
                                📄 {{ $payment->contract->contract_number }}
                            </a>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1 text-xs font-bold">
                                @if($payment->payment_method === 'cash')
                                    <span class="text-amber-500">💵 نقدي</span>
                                @elseif($payment->payment_method === 'bank')
                                    <span class="text-blue-600">🏦 بنك</span>
                                @else
                                    <span class="text-purple-600">📲 تحويل</span>
                                @endif
                            </span>
                        </td>
                        <td class="px-6 py-4 text-left">
                            <span class="inline-flex items-center gap-1 text-base font-black text-emerald-600">
                                {{ number_format($payment->amount, 2) }}
                                <span class="text-xs text-emerald-400 font-normal">ج.م</span>
                            </span>
                        </td>
                        <td class="px-6 py-4 hidden lg:table-cell">
                            @if($payment->wallet)
                            <div class="flex items-center gap-1">
                                <span class="text-sm">{{ $payment->wallet->getTypeLabel() }}</span>
                                <span class="text-xs font-semibold text-slate-600">{{ $payment->wallet->name }}</span>
                            </div>
                            @else
                            <span class="text-xs text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('udhiya.collections.edit', $payment) }}"
                                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-amber-50 text-amber-500 hover:bg-amber-500 hover:text-white transition-all" title="تعديل">
                                    ✏️
                                </a>
                                <form action="{{ route('udhiya.collections.destroy', $payment) }}" method="POST" class="inline"
                                      onsubmit="return confirm('هل تريد حذف هذه الدفعة؟')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-rose-50 text-rose-400 hover:bg-rose-500 hover:text-white transition-all" title="حذف">
                                        🗑️
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="py-16 text-center">
                                <div class="text-6xl mb-4 opacity-50">💰</div>
                                <p class="text-slate-400 font-bold text-lg mb-1">لا توجد دفعات مسجّلة</p>
                                <p class="text-slate-300 text-sm">ابدأ بتسجيل دفعة جديدة من خلال الزر "دفعة جديدة"</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $payments->links() }}
        </div>
        @endif
    </div>

</div>

@endsection
