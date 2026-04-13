@extends('layouts.master')

@section('page-header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-8 mb-16">
    <div>
        <h1 class="text-2xl font-black text-slate-800 tracking-tight flex items-center gap-4"><span class="page-title-emoji">📋</span> طلبات الاشتراك</h1>
        <ol class="breadcrumb">
            <a href="{{ route('udhiya.dashboard') }}">لوحة التحكم</a>
            <span>طلبات الاشتراك</span>
        </ol>
    </div>
    <div class="flex h-full items-center gap-4">
        <a href="{{ route('udhiya.dashboard') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold rounded-lg transition-all bg-slate-100 text-slate-700 hover:bg-slate-200">
            ← العودة
        </a>
    </div>
</div>
@endsection

@section('content')

{{-- Filters --}}
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 mb-8">
    <form method="GET" class="flex flex-col sm:flex-row gap-4">
        <div class="flex-1">
            <label class="block text-xs font-semibold text-slate-600 mb-2">الحالة</label>
            <select name="status" class="w-full px-4 py-2 rounded-lg border border-slate-300 text-slate-700">
                <option value="">الكل</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>⏳ معلقة</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>✅ موافق عليها</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>❌ مرفوضة</option>
                <option value="converted" {{ request('status') === 'converted' ? 'selected' : '' }}>📋 تحويل لصك</option>
            </select>
        </div>
        <div class="flex gap-2 items-end">
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700">
                🔍 بحث
            </button>
            <a href="{{ route('udhiya.contract-requests.index') }}" class="px-6 py-2 bg-slate-100 text-slate-700 rounded-lg font-semibold hover:bg-slate-200">
                🔄 إعادة تعيين
            </a>
        </div>
    </form>
</div>

{{-- Requests Table --}}
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
        <h2 class="text-slate-800 font-bold">{{ $requests->total() }} طلب</h2>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-end text-sm text-slate-500">
            <thead class="text-xs text-slate-600 uppercase bg-slate-100/80 font-bold border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4 font-bold tracking-wider">العميل</th>
                    <th class="px-6 py-4 font-bold tracking-wider">الهاتف</th>
                    <th class="px-6 py-4 font-bold tracking-wider">الحيوان</th>
                    <th class="px-6 py-4 font-bold tracking-wider">النصيب</th>
                    <th class="px-6 py-4 font-bold tracking-wider">السعر</th>
                    <th class="px-6 py-4 font-bold tracking-wider">الحالة</th>
                    <th class="px-6 py-4 font-bold tracking-wider">التاريخ</th>
                    <th class="px-6 py-4 font-bold tracking-wider">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $request)
                <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <strong class="text-slate-800">{{ $request->customer_name }}</strong>
                        @if($request->customer_email)
                            <div class="text-xs text-slate-500">{{ $request->customer_email }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <a href="tel:{{ $request->customer_phone }}" class="text-indigo-600 hover:underline">
                            {{ $request->customer_phone }}
                        </a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="font-semibold text-slate-800">{{ $request->animal->code }}</span>
                        <div class="text-xs text-slate-500">{{ $request->animal->product->name ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium bg-purple-100 text-purple-800">
                            {{ ucfirst($request->share_type) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($request->share_price)
                            <strong class="text-slate-800">{{ number_format($request->share_price) }} ج.م</strong>
                        @else
                            <span class="text-slate-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium
                            @if($request->status === 'pending') bg-orange-100 text-orange-800
                            @elseif($request->status === 'approved') bg-emerald-100 text-emerald-800
                            @elseif($request->status === 'rejected') bg-red-100 text-red-800
                            @else bg-blue-100 text-blue-800
                            @endif
                        ">
                            {{ $request->statusLabel() }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-500">
                        {{ $request->created_at->format('Y/m/d') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex gap-2">
                            @if($request->status === 'pending')
                            <button onclick="updateStatus({{ $request->id }}, 'approved')" class="px-2 py-1 text-xs font-semibold bg-emerald-100 text-emerald-800 rounded hover:bg-emerald-200" title="قبول">
                                ✅
                            </button>
                            <button onclick="updateStatus({{ $request->id }}, 'rejected')" class="px-2 py-1 text-xs font-semibold bg-red-100 text-red-800 rounded hover:bg-red-200" title="رفض">
                                ❌
                            </button>
                            @endif
                            @if($request->status === 'approved')
                            <form method="POST" action="{{ route('udhiya.contract-requests.convert', $request) }}" style="display:inline;">
                                @csrf
                                <button type="submit" class="px-2 py-1 text-xs font-semibold bg-indigo-100 text-indigo-800 rounded hover:bg-indigo-200" title="تحويل لصك">
                                    📋
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-12">
                        <div class="flex flex-col items-center justify-center text-slate-400">
                            <span class="text-4xl mb-3">📭</span>
                            <p class="text-lg font-medium">لا توجد طلبات</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($requests->hasPages())
    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
        {{ $requests->links() }}
    </div>
    @endif
</div>

<script>
function updateStatus(requestId, status) {
    if (!confirm('هل أنت متأكد؟')) return;

    fetch(`/udhiya/contract-requests/${requestId}/status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ status: status })
    })
    .then(() => location.reload())
    .catch(err => alert('حدث خطأ: ' + err));
}
</script>

@endsection
