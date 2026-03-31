@extends('layouts.master')

@section('page-header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8 mt-2">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
            <span class="text-amber-500 text-4xl">🏪</span> الحيوانات بالموقع
        </h1>
        <p class="text-slate-500 font-medium text-sm mt-1">
            <a href="{{ route('udhiya.animals.index') }}" class="text-indigo-500 hover:text-indigo-700 hover:underline">الحيوانات</a>
            / حسب الموقع
        </p>
    </div>
    <div class="flex items-center gap-3 flex-wrap">
        <form method="GET" action="{{ route('udhiya.animals.by-warehouse') }}" class="flex items-center gap-2">
            <select name="status" onchange="this.form.submit()"
                    class="rounded-xl border border-slate-200 bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800 shadow-sm transition-colors">
                <option value="">كل الحالات</option>
                <option value="available"           {{ $statusFilter === 'available'           ? 'selected' : '' }}>متاح</option>
                <option value="partially_allocated" {{ $statusFilter === 'partially_allocated' ? 'selected' : '' }}>مخصص جزئياً</option>
                <option value="fully_allocated"     {{ $statusFilter === 'fully_allocated'     ? 'selected' : '' }}>مخصص كلياً</option>
                <option value="slaughtered"         {{ $statusFilter === 'slaughtered'         ? 'selected' : '' }}>مذبوح</option>
            </select>
        </form>
        <a href="{{ route('udhiya.animals.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 transition-all">
            📋 قائمة الحيوانات
        </a>
    </div>
</div>
@endsection

@section('content')

{{-- Summary cards --}}
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">
@php
$cards = [
    ['label'=>'الإجمالي',        'value'=>$summary['total'],       'emoji'=>'🐾', 'text'=>'text-slate-700',   'border'=>'border-slate-200'],
    ['label'=>'متاح',             'value'=>$summary['available'],   'emoji'=>'✅', 'text'=>'text-emerald-700', 'border'=>'border-emerald-200'],
    ['label'=>'مخصص جزئياً',     'value'=>$summary['partially'],   'emoji'=>'⏳', 'text'=>'text-amber-700',   'border'=>'border-amber-200'],
    ['label'=>'مخصص كلياً',      'value'=>$summary['fully'],       'emoji'=>'🔒', 'text'=>'text-indigo-700',  'border'=>'border-indigo-200'],
    ['label'=>'مذبوح',            'value'=>$summary['slaughtered'], 'emoji'=>'⚡', 'text'=>'text-rose-700',    'border'=>'border-rose-200'],
];
@endphp
@foreach($cards as $card)
<div class="bg-white rounded-2xl shadow-sm border {{ $card['border'] }} p-4 flex flex-col items-center text-center gap-1">
    <span class="text-2xl">{{ $card['emoji'] }}</span>
    <span class="text-2xl font-black {{ $card['text'] }}">{{ $card['value'] }}</span>
    <span class="text-xs font-bold text-slate-400">{{ $card['label'] }}</span>
</div>
@endforeach
</div>

{{-- Warehouses grid --}}
@if($warehouses->isEmpty())
<div class="py-20 text-center bg-white rounded-3xl shadow-sm border border-slate-100">
    <div class="text-6xl mb-4">🏪</div>
    <p class="text-slate-400 font-bold">لا توجد مخازن مسجلة</p>
</div>
@else
<div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
    @foreach($warehouses as $warehouse)
    @php
        $animals  = $warehouse->animals;
        $count    = $animals->count();
        $available= $animals->where('status', 'available')->count();
        $partial  = $animals->where('status', 'partially_allocated')->count();
        $full     = $animals->where('status', 'fully_allocated')->count();
        $slaughtered = $animals->where('status', 'slaughtered')->count();
    @endphp

    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        {{-- Warehouse header --}}
        <div class="px-6 py-5 border-b border-slate-100 bg-amber-50/50 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center text-xl">🏪</div>
                <div>
                    <h6 class="text-base font-black text-slate-800 m-0">{{ $warehouse->name }}</h6>
                    @if($warehouse->description)
                    <p class="text-xs text-slate-400 font-semibold mt-0.5">{{ $warehouse->description }}</p>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-2 flex-wrap justify-end">
                @if($available > 0)
                <span class="text-xs font-black px-2 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-100">✅ {{ $available }}</span>
                @endif
                @if($partial > 0)
                <span class="text-xs font-black px-2 py-1 rounded-lg bg-amber-50 text-amber-700 border border-amber-100">⏳ {{ $partial }}</span>
                @endif
                @if($full > 0)
                <span class="text-xs font-black px-2 py-1 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-100">🔒 {{ $full }}</span>
                @endif
                @if($slaughtered > 0)
                <span class="text-xs font-black px-2 py-1 rounded-lg bg-rose-50 text-rose-700 border border-rose-100">⚡ {{ $slaughtered }}</span>
                @endif
                <span class="text-xs font-black px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600 border border-slate-200">
                    {{ $count }} حيوان
                </span>
            </div>
        </div>

        {{-- Animals --}}
        @if($animals->isEmpty())
        <div class="py-10 text-center">
            <p class="text-slate-300 text-sm font-bold">لا توجد حيوانات في هذا الموقع</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs font-bold">
                        <th class="px-5 py-2.5">الكود</th>
                        <th class="px-5 py-2.5">النوع</th>
                        <th class="px-5 py-2.5 text-center">الحالة</th>
                        <th class="px-5 py-2.5 text-center hidden sm:table-cell">الأنصبة</th>
                        <th class="px-5 py-2.5 text-left hidden md:table-cell">التكلفة</th>
                        <th class="px-5 py-2.5 w-10"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($animals->sortBy('code') as $animal)
                    @php
                        $statusCfg = [
                            'available'           => ['bg-emerald-100 text-emerald-700', 'متاح'],
                            'partially_allocated' => ['bg-amber-100 text-amber-700',     'جزئي'],
                            'fully_allocated'     => ['bg-indigo-100 text-indigo-700',   'مكتمل'],
                            'slaughtered'         => ['bg-rose-100 text-rose-700',       'مذبوح'],
                        ];
                        [$cls, $lbl] = $statusCfg[$animal->status] ?? ['bg-slate-100 text-slate-600', $animal->status];
                        $cat = $animal->product?->mainCategory;
                        $emoji = match($cat?->code) { 'BQR' => '🐄', 'GHN' => '🐑', 'JDN' => '🐐', 'JML' => '🐪', default => '🐾' };
                    @endphp
                    <tr class="hover:bg-slate-50/40 transition-colors {{ $animal->status === 'slaughtered' ? 'opacity-60' : '' }}">
                        <td class="px-5 py-3">
                            <span class="text-sm font-black text-slate-800">{{ $emoji }} {{ $animal->code }}</span>
                        </td>
                        <td class="px-5 py-3 text-sm text-slate-600 font-semibold">
                            {{ $animal->product?->name ?? '—' }}
                        </td>
                        <td class="px-5 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-black {{ $cls }}">
                                {{ $lbl }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-center hidden sm:table-cell">
                            @if($animal->is_grouped && $animal->shareSetting)
                            @php
                                $ss = $animal->shareSetting;
                                $pct = $ss->total_shares > 0 ? round(($ss->sold_shares / $ss->total_shares) * 100) : 0;
                            @endphp
                            <div class="flex items-center gap-1.5 justify-center min-w-[70px]">
                                <div class="flex-1 bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                    <div class="h-1.5 rounded-full {{ $pct >= 100 ? 'bg-indigo-500' : 'bg-amber-400' }}"
                                         style="width:{{ $pct }}%"></div>
                                </div>
                                <span class="text-xs font-black text-slate-500">{{ $ss->sold_shares }}/{{ $ss->total_shares }}</span>
                            </div>
                            @else
                            <span class="text-slate-200 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-left text-sm font-semibold text-slate-600 hidden md:table-cell">
                            {{ number_format($animal->cost, 0) }} <span class="text-xs text-slate-400">ج.م</span>
                        </td>
                        <td class="px-5 py-3 text-center">
                            <a href="{{ route('udhiya.animals.show', $animal) }}"
                               class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-500 hover:bg-indigo-600 hover:text-white inline-flex items-center justify-center transition-colors text-xs">
                                👁
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-slate-50/60 border-t border-slate-100 text-xs font-bold text-slate-500">
                        <td colspan="4" class="px-5 py-2.5">{{ $count }} حيوان في {{ $warehouse->name }}</td>
                        <td class="px-5 py-2.5 text-left text-slate-700 hidden md:table-cell">
                            {{ number_format($animals->sum('cost'), 0) }} ج.م
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif
    </div>
    @endforeach
</div>
@endif

@endsection
