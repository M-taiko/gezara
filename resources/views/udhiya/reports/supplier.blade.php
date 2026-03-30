@extends('layouts.master')
@section('page-header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-8 mb-16">
    <div>
        <h1 class="text-2xl font-black text-slate-800 tracking-tight flex items-center gap-4">كشف حساب مورد — {{ $supplier->name }}</h1>
        <div class="mt-2 flex items-center text-sm text-slate-500"><li class="breadcrumb-item"><a href="{{ route('udhiya.reports.index') }}">التقارير</a></li><span>{{ $supplier->name }}</span></div>
    </div>
</div>
@endsection
@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-12 mb-12">
    <div><div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-12 flex flex-col h-full hover:shadow-md transition-shadow duration-300"><div class="p-12 flex-1 text-center"><h4>{{ number_format($totalPurchases, 2) }}</h4><small class="text-slate-500">إجمالي المشتريات (ج.م)</small></div></div></div>
    <div><div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-12 flex flex-col h-full hover:shadow-md transition-shadow duration-300"><div class="p-12 flex-1 text-center"><h4 class="text-emerald-600">{{ number_format($totalPaid, 2) }}</h4><small class="text-slate-500">المدفوع (ج.م)</small></div></div></div>
    <div><div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-12 flex flex-col h-full hover:shadow-md transition-shadow duration-300"><div class="p-12 flex-1 text-center"><h4 class="{{ $balance > 0 ? 'text-rose-600' : 'text-emerald-600' }}">{{ number_format($balance, 2) }}</h4><small class="text-slate-500">الرصيد المتبقي (ج.م)</small></div></div></div>
    <div><div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-12 flex flex-col h-full hover:shadow-md transition-shadow duration-300"><div class="p-12 flex-1 text-center"><h4>{{ $supplier->purchases->count() }}</h4><small class="text-slate-500">عدد المشتريات</small></div></div></div>
</div>
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-12 flex flex-col h-full hover:shadow-md transition-shadow duration-300">
    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-8 text-slate-800 font-bold"><h6 class="card-title mb-0">المشتريات</h6></div>
    <div class="p-12 flex-1 p-0">
        <div class="overflow-x-auto ring-1 ring-slate-100 sm:rounded-lg">
            <table class="min-w-full text-end text-sm text-slate-500">
                <thead class="thead-light"><tr><th class="px-6 py-4 font-bold tracking-wider">#</th><th class="px-6 py-4 font-bold tracking-wider">التاريخ</th><th class="px-6 py-4 font-bold tracking-wider">الإجمالي</th><th class="px-6 py-4 font-bold tracking-wider">المدفوع</th><th class="px-6 py-4 font-bold tracking-wider">المتبقي</th><th class="px-6 py-4 font-bold tracking-wider"></th></tr></thead>
                <tbody>
                    @foreach($supplier->purchases as $purchase)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $purchase->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $purchase->date }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ number_format($purchase->total, 2) }} ج.م</td>
                        <td class="text-emerald-600">{{ number_format($purchase->paid, 2) }} ج.م</td>
                        <td class="{{ ($purchase->total - $purchase->paid) > 0 ? 'text-rose-600' : 'text-emerald-600' }}">{{ number_format($purchase->total - $purchase->paid, 2) }} ج.م</td>
                        <td class="px-6 py-4 whitespace-nowrap"><a href="{{ route('udhiya.purchases.show', $purchase) }}" class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-semibold rounded-lg transition-all btn-outline-info"><i class="fas fa-eye"></i></a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
