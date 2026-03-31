@extends('layouts.master')

@section('page-header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8 mt-2">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
            <span class="text-indigo-500 text-4xl">🛒</span> مشترى #{{ $purchase->id }}
        </h1>
        <p class="text-slate-500 font-medium text-sm mt-1">
            <a href="{{ route('udhiya.dashboard') }}" class="text-indigo-500 hover:text-indigo-700 hover:underline">الرئيسية</a>
            / <a href="{{ route('udhiya.purchases.index') }}" class="text-indigo-500 hover:text-indigo-700 hover:underline">المشتريات</a>
            / #{{ $purchase->id }}
        </p>
    </div>
    <div class="flex items-center gap-3 flex-wrap">
        <a href="{{ route('udhiya.purchases.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 transition-all">
            ← المشتريات
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="flex flex-col lg:flex-row gap-6 pb-16">

    {{-- ═══ RIGHT: Sidebar ═══ --}}
    <div class="w-full lg:w-80 flex-shrink-0 flex flex-col gap-5">

        {{-- Purchase Info --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <h6 class="text-base font-black text-slate-800 m-0">📋 تفاصيل المشترى</h6>
                @if($purchase->status === 'confirmed')
                    <span class="text-xs font-black px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 border border-emerald-200">✅ مؤكد</span>
                @else
                    <span class="text-xs font-black px-3 py-1 rounded-full bg-amber-100 text-amber-700 border border-amber-200">⏳ مسودة</span>
                @endif
            </div>
            <div class="px-6 py-5 space-y-4">
                <div class="flex justify-between items-center py-2 border-b border-slate-50">
                    <span class="text-xs font-bold text-slate-500">المورد</span>
                    <span class="text-sm font-black text-slate-800">{{ $purchase->supplier->name }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-slate-50">
                    <span class="text-xs font-bold text-slate-500">التاريخ</span>
                    <span class="text-sm font-black text-slate-800">{{ \Carbon\Carbon::parse($purchase->date)->format('d/m/Y') }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-slate-50">
                    <span class="text-xs font-bold text-slate-500">عدد الأصناف</span>
                    <span class="text-sm font-black text-slate-800">{{ $purchase->items->count() }} صنف</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-slate-50">
                    <span class="text-xs font-bold text-slate-500">الحيوانات</span>
                    <span class="text-sm font-black text-slate-800">{{ $purchase->animals->count() }} حيوان</span>
                </div>
                @if($purchase->notes)
                <div class="pt-1">
                    <span class="text-xs font-bold text-slate-500 block mb-1.5">ملاحظات</span>
                    <p class="text-sm text-slate-600 bg-slate-50 rounded-xl px-3 py-2">{{ $purchase->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Financial Summary --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-indigo-50/60">
                <h6 class="text-base font-black text-indigo-900 m-0">💰 الملخص المالي</h6>
            </div>
            <div class="px-6 py-5 space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-xs font-bold text-slate-500">إجمالي الفاتورة</span>
                    <span class="text-lg font-black text-slate-800">{{ number_format($purchase->total, 0) }} <span class="text-xs text-slate-400">ج.م</span></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs font-bold text-slate-500">المدفوع للمورد</span>
                    <span class="text-lg font-black text-emerald-600">{{ number_format($purchase->paid, 0) }} <span class="text-xs text-emerald-400">ج.م</span></span>
                </div>

                @php $remaining = $purchase->total - $purchase->paid; @endphp
                <div class="flex justify-between items-center">
                    <span class="text-xs font-bold text-slate-500">المتبقي</span>
                    <span class="text-lg font-black {{ $remaining > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                        {{ number_format($remaining, 0) }} <span class="text-xs {{ $remaining > 0 ? 'text-rose-400' : 'text-emerald-400' }}">ج.م</span>
                    </span>
                </div>

                @if($purchase->total > 0)
                @php $paidPct = min(100, ($purchase->paid / $purchase->total) * 100); @endphp
                <div class="pt-1">
                    <div class="flex justify-between text-xs font-bold text-slate-400 mb-1.5">
                        <span>نسبة السداد</span>
                        <span>{{ number_format($paidPct, 0) }}%</span>
                    </div>
                    <div class="h-2.5 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all {{ $paidPct >= 100 ? 'bg-emerald-500' : 'bg-amber-400' }}"
                             style="width: {{ $paidPct }}%"></div>
                    </div>
                </div>
                @endif

                @if($remaining <= 0)
                <div class="pt-2 flex items-center gap-2 text-emerald-700 bg-emerald-50 rounded-xl px-3 py-2.5">
                    <span class="text-lg">✅</span>
                    <span class="text-xs font-black">تم سداد الفاتورة بالكامل</span>
                </div>
                @else
                <div class="pt-2 flex items-center gap-2 text-amber-700 bg-amber-50 rounded-xl px-3 py-2.5">
                    <span class="text-lg">⚠️</span>
                    <span class="text-xs font-black">متبقي {{ number_format($remaining, 0) }} ج.م للمورد</span>
                </div>
                @endif
            </div>
        </div>

    </div>

    {{-- ═══ LEFT: Main Content ═══ --}}
    <div class="flex-1 min-w-0 flex flex-col gap-6">

        {{-- Items Table --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <h6 class="text-base font-black text-slate-800 m-0">📦 أصناف المشترى</h6>
                <span class="text-xs font-bold text-slate-400">{{ $purchase->items->count() }} صنف</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-right">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs font-bold">
                            <th class="px-5 py-3">المنتج</th>
                            <th class="px-5 py-3">الكمية</th>
                            <th class="px-5 py-3">الوزن</th>
                            <th class="px-5 py-3">سعر الوحدة</th>
                            <th class="px-5 py-3 text-left">الإجمالي</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($purchase->items as $item)
                        <tr class="hover:bg-indigo-50/20 transition-colors">
                            <td class="px-5 py-3">
                                <div class="text-sm font-black text-slate-800">{{ $item->product->name }}</div>
                                <div class="text-xs text-slate-400 font-semibold">{{ $item->product->mainCategory->name }}</div>
                            </td>
                            <td class="px-5 py-3 text-sm font-bold text-slate-700">{{ $item->quantity }}</td>
                            <td class="px-5 py-3 text-sm font-bold text-slate-700">
                                {{ $item->weight ? number_format($item->weight, 1) . ' كجم' : '—' }}
                            </td>
                            <td class="px-5 py-3 text-sm font-bold text-slate-700">
                                {{ number_format($item->cost_per_unit, 2) }}
                                <span class="text-xs text-slate-400">ج.م</span>
                            </td>
                            <td class="px-5 py-3 text-left text-base font-black text-indigo-700">
                                {{ number_format($item->total, 2) }}
                                <span class="text-xs text-indigo-400 font-normal">ج.م</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-indigo-50/60 border-t-2 border-indigo-100">
                            <td colspan="4" class="px-5 py-3 text-sm font-black text-slate-700">الإجمالي</td>
                            <td class="px-5 py-3 text-left text-lg font-black text-indigo-700">
                                {{ number_format($purchase->items->sum('total'), 2) }}
                                <span class="text-xs text-indigo-400 font-normal">ج.م</span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Animals Table --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <h6 class="text-base font-black text-slate-800 m-0">🐄 الحيوانات المنشأة</h6>
                <span class="text-xs font-bold text-slate-400">{{ $purchase->animals->count() }} حيوان</span>
            </div>
            <div class="overflow-x-auto">
                @if($purchase->animals->isEmpty())
                <div class="py-16 text-center">
                    <div class="text-5xl mb-4">🐄</div>
                    <p class="text-slate-400 font-bold">لا توجد حيوانات مرتبطة بهذا المشترى</p>
                    <a href="{{ route('udhiya.animals.index') }}"
                       class="mt-4 inline-flex items-center gap-2 px-4 py-2 text-sm font-bold rounded-xl bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition-all">
                        ← إدارة الحيوانات
                    </a>
                </div>
                @else
                <table class="w-full text-right">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs font-bold">
                            <th class="px-5 py-3">الكود</th>
                            <th class="px-5 py-3">النوع</th>
                            <th class="px-5 py-3">المخزن</th>
                            <th class="px-5 py-3">التكلفة</th>
                            <th class="px-5 py-3">الحالة</th>
                            <th class="px-5 py-3 w-12"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($purchase->animals as $animal)
                        @php
                            $statusColors = [
                                'available'           => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                'partially_allocated' => 'bg-amber-100 text-amber-700 border-amber-200',
                                'fully_allocated'     => 'bg-rose-100 text-rose-700 border-rose-200',
                                'slaughtered'         => 'bg-slate-100 text-slate-600 border-slate-200',
                            ];
                            $statusColor = $statusColors[$animal->status] ?? 'bg-slate-100 text-slate-600 border-slate-200';
                        @endphp
                        <tr class="hover:bg-indigo-50/20 transition-colors">
                            <td class="px-5 py-3">
                                <span class="text-sm font-black text-indigo-700">{{ $animal->code }}</span>
                            </td>
                            <td class="px-5 py-3">
                                <div class="text-sm font-bold text-slate-800">{{ $animal->product->name }}</div>
                                <div class="text-xs text-slate-400">{{ $animal->product->mainCategory->name }}</div>
                            </td>
                            <td class="px-5 py-3 text-sm font-semibold text-slate-600">
                                {{ $animal->warehouse->name }}
                            </td>
                            <td class="px-5 py-3 text-sm font-black text-slate-800">
                                {{ number_format($animal->cost, 0) }}
                                <span class="text-xs text-slate-400 font-normal">ج.م</span>
                            </td>
                            <td class="px-5 py-3">
                                <span class="text-xs font-black px-2.5 py-1 rounded-full border {{ $statusColor }}">
                                    {{ \App\Models\Animal::STATUS_LABELS[$animal->status] ?? $animal->status }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-center">
                                <a href="{{ route('udhiya.animals.show', $animal) }}"
                                   class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-500 hover:bg-indigo-600 hover:text-white inline-flex items-center justify-center transition-colors text-sm"
                                   title="عرض الحيوان">
                                    👁
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-slate-50/60 border-t border-slate-100">
                            <td colspan="3" class="px-5 py-3 text-xs font-bold text-slate-500">إجمالي تكلفة الحيوانات</td>
                            <td class="px-5 py-3 text-sm font-black text-slate-800">
                                {{ number_format($purchase->animals->sum('cost'), 0) }}
                                <span class="text-xs text-slate-400 font-normal">ج.م</span>
                            </td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
