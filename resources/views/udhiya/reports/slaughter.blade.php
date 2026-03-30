@extends('layouts.master')
@section('page-header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-8 mb-16">
    <div>
        <h1 class="text-2xl font-black text-slate-800 tracking-tight flex items-center gap-4">جدول الذبح</h1>
        <div class="mt-2 flex items-center text-sm text-slate-500"><li class="breadcrumb-item"><a href="{{ route('udhiya.reports.index') }}">التقارير</a></li><span>الذبح</span></div>
    </div>
</div>
@endsection
@section('content')
@forelse($contracts as $date => $dayContracts)
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-12 flex flex-col h-full hover:shadow-md transition-shadow duration-300">
    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-8 text-slate-800 font-bold">
        <h6 class="mb-0"><i class="fas fa-calendar-day ml-2"></i>{{ $date }} — {{ $dayContracts->count() }} صك</h6>
    </div>
    <div class="p-12 flex-1 p-0">
        <div class="overflow-x-auto ring-1 ring-slate-100 sm:rounded-lg">
            <table class="min-w-full text-end text-sm text-slate-500">
                <thead class="thead-light"><tr><th class="px-6 py-4 font-bold tracking-wider">الترتيب</th><th class="px-6 py-4 font-bold tracking-wider">رقم الصك</th><th class="px-6 py-4 font-bold tracking-wider">العميل</th><th class="px-6 py-4 font-bold tracking-wider">الهاتف</th><th class="px-6 py-4 font-bold tracking-wider">الحيوانات</th><th class="px-6 py-4 font-bold tracking-wider">الإجمالي</th><th class="px-6 py-4 font-bold tracking-wider"></th></tr></thead>
                <tbody>
                    @foreach($dayContracts->sortBy('slaughter_order') as $c)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $c->slaughter_order ?? '—' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $c->contract_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $c->customer->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $c->customer->phone }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $c->items->count() }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ number_format($c->total_amount, 2) }} ج.م</td>
                        <td class="px-6 py-4 whitespace-nowrap"><a href="{{ route('udhiya.contracts.show', $c) }}" class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-semibold rounded-lg transition-all btn-outline-info"><i class="fas fa-eye"></i></a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@empty
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-12 flex flex-col h-full hover:shadow-md transition-shadow duration-300"><div class="p-12 flex-1 text-center text-slate-500 py-5"><i class="fas fa-calendar fa-3x mb-6"></i><p>لا يوجد جدول ذبح مسجّل</p></div></div>
@endforelse
@endsection
