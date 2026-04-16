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
                        @if($request->animal)
                        <span class="font-semibold text-slate-800">{{ $request->animal->code }}</span>
                        <div class="text-xs text-slate-500">{{ $request->animal->product->name ?? '-' }}</div>
                        @else
                        <span class="text-slate-400 italic">لم يتم تحديد الأضحية بعد</span>
                        @endif
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
                            @if($request->status === 'pending' || $request->status === 'approved')
                            <button onclick="openConvertModal({{ $request->id }}, '{{ $request->customer_name }}')" class="px-3 py-1.5 text-xs font-semibold bg-indigo-100 text-indigo-800 rounded hover:bg-indigo-200" title="تحويل لصك">
                                📋 تحويل
                            </button>
                            @endif
                            @if($request->status === 'converted')
                            <span class="px-3 py-1.5 text-xs font-semibold bg-emerald-100 text-emerald-800 rounded">
                                ✅ محول
                            </span>
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

{{-- Convert to Contract Modal --}}
<div id="convertModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-2xl w-full overflow-hidden shadow-xl">
        <div class="px-6 py-5 border-b border-slate-200 bg-gradient-to-r from-indigo-600 to-purple-600 flex items-center justify-between">
            <h6 class="text-xl font-black text-white m-0">تحويل الطلب إلى صك</h6>
            <button type="button" onclick="closeConvertModal()" class="text-white hover:text-slate-200 text-2xl font-bold">×</button>
        </div>

        <form action="#" id="convertForm" method="POST" class="p-6 space-y-4">
            @csrf
            @method('POST')

            <div>
                <p class="text-sm font-semibold text-slate-700 mb-3">
                    <strong id="modalCustomerName">—</strong>
                </p>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">اختر الأضحية <span class="text-rose-500">*</span></label>
                <select name="animal_id" id="animalSelect" required
                        class="w-full rounded-xl border border-slate-300 bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 py-3 px-4 text-sm font-semibold text-slate-800">
                    <option value="">— اختر أضحية —</option>
                    @foreach(\App\Models\Animal::whereIn('status', ['available', 'partially_allocated'])->with('product.mainCategory')->orderBy('code')->get() as $animal)
                    <option value="{{ $animal->id }}">
                        {{ $animal->code }} - {{ $animal->product->name }} ({{ $animal->product->mainCategory?->name }})
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">المجموعة (اختياري)</label>
                <select name="group_id" id="groupSelect"
                        class="w-full rounded-xl border border-slate-300 bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 py-3 px-4 text-sm font-semibold text-slate-800">
                    <option value="">— بدون مجموعة —</option>
                    @foreach(\App\Models\SlaughterGroup::with('animal')->latest()->get() as $group)
                    <option value="{{ $group->id }}">
                        {{ $group->name }} ({{ $group->animal?->code }})
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="submit" class="flex-1 px-4 py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition">
                    ✅ تحويل الآن
                </button>
                <button type="button" onclick="closeConvertModal()" class="flex-1 px-4 py-3 bg-slate-100 text-slate-700 font-bold rounded-xl hover:bg-slate-200 transition">
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openConvertModal(requestId, customerName) {
        document.getElementById('modalCustomerName').textContent = customerName;
        document.getElementById('convertForm').action = `/udhiya/contract-requests/${requestId}/convert`;
        document.getElementById('animalSelect').value = '';
        document.getElementById('groupSelect').value = '';
        document.getElementById('convertModal').classList.remove('hidden');
    }

    function closeConvertModal() {
        document.getElementById('convertModal').classList.add('hidden');
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeConvertModal();
        }
    });

    document.getElementById('convertModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeConvertModal();
        }
    });
</script>

@endsection
