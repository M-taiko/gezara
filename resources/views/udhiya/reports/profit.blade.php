@extends('layouts.master')

@section('page-header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8 mt-2">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
            <span class="text-emerald-600 text-4xl">📈</span> تقرير الأرباح والخسائر
        </h1>
        <p class="text-slate-500 font-medium text-sm mt-1">
            <a href="{{ route('udhiya.reports.index') }}" class="text-indigo-500 hover:text-indigo-700 hover:underline">التقارير</a> / الأرباح والخسائر
        </p>
    </div>
    <a href="{{ route('udhiya.expenses.index') }}"
       class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold rounded-xl bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-100 transition-all">
        💸 إدارة المصروفات
    </a>
</div>
@endsection

@section('content')

{{-- Summary cards --}}
<div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
    <div class="bg-white rounded-2xl shadow-sm border border-indigo-100 p-5 text-center">
        <div class="text-2xl font-black text-indigo-700">{{ number_format($totalRevenue, 0) }}</div>
        <div class="text-xs font-bold text-slate-500 mt-1">💰 إجمالي الإيرادات</div>
        <div class="text-xs text-slate-400">ج.م</div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-amber-100 p-5 text-center">
        <div class="text-2xl font-black text-amber-700">{{ number_format($totalCost, 0) }}</div>
        <div class="text-xs font-bold text-slate-500 mt-1">🐄 تكلفة الحيوانات</div>
        <div class="text-xs text-slate-400">ج.م</div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-rose-100 p-5 text-center">
        <div class="text-2xl font-black text-rose-600">{{ number_format($totalExpenses, 0) }}</div>
        <div class="text-xs font-bold text-slate-500 mt-1">💸 المصروفات التشغيلية</div>
        <div class="text-xs text-slate-400">ج.م</div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-sky-100 p-5 text-center">
        <div class="text-2xl font-black text-sky-700">{{ number_format($totalCollected, 0) }}</div>
        <div class="text-xs font-bold text-slate-500 mt-1">✅ المحصّل فعلياً</div>
        <div class="text-xs text-slate-400">ج.م</div>
    </div>
    <div class="col-span-2 lg:col-span-1 bg-white rounded-2xl shadow-sm border p-5 text-center
                {{ $totalProfit >= 0 ? 'border-emerald-200 bg-emerald-50' : 'border-rose-200 bg-rose-50' }}">
        <div class="text-2xl font-black {{ $totalProfit >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
            {{ $totalProfit >= 0 ? '+' : '' }}{{ number_format($totalProfit, 0) }}
        </div>
        <div class="text-xs font-bold text-slate-500 mt-1">
            {{ $totalProfit >= 0 ? '📈 صافي الربح' : '📉 صافي الخسارة' }}
        </div>
        <div class="text-xs text-slate-400">ج.م</div>
    </div>
</div>

<div class="flex flex-col lg:flex-row gap-6">

    {{-- Contracts table --}}
    <div class="flex-1 min-w-0">
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                <h6 class="text-base font-black text-slate-800 m-0">📄 تفاصيل الصكوك</h6>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-right">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs font-bold">
                            <th class="px-5 py-3">الصك</th>
                            <th class="px-5 py-3">العميل</th>
                            <th class="px-5 py-3 text-left">الإيراد</th>
                            <th class="px-5 py-3 text-left hidden md:table-cell">التكلفة</th>
                            <th class="px-5 py-3 text-left">الربح</th>
                            <th class="px-5 py-3 text-left hidden md:table-cell">المحصّل</th>
                            <th class="px-5 py-3 text-left hidden md:table-cell">المتبقي</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($contracts as $c)
                        @php
                            $cost   = $c->items->sum(fn($i) => $i->animal->cost);
                            $profit = $c->total_amount - $cost;
                        @endphp
                        <tr class="hover:bg-slate-50/40 transition-colors">
                            <td class="px-5 py-3">
                                <a href="{{ route('udhiya.contracts.show', $c) }}"
                                   class="font-black text-indigo-600 hover:underline text-sm">
                                    {{ $c->contract_number }}
                                </a>
                            </td>
                            <td class="px-5 py-3 text-sm font-semibold text-slate-700">{{ $c->customer->name }}</td>
                            <td class="px-5 py-3 text-left text-sm font-semibold text-slate-700">
                                {{ number_format($c->total_amount, 0) }} <span class="text-xs text-slate-400">ج.م</span>
                            </td>
                            <td class="px-5 py-3 text-left text-sm text-slate-500 hidden md:table-cell">
                                {{ number_format($cost, 0) }} <span class="text-xs text-slate-400">ج.م</span>
                            </td>
                            <td class="px-5 py-3 text-left">
                                <span class="text-sm font-black {{ $profit >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $profit >= 0 ? '+' : '' }}{{ number_format($profit, 0) }}
                                </span>
                                <span class="text-xs text-slate-400"> ج.م</span>
                            </td>
                            <td class="px-5 py-3 text-left text-sm font-semibold text-emerald-600 hidden md:table-cell">
                                {{ number_format($c->paid_amount, 0) }} <span class="text-xs text-emerald-400">ج.م</span>
                            </td>
                            <td class="px-5 py-3 text-left hidden md:table-cell">
                                @if($c->remaining_amount > 0)
                                <span class="text-sm font-bold text-rose-600">{{ number_format($c->remaining_amount, 0) }} <span class="text-xs text-rose-400">ج.م</span></span>
                                @else
                                <span class="text-xs font-black text-emerald-600">مسدّد ✅</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-slate-900 text-white text-sm font-black">
                            <td colspan="2" class="px-5 py-3 text-slate-400">الإجمالي</td>
                            <td class="px-5 py-3 text-left">{{ number_format($totalRevenue, 0) }} <span class="text-xs text-indigo-400">ج.م</span></td>
                            <td class="px-5 py-3 text-left hidden md:table-cell">{{ number_format($totalCost, 0) }} <span class="text-xs text-slate-400">ج.م</span></td>
                            <td class="px-5 py-3 text-left {{ ($totalRevenue - $totalCost) >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                                {{ number_format($totalRevenue - $totalCost, 0) }} <span class="text-xs">ج.م</span>
                            </td>
                            <td class="px-5 py-3 text-left text-emerald-400 hidden md:table-cell">{{ number_format($totalCollected, 0) }} <span class="text-xs">ج.م</span></td>
                            <td class="hidden md:table-cell"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- Expenses sidebar --}}
    <div class="w-full lg:w-72 flex-shrink-0">
        <div class="bg-white rounded-3xl shadow-sm border border-rose-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-rose-100 bg-gradient-to-b from-rose-50 to-white">
                <h6 class="text-base font-black text-rose-900 m-0">💸 المصروفات التشغيلية</h6>
            </div>
            <div class="px-6 py-5 space-y-3">
                @foreach(\App\Models\Expense::CATEGORIES as $key => $cat)
                @php $amt = $expensesByCategory[$key] ?? 0; @endphp
                @if($amt > 0)
                <div class="flex items-center justify-between py-2 border-b border-slate-50">
                    <span class="text-sm font-semibold text-slate-600">{{ $cat['emoji'] }} {{ $cat['label'] }}</span>
                    <span class="text-sm font-black text-rose-700">{{ number_format($amt, 0) }} ج.م</span>
                </div>
                @endif
                @endforeach
                @if($totalExpenses == 0)
                <p class="text-xs text-slate-400 text-center py-4">لا توجد مصروفات مسجّلة</p>
                @endif
                <div class="pt-2 flex justify-between items-center">
                    <span class="text-sm font-black text-slate-800">إجمالي المصروفات</span>
                    <span class="text-base font-black text-rose-600">{{ number_format($totalExpenses, 0) }} ج.م</span>
                </div>

                <div class="mt-4 pt-4 border-t-2 border-slate-200 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">الإيرادات</span>
                        <span class="font-bold text-indigo-700">{{ number_format($totalRevenue, 0) }} ج.م</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">تكلفة الحيوانات</span>
                        <span class="font-bold text-amber-700">- {{ number_format($totalCost, 0) }} ج.م</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">المصروفات</span>
                        <span class="font-bold text-rose-600">- {{ number_format($totalExpenses, 0) }} ج.م</span>
                    </div>
                    <div class="flex justify-between text-base font-black pt-2 border-t border-slate-200
                                {{ $totalProfit >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                        <span>{{ $totalProfit >= 0 ? 'صافي الربح' : 'صافي الخسارة' }}</span>
                        <span>{{ $totalProfit >= 0 ? '+' : '' }}{{ number_format($totalProfit, 0) }} ج.م</span>
                    </div>
                </div>

                <a href="{{ route('udhiya.expenses.index') }}"
                   class="w-full mt-2 inline-flex justify-center items-center gap-2 px-4 py-2.5 text-sm font-bold rounded-xl bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-100 transition-all">
                    إدارة المصروفات ←
                </a>
            </div>
        </div>
    </div>

</div>

{{-- Per-animal breakdown --}}
@if($animalStats->count() > 0)
<div class="mt-6 bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
        <h6 class="text-base font-black text-slate-800 m-0">🐄 ربحية كل حيوان</h6>
        <span class="text-xs text-slate-400 font-semibold">شامل مصاريف العلف والعلاج والنقل</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-right">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs font-bold">
                    <th class="px-5 py-3">الحيوان</th>
                    <th class="px-5 py-3 hidden sm:table-cell">النوع</th>
                    <th class="px-5 py-3 text-left">تكلفة الشراء</th>
                    <th class="px-5 py-3 text-left">مصاريف إضافية</th>
                    <th class="px-5 py-3 text-left font-black text-slate-700">إجمالي التكلفة</th>
                    <th class="px-5 py-3 text-left">الإيراد</th>
                    <th class="px-5 py-3 text-left font-black">صافي الربح</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($animalStats as $s)
                <tr class="hover:bg-slate-50/40 transition-colors">
                    <td class="px-5 py-3">
                        <a href="{{ route('udhiya.animals.show', $s['animal']) }}"
                           class="font-black text-indigo-600 hover:underline text-sm">
                            {{ $s['animal']->code }}
                        </a>
                    </td>
                    <td class="px-5 py-3 text-sm text-slate-600 hidden sm:table-cell">
                        {{ $s['animal']->product->name ?? '—' }}
                    </td>
                    <td class="px-5 py-3 text-left text-sm text-slate-600">
                        {{ number_format($s['purchase_cost'], 0) }} <span class="text-xs text-slate-400">ج.م</span>
                    </td>
                    <td class="px-5 py-3 text-left text-sm">
                        @if($s['linked_expenses'] > 0)
                        <a href="{{ route('udhiya.expenses.index', ['animal_id' => $s['animal']->id]) }}"
                           class="font-bold text-rose-600 hover:underline">
                            {{ number_format($s['linked_expenses'], 0) }}
                        </a>
                        <span class="text-xs text-rose-400"> ج.م</span>
                        @else
                        <span class="text-slate-300 text-xs">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-left text-sm font-black text-amber-700">
                        {{ number_format($s['total_cost'], 0) }} <span class="text-xs text-amber-400 font-normal">ج.م</span>
                    </td>
                    <td class="px-5 py-3 text-left text-sm font-semibold text-indigo-700">
                        {{ number_format($s['revenue'], 0) }} <span class="text-xs text-indigo-400">ج.م</span>
                    </td>
                    <td class="px-5 py-3 text-left">
                        <span class="text-base font-black {{ $s['profit'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                            {{ $s['profit'] >= 0 ? '+' : '' }}{{ number_format($s['profit'], 0) }}
                        </span>
                        <span class="text-xs text-slate-400"> ج.م</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="bg-slate-900 text-white text-sm font-black">
                    <td colspan="2" class="px-5 py-3 text-slate-400">الإجمالي</td>
                    <td class="px-5 py-3 text-left">{{ number_format($animalStats->sum('purchase_cost'), 0) }} <span class="text-xs text-slate-400">ج.م</span></td>
                    <td class="px-5 py-3 text-left text-rose-400">{{ number_format($animalStats->sum('linked_expenses'), 0) }} <span class="text-xs">ج.م</span></td>
                    <td class="px-5 py-3 text-left text-amber-400">{{ number_format($animalStats->sum('total_cost'), 0) }} <span class="text-xs">ج.م</span></td>
                    <td class="px-5 py-3 text-left text-indigo-400">{{ number_format($animalStats->sum('revenue'), 0) }} <span class="text-xs">ج.م</span></td>
                    <td class="px-5 py-3 text-left {{ $animalStats->sum('profit') >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                        {{ number_format($animalStats->sum('profit'), 0) }} <span class="text-xs">ج.م</span>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endif

@endsection
