@extends('layouts.master')

@section('page-header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8 mt-2">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
            <span class="text-rose-600 text-4xl">🔪</span> تقرير الذبح
        </h1>
        <p class="text-slate-500 font-medium text-sm mt-1">
            <a href="{{ route('udhiya.reports.index') }}" class="text-indigo-500 hover:text-indigo-700 hover:underline">التقارير</a>
            / الذبح
        </p>
    </div>
    <form method="GET" action="{{ route('udhiya.reports.slaughter') }}" class="flex items-center gap-2">
        <select name="filter" onchange="this.form.submit()"
                class="rounded-xl border border-slate-200 bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800 shadow-sm transition-colors">
            <option value="all"        {{ $filter === 'all'        ? 'selected' : '' }}>كل المجموعات</option>
            <option value="slaughtered"{{ $filter === 'slaughtered'? 'selected' : '' }}>تم ذبحها</option>
            <option value="pending"    {{ $filter === 'pending'    ? 'selected' : '' }}>لم تُذبح بعد</option>
        </select>
    </form>
</div>
@endsection

@section('content')

{{-- Summary cards --}}
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">
@php
$cards = [
    ['label'=>'إجمالي المجموعات', 'value'=>$summary['total'],         'emoji'=>'👥', 'color'=>'slate'],
    ['label'=>'تم الذبح',         'value'=>$summary['slaughtered'],    'emoji'=>'🔪', 'color'=>'rose'],
    ['label'=>'لم تُذبح',         'value'=>$summary['pending'],        'emoji'=>'⏳', 'color'=>'amber'],
    ['label'=>'تم التسليم',       'value'=>$summary['delivered'],      'emoji'=>'✅', 'color'=>'emerald'],
    ['label'=>'لم يستلموا',       'value'=>$summary['not_delivered'],  'emoji'=>'🕐', 'color'=>'indigo'],
];
$colorMap = [
    'slate'   => ['bg-slate-50',   'text-slate-700',   'border-slate-200'],
    'rose'    => ['bg-rose-50',    'text-rose-700',    'border-rose-200'],
    'amber'   => ['bg-amber-50',   'text-amber-700',   'border-amber-200'],
    'emerald' => ['bg-emerald-50', 'text-emerald-700', 'border-emerald-200'],
    'indigo'  => ['bg-indigo-50',  'text-indigo-700',  'border-indigo-200'],
];
@endphp
@foreach($cards as $card)
@php [$bg, $text, $border] = $colorMap[$card['color']]; @endphp
<div class="bg-white rounded-2xl shadow-sm border {{ $border }} p-5 flex flex-col items-center text-center gap-1">
    <span class="text-3xl">{{ $card['emoji'] }}</span>
    <span class="text-3xl font-black {{ $text }}">{{ $card['value'] }}</span>
    <span class="text-xs font-bold text-slate-500">{{ $card['label'] }}</span>
</div>
@endforeach
</div>

{{-- Groups --}}
@forelse($groups as $group)
@php
    $animal      = $group->animal;
    $isSlaughtered = $animal?->status === 'slaughtered';
    $cat         = $animal?->product?->mainCategory;
    $emoji       = match($cat?->code) { 'BQR' => '🐄', 'GHN' => '🐑', 'JDN' => '🐐', 'JML' => '🐪', default => '🐾' };
    $members     = $group->members;
    $deliveredCount   = $members->filter(fn($m) => $m->contractItem?->delivered_at)->count();
    $withContract     = $members->filter(fn($m) => $m->contractItem)->count();
    $noContract       = $members->filter(fn($m) => !$m->contractItem)->count();
    $totalMembers     = $members->count();
    $deliveryPct      = $withContract > 0 ? round(($deliveredCount / $withContract) * 100) : 0;
@endphp

<div class="bg-white rounded-3xl shadow-sm border {{ $isSlaughtered ? 'border-rose-100' : 'border-slate-100' }} overflow-hidden mb-5">

    {{-- Group header --}}
    <div class="px-6 py-5 border-b {{ $isSlaughtered ? 'border-rose-100 bg-rose-50/40' : 'border-slate-100 bg-slate-50/50' }} flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="flex items-center gap-3">
            <span class="text-3xl">{{ $emoji }}</span>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h6 class="text-base font-black text-slate-800 m-0">{{ $group->name }}</h6>
                    @if($isSlaughtered)
                        <span class="text-xs font-black px-2.5 py-1 rounded-full bg-rose-100 text-rose-700 border border-rose-200">
                            🔪 تم الذبح {{ $animal->slaughtered_at?->format('d/m/Y') }}
                        </span>
                    @else
                        <span class="text-xs font-black px-2.5 py-1 rounded-full bg-amber-100 text-amber-700 border border-amber-200">
                            ⏳ لم تُذبح بعد
                        </span>
                    @endif
                </div>
                <p class="text-xs text-slate-400 font-semibold mt-0.5">
                    {{ $group->shareLabel() }}
                    @if($animal)
                    — <a href="{{ route('udhiya.animals.show', $animal) }}"
                         class="text-indigo-500 hover:underline font-bold">{{ $animal->code }}</a>
                    @endif
                    — {{ $totalMembers }} عضو
                </p>
            </div>
        </div>

        {{-- Delivery progress --}}
        @if($isSlaughtered && $withContract > 0)
        <div class="flex flex-col gap-1 min-w-[160px]">
            <div class="flex justify-between text-xs font-bold text-slate-500 mb-1">
                <span>التسليم</span>
                <span>{{ $deliveredCount }}/{{ $withContract }}</span>
            </div>
            <div class="h-2.5 bg-slate-100 rounded-full overflow-hidden">
                <div class="h-full rounded-full transition-all {{ $deliveryPct >= 100 ? 'bg-emerald-500' : 'bg-indigo-400' }}"
                     style="width: {{ $deliveryPct }}%"></div>
            </div>
            <p class="text-xs text-slate-400 font-semibold">{{ $deliveryPct }}% تم تسليمهم</p>
        </div>
        @endif

        <a href="{{ route('udhiya.groups.show', $group) }}"
           class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-black rounded-xl bg-indigo-50 text-indigo-700 hover:bg-indigo-600 hover:text-white border border-indigo-100 transition-all no-print">
            إدارة المجموعة ←
        </a>
    </div>

    {{-- Members table --}}
    @if($members->isEmpty())
    <div class="py-10 text-center text-slate-400 text-sm font-bold">لا يوجد أعضاء</div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-right">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs font-bold">
                    <th class="px-5 py-3">#</th>
                    <th class="px-5 py-3">العميل</th>
                    <th class="px-5 py-3 hidden sm:table-cell">الهاتف</th>
                    <th class="px-5 py-3 text-center">الأنصبة</th>
                    <th class="px-5 py-3 text-center hidden md:table-cell">المستحق</th>
                    <th class="px-5 py-3 text-center hidden md:table-cell">المدفوع</th>
                    <th class="px-5 py-3 text-center">الصك</th>
                    @if($isSlaughtered)
                    <th class="px-5 py-3 text-center">حالة التسليم</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 text-sm">
                @foreach($members as $i => $member)
                @php
                    $item      = $member->contractItem;
                    $contract  = $item?->contract;
                    $paid      = $contract?->paid_amount ?? 0;
                    $total     = $contract?->total_amount ?? 0;
                    $delivered = $item?->delivered_at;
                    $priceField    = 'price_' . $group->share_type;
                    $pricePerShare = $animal ? (float)($animal->$priceField ?? 0) : 0;
                    $due = $pricePerShare * $member->shares_count;
                @endphp
                <tr class="hover:bg-slate-50/40 transition-colors {{ $delivered ? 'opacity-75' : '' }}">
                    <td class="px-5 py-3 text-slate-400 font-bold text-xs">{{ $i + 1 }}</td>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-xl {{ $delivered ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-indigo-50 text-indigo-600 border-indigo-100' }} flex items-center justify-center font-black text-sm border flex-shrink-0">
                                {{ mb_substr($member->customer?->name ?? '?', 0, 1) }}
                            </div>
                            <span class="font-black text-slate-800">{{ $member->customer?->name ?? '—' }}</span>
                        </div>
                    </td>
                    <td class="px-5 py-3 text-slate-400 text-xs hidden sm:table-cell">
                        @if($member->customer?->phone)
                        @php
                            $ph = $member->customer->phone;
                            $wa = preg_replace('/\D/', '', $ph);
                            if (!str_starts_with($wa, '20')) $wa = '20' . ltrim($wa, '0');
                        @endphp
                        <a href="https://wa.me/{{ $wa }}" target="_blank"
                           class="text-emerald-600 hover:underline font-semibold">{{ $ph }}</a>
                        @else —
                        @endif
                    </td>
                    <td class="px-5 py-3 text-center">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                            {{ $member->shares_count }} نصيب
                        </span>
                    </td>
                    <td class="px-5 py-3 text-center hidden md:table-cell">
                        @if($due > 0)
                        <span class="text-sm font-black text-slate-700">{{ number_format($due, 0) }}</span>
                        <span class="text-xs text-slate-400"> ج.م</span>
                        @else <span class="text-slate-300 text-xs">—</span> @endif
                    </td>
                    <td class="px-5 py-3 text-center hidden md:table-cell">
                        @if($paid > 0)
                        <span class="text-sm font-black text-emerald-600">{{ number_format($paid, 0) }}</span>
                        <span class="text-xs text-slate-400"> ج.م</span>
                        @else <span class="text-slate-300 text-xs">—</span> @endif
                    </td>
                    <td class="px-5 py-3 text-center">
                        @if($contract)
                        <a href="{{ route('udhiya.contracts.show', $contract) }}"
                           class="text-xs font-bold text-indigo-600 hover:underline">
                            📄 {{ $contract->contract_number }}
                        </a>
                        @else
                        <span class="text-xs font-bold text-slate-300">لا يوجد صك</span>
                        @endif
                    </td>
                    @if($isSlaughtered)
                    <td class="px-5 py-3 text-center">
                        @if(!$item)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-black bg-slate-100 text-slate-400 border border-slate-200">
                                — بدون صك
                            </span>
                        @elseif($delivered)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-black bg-emerald-50 text-emerald-700 border border-emerald-200">
                                ✅ استلم {{ $delivered->format('d/m') }}
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-black bg-amber-50 text-amber-700 border border-amber-200 animate-pulse">
                                🕐 لم يستلم
                            </span>
                        @endif
                    </td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Footer stats for slaughtered groups --}}
    @if($isSlaughtered)
    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/30 flex flex-wrap gap-4 text-xs font-bold text-slate-500">
        <span>👥 {{ $totalMembers }} عضو</span>
        <span class="text-emerald-600">✅ استلم: {{ $deliveredCount }}</span>
        @if($withContract - $deliveredCount > 0)
        <span class="text-amber-600">🕐 لم يستلم: {{ $withContract - $deliveredCount }}</span>
        @endif
        @if($noContract > 0)
        <span class="text-slate-400">⚠️ بدون صك: {{ $noContract }}</span>
        @endif
    </div>
    @endif

</div>
@empty
<div class="py-20 text-center bg-white rounded-3xl shadow-sm border border-slate-100">
    <div class="text-6xl mb-4">🔪</div>
    <p class="text-slate-400 font-bold text-lg">لا توجد مجموعات</p>
    <a href="{{ route('udhiya.groups.index') }}"
       class="mt-4 inline-flex items-center gap-2 px-4 py-2 text-sm font-bold rounded-xl bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition-all">
        ← إدارة المجموعات
    </a>
</div>
@endforelse

@endsection
