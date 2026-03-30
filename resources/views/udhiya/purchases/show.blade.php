@extends('layouts.master')
@section('page-header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-8 mb-16">
    <div>
        <h1 class="text-2xl font-black text-slate-800 tracking-tight flex items-center gap-4">مشترى #{{ $purchase->id }}</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('udhiya.purchases.index') }}">المشتريات</a></li>
            <span>#{{ $purchase->id }}</span>
        </ol>
    </div>
</div>
@endsection
@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-12 mb-12">
    <div class="col-span-1 lg:col-span-4">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-12 flex flex-col h-full hover:shadow-md transition-shadow duration-300">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-8 text-slate-800 font-bold"><h6 class="card-title mb-0">تفاصيل المشترى</h6></div>
            <div class="p-12 flex-1">
                <table class="table table-sm table-borderless mb-0">
                    <tr><th class="text-slate-500">المورد</th><td class="px-6 py-4 whitespace-nowrap">{{ $purchase->supplier->name }}</td></tr>
                    <tr><th class="text-slate-500">التاريخ</th><td class="px-6 py-4 whitespace-nowrap">{{ $purchase->date }}</td></tr>
                    <tr><th class="text-slate-500">الإجمالي</th><td class="px-6 py-4 whitespace-nowrap"><strong>{{ number_format($purchase->total, 2) }} ج.م</strong></td></tr>
                    <tr><th class="text-slate-500">المدفوع</th><td class="text-emerald-600">{{ number_format($purchase->paid, 2) }} ج.م</td></tr>
                    <tr><th class="text-slate-500">المتبقي</th><td class="text-rose-600">{{ number_format($purchase->total - $purchase->paid, 2) }} ج.م</td></tr>
                    <tr><th class="text-slate-500">الحالة</th><td class="px-6 py-4 whitespace-nowrap">
                        @if($purchase->status === 'confirmed') <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium bg-emerald-100 text-emerald-800 ring-1 ring-inset ring-emerald-600/20">مؤكد</span>
                        @else <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium bg-slate-100 text-slate-800 ring-1 ring-inset ring-slate-600/20">مسودة</span> @endif
                    </td></tr>
                    @if($purchase->notes)<tr><th class="text-slate-500">ملاحظات</th><td class="px-6 py-4 whitespace-nowrap">{{ $purchase->notes }}</td></tr>@endif
                </table>
            </div>
        </div>
    </div>
    <div class="col-span-1 lg:col-span-8">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-12 flex flex-col h-full hover:shadow-md transition-shadow duration-300">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-8 text-slate-800 font-bold"><h6 class="card-title mb-0">أصناف المشترى</h6></div>
            <div class="p-12 flex-1 p-0">
                <div class="overflow-x-auto ring-1 ring-slate-100 sm:rounded-lg">
                    <table class="min-w-full text-end text-sm text-slate-500">
                        <thead class="thead-light">
                            <tr><th class="px-6 py-4 font-bold tracking-wider">المنتج</th><th class="px-6 py-4 font-bold tracking-wider">الكمية</th><th class="px-6 py-4 font-bold tracking-wider">الوزن</th><th class="px-6 py-4 font-bold tracking-wider">سعر الوحدة</th><th class="px-6 py-4 font-bold tracking-wider">الإجمالي</th></tr>
                        </thead>
                        <tbody>
                            @foreach($purchase->items as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $item->product->mainCategory->name }} — {{ $item->product->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $item->quantity }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $item->weight ? $item->weight . ' كجم' : '—' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ number_format($item->cost_per_unit, 2) }} ج.م</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ number_format($item->total, 2) }} ج.م</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-12 flex flex-col h-full hover:shadow-md transition-shadow duration-300">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-8 text-slate-800 font-bold"><h6 class="card-title mb-0">الحيوانات المنشأة ({{ $purchase->animals->count() }})</h6></div>
            <div class="p-12 flex-1 p-0">
                <div class="overflow-x-auto ring-1 ring-slate-100 sm:rounded-lg">
                    <table class="min-w-full text-end text-sm text-slate-500">
                        <thead class="thead-light">
                            <tr><th class="px-6 py-4 font-bold tracking-wider">الكود</th><th class="px-6 py-4 font-bold tracking-wider">النوع</th><th class="px-6 py-4 font-bold tracking-wider">المخزن</th><th class="px-6 py-4 font-bold tracking-wider">الحالة</th><th class="px-6 py-4 font-bold tracking-wider"></th></tr>
                        </thead>
                        <tbody>
                            @foreach($purchase->animals as $animal)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $animal->code }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $animal->product->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $animal->warehouse->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php $labels = \App\Models\Animal::STATUS_LABELS; @endphp
                                    <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium badge-{{ ['available'=>'success','partially_allocated'=>'warning','fully_allocated'=>'primary','slaughtered'=>'dark'][$animal->status] ?? 'secondary' }}">
                                        {{ $labels[$animal->status] ?? $animal->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap"><a href="{{ route('udhiya.animals.show', $animal) }}" class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-semibold rounded-lg transition-all btn-outline-info"><i class="fas fa-eye"></i></a></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
