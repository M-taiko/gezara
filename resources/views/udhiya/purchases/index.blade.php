@extends('layouts.master')

@section('page-header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-8 mb-16">
    <div>
        <h1 class="text-2xl font-black text-slate-800 tracking-tight flex items-center gap-4"><span class="page-title-emoji">🛒</span> المشتريات</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('udhiya.dashboard') }}">الرئيسية</a></li>
            <span>المشتريات</span>
        </ol>
    </div>
    <div class="flex h-full items-center">
        <a href="{{ route('udhiya.purchases.create') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold rounded-xl transition-all bg-indigo-600 text-white hover:bg-indigo-700 ring-indigo-600 focus:ring-offset-2 focus:ring-2 border border-transparent shadow-sm">
            ➕ مشترى جديد
        </a>
    </div>
</div>
@endsection

@section('content')

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-12 flex flex-col h-full hover:shadow-md transition-shadow duration-300">
    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-8 text-slate-800 font-bold">
        <span>🛒 قائمة المشتريات</span>
        <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium bg-indigo-100 text-indigo-800 ring-1 ring-inset ring-indigo-600/20">{{ $purchases->total() }} فاتورة</span>
    </div>
    <div class="p-12 flex-1 p-0">
        <div class="overflow-x-auto ring-1 ring-slate-100 sm:rounded-lg">
            <table class="min-w-full text-end text-sm text-slate-500">
                <thead>
                    <tr>
                        <th class="px-6 py-4 font-bold tracking-wider">#</th>
                        <th class="px-6 py-4 font-bold tracking-wider">المورد</th>
                        <th class="px-6 py-4 font-bold tracking-wider">التاريخ</th>
                        <th class="px-6 py-4 font-bold tracking-wider">الإجمالي</th>
                        <th class="px-6 py-4 font-bold tracking-wider">المدفوع</th>
                        <th class="px-6 py-4 font-bold tracking-wider">المتبقي</th>
                        <th class="px-6 py-4 font-bold tracking-wider">الحالة</th>
                        <th class="text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $purchase)
                    @php $remaining = $purchase->total - $purchase->paid; @endphp
                    <tr>
                        <td style="color:var(--text-slate-500);font-size:.82rem;">{{ $purchase->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap"><strong>{{ $purchase->supplier->name }}</strong></td>
                        <td style="font-size:.82rem;color:var(--text-slate-500);">{{ $purchase->date }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ number_format($purchase->total) }} <small class="text-slate-500">ج.م</small></td>
                        <td class="px-6 py-4 whitespace-nowrap"><span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium bg-emerald-100 text-emerald-800 ring-1 ring-inset ring-emerald-600/20">{{ number_format($purchase->paid) }} ج.م</span></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($remaining > 0)
                                <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium bg-rose-100 text-rose-800 ring-1 ring-inset ring-rose-600/20">{{ number_format($remaining) }} ج.م</span>
                            @else
                                <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium bg-emerald-100 text-emerald-800 ring-1 ring-inset ring-emerald-600/20">مسدد ✅</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($purchase->status === 'confirmed')
                                <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium bg-emerald-100 text-emerald-800 ring-1 ring-inset ring-emerald-600/20">✅ مؤكدة</span>
                            @elseif($purchase->status === 'pending')
                                <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium bg-orange-100 text-orange-800 ring-1 ring-inset ring-orange-600/20">⏳ معلقة</span>
                            @else
                                <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium bg-slate-100 text-slate-800 ring-1 ring-inset ring-slate-600/20">{{ $purchase->status }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('udhiya.purchases.show', $purchase) }}"
                               class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-semibold rounded-lg transition-all bg-sky-500 text-white hover:bg-sky-600 ring-sky-500 focus:ring-offset-2 focus:ring-2 border border-transparent shadow-sm btn-action" title="عرض">👁️</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <span class="empty-icon">🛒</span>
                                <p>لا توجد مشتريات بعد</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($purchases->hasPages())
    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">{{ $purchases->links() }}</div>
    @endif
</div>

@endsection
