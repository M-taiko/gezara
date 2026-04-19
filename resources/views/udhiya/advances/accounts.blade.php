@extends('layouts.master')
@section('page-header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 mb-8 mt-2">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
            <span class="text-indigo-600 text-4xl">📊</span> الحسابات العامة
        </h1>
        <p class="text-slate-500 font-medium text-sm mt-1">
            <a href="{{ route('udhiya.dashboard') }}" class="text-indigo-500 hover:text-indigo-700 hover:underline">لوحة التحكم</a>
            / الحسابات العامة
        </p>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-6">
    {{-- Quick Summary --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3 mb-6">
        {{-- Total Contracts --}}
        <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-xs font-bold text-slate-600 mb-1">الصكوك المستحقة</p>
                    <p class="text-xl font-black text-purple-700">{{ number_format($totals['contracts_receivable'], 0) }}</p>
                </div>
                <div class="text-2xl">📋</div>
            </div>
        </div>

        {{-- Total Payments --}}
        <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-xs font-bold text-slate-600 mb-1">المحصّل من العملاء</p>
                    <p class="text-xl font-black text-emerald-700">{{ number_format($totals['payments_received'], 0) }}</p>
                </div>
                <div class="text-2xl">✅</div>
            </div>
        </div>

        {{-- Total Purchases --}}
        <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-xs font-bold text-slate-600 mb-1">التزام الشراء</p>
                    <p class="text-xl font-black text-orange-700">{{ number_format($totals['purchases_payable'], 0) }}</p>
                </div>
                <div class="text-2xl">🛒</div>
            </div>
        </div>

        {{-- Total Sales --}}
        <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-xs font-bold text-slate-600 mb-1">إيراد المبيعات</p>
                    <p class="text-xl font-black text-blue-700">{{ number_format($totals['sales_revenue'], 0) }}</p>
                </div>
                <div class="text-2xl">🧊</div>
            </div>
        </div>

        {{-- Net Balance --}}
        <div class="rounded-xl p-4 shadow-sm border hover:shadow-md transition-shadow"
             :class="$totals['net_balance'] >= 0 ? 'bg-white border-indigo-200' : 'bg-white border-rose-200'">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-xs font-bold text-slate-600 mb-1">الرصيد الصافي</p>
                    <p class="text-xl font-black" :class="$totals['net_balance'] >= 0 ? 'text-indigo-700' : 'text-rose-700'">
                        {{ number_format($totals['net_balance'], 0) }}
                    </p>
                </div>
                <div class="text-2xl">{{ $totals['net_balance'] >= 0 ? '📈' : '📉' }}</div>
            </div>
        </div>
    </div>


    {{-- Filters --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <form method="GET" action="{{ route('udhiya.accounts') }}" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                {{-- Transaction Type --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-2">نوع العملية</label>
                    <select name="transaction_type" class="w-full rounded-lg border border-slate-200 bg-white hover:border-slate-300 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2 px-3 text-sm text-slate-800 transition-colors">
                        <option value="">-- الكل --</option>
                        <option value="contract" {{ $filters['transaction_type'] === 'contract' ? 'selected' : '' }}>الصكوك</option>
                        <option value="payment" {{ $filters['transaction_type'] === 'payment' ? 'selected' : '' }}>الدفعات</option>
                        <option value="advance" {{ $filters['transaction_type'] === 'advance' ? 'selected' : '' }}>السلف</option>
                        <option value="purchase" {{ $filters['transaction_type'] === 'purchase' ? 'selected' : '' }}>المشتريات</option>
                        <option value="sale" {{ $filters['transaction_type'] === 'sale' ? 'selected' : '' }}>المبيعات</option>
                    </select>
                </div>

                {{-- Wallet --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-2">الخزينة</label>
                    <select name="wallet_id" class="w-full rounded-lg border border-slate-200 bg-white hover:border-slate-300 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2 px-3 text-sm text-slate-800 transition-colors">
                        <option value="">-- الكل --</option>
                        @foreach($wallets as $w)
                        <option value="{{ $w->id }}" {{ $filters['wallet_id'] == $w->id ? 'selected' : '' }}>{{ $w->getTypeLabel() }} - {{ $w->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Start Date --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-2">من تاريخ</label>
                    <input type="date" name="start_date" value="{{ $filters['start_date'] }}"
                           class="w-full rounded-lg border border-slate-200 bg-white hover:border-slate-300 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2 px-3 text-sm text-slate-800 transition-colors">
                </div>

                {{-- End Date --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-2">إلى تاريخ</label>
                    <input type="date" name="end_date" value="{{ $filters['end_date'] }}"
                           class="w-full rounded-lg border border-slate-200 bg-white hover:border-slate-300 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2 px-3 text-sm text-slate-800 transition-colors">
                </div>
            </div>

            <div class="flex gap-2 pt-2">
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 text-white font-bold py-2 px-5 hover:bg-indigo-700 transition-colors shadow-sm">
                    🔍 تصفية
                </button>
                <a href="{{ route('udhiya.accounts') }}" class="inline-flex items-center gap-2 rounded-lg bg-slate-100 text-slate-700 font-bold py-2 px-4 hover:bg-slate-200 transition-colors">
                    ↻ مسح
                </a>
            </div>
        </form>
    </div>

    {{-- Transactions Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-slate-100">
            <h6 class="text-base font-black text-slate-800 m-0 flex items-center gap-2">
                📋 تفاصيل العمليات
                <span class="text-xs font-bold bg-indigo-100 text-indigo-700 px-2 py-1 rounded-full">({{ $paginatedTransactions->total() }})</span>
            </h6>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs font-bold">
                        <th class="px-6 py-3">المرجع</th>
                        <th class="px-6 py-3">البيان</th>
                        <th class="px-6 py-3">نوع العملية</th>
                        <th class="px-6 py-3 text-center">مدخل</th>
                        <th class="px-6 py-3 text-center">مخرج</th>
                        <th class="px-6 py-3">الخزينة</th>
                        <th class="px-6 py-3">التاريخ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($paginatedTransactions as $t)
                    @if($t['is_contract'] ?? false)
                        {{-- Contract Row with Payment Breakdown --}}
                        <tr class="hover:bg-slate-50/40 transition-colors bg-purple-50/30">
                            <td class="px-6 py-4 font-bold">
                                <a href="{{ $t['reference_url'] }}" class="text-indigo-600 hover:text-indigo-800 hover:underline">
                                    {{ $t['reference'] }}
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-700">{{ $t['description'] }}</div>
                                <div class="text-xs text-slate-500 mt-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-emerald-600 font-bold">محصّل: {{ number_format($t['collected'], 2) }} ج.م</span>
                                        <span class="text-rose-600 font-bold">متبقي: {{ number_format($t['remaining'], 2) }} ج.م</span>
                                    </div>
                                    <div class="mt-1 bg-slate-200 rounded-full h-1.5 overflow-hidden">
                                        <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ $t['total'] > 0 ? ($t['collected'] / $t['total'] * 100) : 0 }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-black bg-purple-100 text-purple-700">
                                    {{ $t['transaction_type'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center font-black text-emerald-600">
                                +{{ number_format($t['debit'], 2) }}
                            </td>
                            <td class="px-6 py-4 text-center font-black text-slate-400">
                                —
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">
                                {{ $t['wallet_name'] }}
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $t['date']->format('d/m/Y') }}</td>
                        </tr>
                    @else
                        {{-- Regular Transaction Row --}}
                        <tr class="hover:bg-slate-50/40 transition-colors">
                            <td class="px-6 py-4 font-bold">
                                @if($t['reference_url'])
                                    <a href="{{ $t['reference_url'] }}" class="text-indigo-600 hover:text-indigo-800 hover:underline">
                                        {{ $t['reference'] ?: '—' }}
                                    </a>
                                @else
                                    <span class="text-slate-700">{{ $t['reference'] ?: '—' }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-700">
                                {{ $t['description'] }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-black
                                    @if($t['type'] === 'payment') bg-indigo-100 text-indigo-700
                                    @elseif($t['type'] === 'advance') bg-blue-100 text-blue-700
                                    @elseif($t['type'] === 'purchase') bg-orange-100 text-orange-700
                                    @elseif($t['type'] === 'sale') bg-emerald-100 text-emerald-700
                                    @else bg-slate-100 text-slate-700 @endif">
                                    {{ $t['transaction_type'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center font-black text-emerald-600">
                                {{ $t['debit'] > 0 ? '+' . number_format($t['debit'], 2) : '—' }}
                            </td>
                            <td class="px-6 py-4 text-center font-black text-rose-600">
                                {{ $t['credit'] > 0 ? '-' . number_format($t['credit'], 2) : '—' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">
                                {{ $t['wallet_name'] }}
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $t['date']->format('d/m/Y') }}</td>
                        </tr>
                    @endif
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="text-4xl mb-3">💤</div>
                            <p class="text-slate-400 font-semibold">لا توجد عمليات</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if($paginatedTransactions->hasPages())
    <div class="flex justify-center pt-4">
        {{ $paginatedTransactions->links() }}
    </div>
    @endif
</div>
@endsection
