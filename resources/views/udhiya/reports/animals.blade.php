@extends('layouts.master')

@section('page-header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8 mt-2">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
            <span class="text-indigo-600 text-4xl">🐄</span> تقرير الحيوانات
        </h1>
        <p class="text-slate-500 font-medium text-sm mt-1">
            <a href="{{ route('udhiya.reports.index') }}" class="text-indigo-500 hover:text-indigo-700 hover:underline">التقارير</a> / الحيوانات
        </p>
    </div>
    <form method="GET" action="{{ route('udhiya.reports.animals') }}" class="flex items-center gap-2">
        <select name="status" onchange="this.form.submit()"
                class="rounded-xl border border-slate-200 bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors shadow-sm">
            <option value="">كل الحالات</option>
            <option value="available"           {{ request('status') === 'available'           ? 'selected' : '' }}>متاح</option>
            <option value="partially_allocated" {{ request('status') === 'partially_allocated' ? 'selected' : '' }}>مخصص جزئياً</option>
            <option value="fully_allocated"     {{ request('status') === 'fully_allocated'     ? 'selected' : '' }}>مخصص كلياً</option>
            <option value="slaughtered"         {{ request('status') === 'slaughtered'         ? 'selected' : '' }}>مذبوح</option>
        </select>
    </form>
</div>
@endsection

@section('content')

{{-- Summary cards --}}
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">
@php
$cards = [
    ['label'=>'إجمالي الحيوانات', 'value'=>$summary['total'],      'emoji'=>'🐾', 'bg'=>'bg-slate-50',   'text'=>'text-slate-700',  'border'=>'border-slate-200'],
    ['label'=>'متاح',             'value'=>$summary['available'],   'emoji'=>'✅', 'bg'=>'bg-emerald-50', 'text'=>'text-emerald-700','border'=>'border-emerald-200'],
    ['label'=>'مخصص جزئياً',     'value'=>$summary['partially'],   'emoji'=>'⏳', 'bg'=>'bg-amber-50',   'text'=>'text-amber-700',  'border'=>'border-amber-200'],
    ['label'=>'مخصص كلياً',      'value'=>$summary['fully'],       'emoji'=>'🔒', 'bg'=>'bg-indigo-50',  'text'=>'text-indigo-700', 'border'=>'border-indigo-200'],
    ['label'=>'مذبوح',            'value'=>$summary['slaughtered'], 'emoji'=>'⚡', 'bg'=>'bg-rose-50',    'text'=>'text-rose-700',   'border'=>'border-rose-200'],
];
@endphp
@foreach($cards as $card)
<div class="bg-white rounded-2xl shadow-sm border {{ $card['border'] }} p-5 flex flex-col items-center text-center gap-1">
    <span class="text-3xl">{{ $card['emoji'] }}</span>
    <span class="text-3xl font-black {{ $card['text'] }}">{{ $card['value'] }}</span>
    <span class="text-xs font-bold text-slate-500">{{ $card['label'] }}</span>
</div>
@endforeach
</div>

{{-- Financial summary row --}}
@php
$totalCost     = $animals->sum('cost');
$totalExpenses = $animals->sum(fn($a) => $animalStats[$a->id]['linkedExpenses'] ?? 0);
$totalRevenue  = $animals->sum(fn($a) => $animalStats[$a->id]['revenue'] ?? 0);
$totalProfit   = $totalRevenue - $totalCost - $totalExpenses;
@endphp
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex flex-col gap-1">
        <span class="text-xs font-bold text-slate-400">إجمالي التكلفة</span>
        <span class="text-xl font-black text-rose-600">{{ number_format($totalCost, 0) }} <span class="text-xs text-rose-400">ج.م</span></span>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex flex-col gap-1">
        <span class="text-xs font-bold text-slate-400">إجمالي المصاريف</span>
        <span class="text-xl font-black text-orange-600">{{ number_format($totalExpenses, 0) }} <span class="text-xs text-orange-400">ج.م</span></span>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex flex-col gap-1">
        <span class="text-xs font-bold text-slate-400">إجمالي الإيرادات</span>
        <span class="text-xl font-black text-emerald-600">{{ number_format($totalRevenue, 0) }} <span class="text-xs text-emerald-400">ج.م</span></span>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border {{ $totalProfit >= 0 ? 'border-emerald-100' : 'border-rose-100' }} p-5 flex flex-col gap-1">
        <span class="text-xs font-bold text-slate-400">صافي الأرباح</span>
        <span class="text-xl font-black {{ $totalProfit >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
            {{ $totalProfit >= 0 ? '+' : '' }}{{ number_format($totalProfit, 0) }} <span class="text-xs">ج.م</span>
        </span>
    </div>
</div>

{{-- Table --}}
<div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
        <h6 class="text-base font-black text-slate-800 m-0">قائمة الحيوانات</h6>
        <span class="text-xs font-bold text-slate-400">{{ $animals->count() }} حيوان</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-right">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs font-bold">
                    <th class="px-5 py-3">الكود</th>
                    <th class="px-5 py-3 hidden sm:table-cell">النوع</th>
                    <th class="px-5 py-3 hidden md:table-cell">المخزن</th>
                    <th class="px-5 py-3 text-left">التكلفة</th>
                    <th class="px-5 py-3 text-left hidden sm:table-cell">المصاريف</th>
                    <th class="px-5 py-3 text-left hidden md:table-cell">الإيراد</th>
                    <th class="px-5 py-3 text-left">صافي الربح</th>
                    <th class="px-5 py-3 hidden lg:table-cell">الأنصبة</th>
                    <th class="px-5 py-3">الحالة</th>
                    <th class="px-5 py-3 w-12"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($animals as $animal)
                @php
                $statusConfig = [
                    'available'           => ['bg-emerald-100 text-emerald-700', 'متاح'],
                    'partially_allocated' => ['bg-amber-100 text-amber-700',     'جزئي'],
                    'fully_allocated'     => ['bg-indigo-100 text-indigo-700',   'مكتمل'],
                    'slaughtered'         => ['bg-rose-100 text-rose-700',       'مذبوح'],
                ];
                [$statusCls, $statusLbl] = $statusConfig[$animal->status] ?? ['bg-slate-100 text-slate-600', $animal->status];
                $stats          = $animalStats[$animal->id] ?? ['linkedExpenses'=>0,'revenue'=>0,'profit'=>0];
                $profit         = $stats['profit'];
                $linkedExpenses = $stats['linkedExpenses'];
                $revenue        = $stats['revenue'];
                @endphp
                <tr class="hover:bg-slate-50/40 transition-colors">
                    {{-- Code --}}
                    <td class="px-5 py-4">
                        <span class="font-black text-slate-800 text-sm">{{ $animal->code }}</span>
                        <div class="text-xs text-slate-400 font-semibold hidden sm:block">{{ $animal->product->name }}</div>
                    </td>
                    {{-- Type --}}
                    <td class="px-5 py-4 text-sm font-semibold text-slate-700 hidden sm:table-cell">
                        {{ $animal->product->mainCategory->name }}
                    </td>
                    {{-- Warehouse --}}
                    <td class="px-5 py-4 text-sm text-slate-500 hidden md:table-cell">
                        {{ $animal->warehouse->name }}
                    </td>
                    {{-- Purchase Cost --}}
                    <td class="px-5 py-4 text-left text-sm font-semibold text-rose-600">
                        {{ number_format($animal->cost, 0) }}
                        <span class="text-xs text-slate-400">ج.م</span>
                    </td>
                    {{-- Expenses --}}
                    <td class="px-5 py-4 text-left hidden sm:table-cell">
                        @if($linkedExpenses > 0)
                        <span class="text-sm font-bold text-orange-600">{{ number_format($linkedExpenses, 0) }}</span>
                        <span class="text-xs text-slate-400"> ج.م</span>
                        @else
                        <span class="text-slate-300 text-xs">—</span>
                        @endif
                    </td>
                    {{-- Revenue --}}
                    <td class="px-5 py-4 text-left hidden md:table-cell">
                        @if($revenue > 0)
                        <span class="text-sm font-bold text-emerald-700">{{ number_format($revenue, 0) }}</span>
                        <span class="text-xs text-slate-400"> ج.م</span>
                        @else
                        <span class="text-slate-300 text-xs">—</span>
                        @endif
                    </td>
                    {{-- Net Profit --}}
                    <td class="px-5 py-4 text-left">
                        @if($revenue > 0 || $linkedExpenses > 0)
                            <span class="text-sm font-black {{ $profit >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                                {{ $profit >= 0 ? '+' : '' }}{{ number_format($profit, 0) }}
                            </span>
                            <span class="text-xs text-slate-400"> ج.م</span>
                        @else
                        <span class="text-slate-300 text-xs">—</span>
                        @endif
                    </td>
                    {{-- Shares --}}
                    <td class="px-5 py-4 hidden lg:table-cell">
                        @if($animal->is_grouped && $animal->shareSetting)
                        @php
                            $pct  = $animal->shareSetting->completionPercentage();
                            $sold = $animal->shareSetting->sold_shares;
                            $tot  = $animal->shareSetting->total_shares;
                        @endphp
                        <div class="flex items-center gap-2 min-w-[90px]">
                            <div class="flex-1 bg-slate-100 rounded-full h-2 overflow-hidden">
                                <div class="h-2 rounded-full transition-all {{ $pct >= 100 ? 'bg-indigo-500' : ($pct > 50 ? 'bg-amber-500' : 'bg-emerald-500') }}"
                                     style="width:{{ $pct }}%"></div>
                            </div>
                            <span class="text-xs font-black text-slate-600 whitespace-nowrap">{{ $sold }}/{{ $tot }}</span>
                        </div>
                        @else
                        <span class="text-slate-300 text-xs">—</span>
                        @endif
                    </td>
                    {{-- Status --}}
                    <td class="px-5 py-4">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-black {{ $statusCls }}">
                            {{ $statusLbl }}
                        </span>
                    </td>
                    {{-- View History Button --}}
                    <td class="px-5 py-4 text-center">
                        <a href="{{ route('udhiya.animals.show', $animal) }}"
                           title="عرض السجل الكامل"
                           class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white inline-flex items-center justify-center transition-colors text-sm font-black shadow-sm">
                            📋
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10">
                        <div class="py-16 text-center">
                            <div class="text-5xl mb-4">🐄</div>
                            <p class="text-slate-400 font-bold">لا توجد حيوانات</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
