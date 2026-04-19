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
    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Total Debits --}}
        <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-3xl p-6 border border-emerald-200">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-bold text-emerald-700 mb-1">إجمالي المدخلات</p>
                    <p class="text-3xl font-black text-emerald-900">{{ number_format($totalDebits, 2) }}</p>
                    <p class="text-xs text-emerald-600 mt-1">ج.م</p>
                </div>
                <div class="text-4xl">📥</div>
            </div>
        </div>

        {{-- Total Credits --}}
        <div class="bg-gradient-to-br from-rose-50 to-rose-100 rounded-3xl p-6 border border-rose-200">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-bold text-rose-700 mb-1">إجمالي المخرجات</p>
                    <p class="text-3xl font-black text-rose-900">{{ number_format($totalCredits, 2) }}</p>
                    <p class="text-xs text-rose-600 mt-1">ج.م</p>
                </div>
                <div class="text-4xl">📤</div>
            </div>
        </div>

        {{-- Net Amount --}}
        <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-3xl p-6 border border-indigo-200">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-bold text-indigo-700 mb-1">الصافي</p>
                    <p class="text-3xl font-black" :class="netAmount >= 0 ? 'text-indigo-900' : 'text-rose-900'">
                        {{ number_format($netAmount, 2) }}
                    </p>
                    <p class="text-xs text-indigo-600 mt-1">ج.م</p>
                </div>
                <div class="text-4xl">💰</div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">
        <form method="GET" action="{{ route('udhiya.accounts') }}" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Transaction Type --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-2">نوع العملية</label>
                    <select name="transaction_type" class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                        <option value="">-- الكل --</option>
                        <option value="contract" {{ $transaction_type === 'contract' ? 'selected' : '' }}>الصكوك</option>
                        <option value="payment" {{ $transaction_type === 'payment' ? 'selected' : '' }}>الدفعات</option>
                        <option value="advance" {{ $transaction_type === 'advance' ? 'selected' : '' }}>السلف</option>
                        <option value="purchase" {{ $transaction_type === 'purchase' ? 'selected' : '' }}>المشتريات</option>
                        <option value="sale" {{ $transaction_type === 'sale' ? 'selected' : '' }}>المبيعات</option>
                    </select>
                </div>

                {{-- Wallet --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-2">الخزينة</label>
                    <select name="wallet_id" class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                        <option value="">-- الكل --</option>
                        @foreach($wallets as $w)
                        <option value="{{ $w->id }}" {{ $wallet_id == $w->id ? 'selected' : '' }}>{{ $w->getTypeLabel() }} - {{ $w->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Start Date --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-2">من تاريخ</label>
                    <input type="date" name="start_date" value="{{ $start_date }}"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                </div>

                {{-- End Date --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-2">إلى تاريخ</label>
                    <input type="date" name="end_date" value="{{ $end_date }}"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                </div>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="rounded-xl bg-indigo-600 text-white font-black py-2.5 px-6 hover:bg-indigo-700 transition-colors">
                    🔍 تصفية
                </button>
                <a href="{{ route('udhiya.accounts') }}" class="rounded-xl bg-slate-200 text-slate-700 font-bold py-2.5 px-6 hover:bg-slate-300 transition-colors">
                    ↻ مسح الفلاتر
                </a>
            </div>
        </form>
    </div>

    {{-- Transactions Table --}}
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
            <h6 class="text-lg font-black text-slate-800 m-0 flex items-center gap-3">
                📋 العمليات
                <span class="text-sm font-bold text-slate-400">({{ $paginatedTransactions->total() }})</span>
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
    <div class="flex justify-center">
        {{ $paginatedTransactions->links() }}
    </div>
</div>
@endsection
