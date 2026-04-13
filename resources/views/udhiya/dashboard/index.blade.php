@extends('layouts.master')

@section('page-header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-8 mb-16">
    <div>
        <h1 class="text-2xl font-black text-slate-800 tracking-tight flex items-center gap-4"><span class="page-title-emoji">📊</span> لوحة التحكم</h1>
        <ol class="breadcrumb">
            <span>برنامج الأضاحي 🐄</span>
        </ol>
    </div>
    <div class="flex h-full items-center">
        <span style="font-size:.82rem;color:var(--text-slate-500);">📅 {{ now()->format('Y/m/d') }}</span>
    </div>
</div>
@endsection

@section('content')

{{-- Stats Row 1 --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 sm:gap-12 mb-12">
    <div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 border-b-4 border-b-purple-500 hover:shadow-md transition-all bg-white rounded-2xl shadow-sm border-b-4 h-full p-10 hover:-translate-y-1 transition-transform duration-300">
            <div class="w-12 h-12 rounded-xl bg-slate-50 flex items-center justify-center text-2xl mb-4 shadow-sm">🐄</div>
            <div class="text-3xl font-black text-slate-800 mb-1 tracking-tight">{{ $stats['animals_total'] }}</div>
            <div class="text-sm font-semibold text-slate-500 uppercase">إجمالي الحيوانات</div>
        </div>
    </div>
    <div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 border-b-4 border-b-emerald-500 hover:shadow-md transition-all bg-white rounded-2xl shadow-sm border-b-4 h-full p-10 hover:-translate-y-1 transition-transform duration-300">
            <div class="w-12 h-12 rounded-xl bg-slate-50 flex items-center justify-center text-2xl mb-4 shadow-sm">✅</div>
            <div class="text-3xl font-black text-slate-800 mb-1 tracking-tight">{{ $stats['animals_available'] }}</div>
            <div class="text-sm font-semibold text-slate-500 uppercase">متاح للبيع</div>
        </div>
    </div>
    <div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 border-b-4 border-b-orange-500 hover:shadow-md transition-all bg-white rounded-2xl shadow-sm border-b-4 h-full p-10 hover:-translate-y-1 transition-transform duration-300">
            <div class="w-12 h-12 rounded-xl bg-slate-50 flex items-center justify-center text-2xl mb-4 shadow-sm">📋</div>
            <div class="text-3xl font-black text-slate-800 mb-1 tracking-tight">{{ $stats['contracts_active'] }}</div>
            <div class="text-sm font-semibold text-slate-500 uppercase">صكوك نشطة</div>
        </div>
    </div>
    <div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 border-b-4 border-b-pink-500 hover:shadow-md transition-all bg-white rounded-2xl shadow-sm border-b-4 h-full p-10 hover:-translate-y-1 transition-transform duration-300">
            <div class="w-12 h-12 rounded-xl bg-slate-50 flex items-center justify-center text-2xl mb-4 shadow-sm">🙋</div>
            <div class="text-3xl font-black text-slate-800 mb-1 tracking-tight">{{ $stats['customers_total'] }}</div>
            <div class="text-sm font-semibold text-slate-500 uppercase">إجمالي العملاء</div>
        </div>
    </div>
</div>

{{-- Stats Row 2 --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 sm:gap-12 mb-12">
    <div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 border-b-4 border-b-blue-500 hover:shadow-md transition-all bg-white rounded-2xl shadow-sm border-b-4 h-full p-10 hover:-translate-y-1 transition-transform duration-300">
            <div class="w-12 h-12 rounded-xl bg-slate-50 flex items-center justify-center text-2xl mb-4 shadow-sm">💰</div>
            <div class="text-3xl font-black text-slate-800 mb-1 tracking-tight">{{ number_format($stats['revenue_total']/1000,1) }}k</div>
            <div class="text-sm font-semibold text-slate-500 uppercase">إجمالي المبيعات ج.م</div>
        </div>
    </div>
    <div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 border-b-4 border-b-teal-500 hover:shadow-md transition-all bg-white rounded-2xl shadow-sm border-b-4 h-full p-10 hover:-translate-y-1 transition-transform duration-300">
            <div class="w-12 h-12 rounded-xl bg-slate-50 flex items-center justify-center text-2xl mb-4 shadow-sm">💵</div>
            <div class="text-3xl font-black text-slate-800 mb-1 tracking-tight">{{ number_format($stats['collected_total']/1000,1) }}k</div>
            <div class="text-sm font-semibold text-slate-500 uppercase">المحصّل ج.م</div>
        </div>
    </div>
    <div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 border-b-4 border-b-orange-500 hover:shadow-md transition-all bg-white rounded-2xl shadow-sm border-b-4 h-full p-10 hover:-translate-y-1 transition-transform duration-300">
            <div class="w-12 h-12 rounded-xl bg-slate-50 flex items-center justify-center text-2xl mb-4 shadow-sm">⏳</div>
            <div class="text-3xl font-black text-slate-800 mb-1 tracking-tight">{{ number_format($stats['remaining_total']/1000,1) }}k</div>
            <div class="text-sm font-semibold text-slate-500 uppercase">المتبقي ج.م</div>
        </div>
    </div>
    <div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 border-b-4 border-b-emerald-500 hover:shadow-md transition-all bg-white rounded-2xl shadow-sm border-b-4 h-full p-10 hover:-translate-y-1 transition-transform duration-300">
            <div class="w-12 h-12 rounded-xl bg-slate-50 flex items-center justify-center text-2xl mb-4 shadow-sm">🏦</div>
            <div class="text-3xl font-black text-slate-800 mb-1 tracking-tight">{{ number_format($stats['treasury_balance']/1000,1) }}k</div>
            <div class="text-sm font-semibold text-slate-500 uppercase">رصيد الخزينة ج.م</div>
        </div>
    </div>
</div>

{{-- Collection Progress --}}
@if($stats['revenue_total'] > 0)
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-12 flex flex-col h-full hover:shadow-md transition-shadow duration-300">
    <div class="p-12 flex-1">
        <div class="flex justify-between items-center mb-4">
            <span style="font-weight:700;font-size:.9rem;">💹 نسبة التحصيل</span>
            <span style="font-weight:700;color:var(--primary);">
                {{ number_format(($stats['collected_total']/$stats['revenue_total'])*100,1) }}%
            </span>
        </div>
        <div class="w-full bg-slate-200 rounded-full h-2.5 overflow-hidden">
            <div class="bg-indigo-600 h-2.5 rounded-full" " style="width:{{ ($stats['collected_total']/$stats['revenue_total'])*100 }}%"></div>
        </div>
        <div class="flex justify-between mt-4" style="font-size:.78rem;color:var(--text-slate-500);">
            <span>محصّل: {{ number_format($stats['collected_total']) }} ج.م</span>
            <span>إجمالي: {{ number_format($stats['revenue_total']) }} ج.م</span>
        </div>
    </div>
</div>
@endif

{{-- Available Animals Section --}}
<div class="mb-12">
    <h2 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">
        <span>🐄</span> الحيوانات المتاحة
    </h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($availableAnimals as $animal)
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <div class="text-sm font-semibold text-slate-500 uppercase">{{ $animal->product->name ?? 'غير محدد' }}</div>
                    <div class="text-lg font-bold text-slate-800 mt-1">{{ $animal->code }}</div>
                </div>
                @if($animal->is_grouped)
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium bg-indigo-100 text-indigo-800">🔗 مجمع</span>
                @else
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium bg-green-100 text-green-800">✅ مستقل</span>
                @endif
            </div>

            <div class="space-y-2 mb-4">
                <div class="flex justify-between text-sm">
                    <span class="text-slate-600">السعر الكامل:</span>
                    <span class="font-semibold text-slate-800">{{ number_format($animal->price_full ?? 0) }} ج.م</span>
                </div>
                @if($animal->is_grouped && $animal->shareSetting)
                <div class="flex justify-between text-sm">
                    <span class="text-slate-600">نوع النصيب:</span>
                    <span class="font-semibold text-indigo-600">{{ $animal->shareSetting->share_type }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-600">الأنصبة المتبقية:</span>
                    <span class="font-semibold text-orange-600">{{ $animal->shareSetting->remaining_shares ?? 0 }}</span>
                </div>
                @endif
            </div>

            @if($animal->is_grouped && $animal->shareSetting && $animal->shareSetting->remaining_shares > 0)
            <button class="w-full bg-indigo-600 text-white py-2 rounded-lg font-semibold text-sm hover:bg-indigo-700 transition-all" onclick="showRequestModal({{ $animal->id }}, '{{ $animal->code }}', '{{ $animal->shareSetting->share_type }}')">
                🎯 تقديم على نصيب
            </button>
            @else
                <button disabled class="w-full bg-slate-300 text-slate-500 py-2 rounded-lg font-semibold text-sm cursor-not-allowed">
                    ❌ غير متاح
                </button>
            @endif
        </div>
        @empty
        <div class="col-span-full text-center py-12">
            <div class="flex flex-col items-center justify-center text-slate-400">
                <span class="text-4xl mb-3">📭</span>
                <p class="text-lg font-medium">لا توجد حيوانات متاحة حالياً</p>
            </div>
        </div>
        @endforelse
    </div>
</div>

{{-- Pending Contract Requests Section --}}
@if($pendingRequests->count() > 0)
<div class="mb-12 bg-red-50 border-2 border-red-200 rounded-2xl p-6 shadow-sm">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-bold text-red-900 flex items-center gap-2">
            <span>🔴</span> طلبات الاشتراك المعلقة ({{ $pendingRequests->count() }})
        </h2>
        <a href="{{ route('udhiya.contract-requests.index') }}" class="text-red-600 hover:text-red-700 text-sm font-semibold bg-red-100 px-4 py-2 rounded-lg hover:bg-red-200">عرض الكل →</a>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($pendingRequests->take(6) as $req)
        <div class="bg-white rounded-lg p-4 border-2 border-red-200 hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <div class="font-semibold text-slate-800">{{ $req->customer_name }}</div>
                    <div class="text-xs text-slate-500">{{ $req->customer_phone }}</div>
                </div>
                <span class="text-xs font-medium bg-red-100 text-red-800 px-2 py-1 rounded">{{ $req->animal->code }}</span>
            </div>
            <div class="text-sm text-slate-600 mb-2">
                <strong>النصيب:</strong> {{ $req->share_type }}
            </div>
            @if($req->share_price)
            <div class="text-sm font-semibold text-indigo-600 mb-2">
                {{ number_format($req->share_price) }} ج.م
            </div>
            @endif
            <div class="text-xs text-slate-500">
                {{ $req->created_at->diffForHumans() }}
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Recent Tables --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 sm:gap-12 mb-12">
    <div class="col-span-1 lg:col-span-6">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-12 flex flex-col h-full hover:shadow-md transition-shadow duration-300">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-8 text-slate-800 font-bold">
                <span>📋 آخر الصكوك</span>
                <a href="{{ route('udhiya.contracts.index') }}" class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-semibold rounded-lg transition-all bg-indigo-600 text-white hover:bg-indigo-700 ring-indigo-600 focus:ring-offset-2 focus:ring-2 border border-transparent shadow-sm">عرض الكل</a>
            </div>
            <div class="p-12 flex-1 p-0">
                <div class="overflow-x-auto ring-1 ring-slate-100 sm:rounded-lg">
                    <table class="min-w-full text-end text-sm text-slate-500">
                        <thead class="text-xs text-slate-600 uppercase bg-slate-100/80 font-bold border-b border-slate-200"><tr><th class="px-6 py-4 font-bold tracking-wider">رقم الصك</th><th class="px-6 py-4 font-bold tracking-wider">العميل</th><th class="px-6 py-4 font-bold tracking-wider">المبلغ</th><th class="px-6 py-4 font-bold tracking-wider">الحالة</th></tr></thead>
                        <tbody>
                            @forelse($recentContracts as $c)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap"><strong style="color:var(--primary);">{{ $c->contract_number }}</strong></td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $c->customer->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ number_format($c->total_amount) }} <small class="text-slate-500">ج.م</small></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($c->status==='active') <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium bg-orange-100 text-orange-800 ring-1 ring-inset ring-orange-600/20">🟡 نشط</span>
                                    @elseif($c->status==='completed') <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium bg-emerald-100 text-emerald-800 ring-1 ring-inset ring-emerald-600/20">✅ مكتمل</span>
                                    @else <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium bg-slate-100 text-slate-800 ring-1 ring-inset ring-slate-600/20">{{ $c->status }}</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition-colors"><td colspan="10" class="text-center py-12"><div class="flex flex-col items-center justify-center text-slate-400"><span class="text-4xl mb-3">📭</span><p class="text-lg font-medium">لا توجد بيانات</p></div></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-span-1 lg:col-span-6">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-12 flex flex-col h-full hover:shadow-md transition-shadow duration-300">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-8 text-slate-800 font-bold">
                <span>🛒 آخر المشتريات</span>
                <a href="{{ route('udhiya.purchases.index') }}" class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-semibold rounded-lg transition-all bg-indigo-600 text-white hover:bg-indigo-700 ring-indigo-600 focus:ring-offset-2 focus:ring-2 border border-transparent shadow-sm">عرض الكل</a>
            </div>
            <div class="p-12 flex-1 p-0">
                <div class="overflow-x-auto ring-1 ring-slate-100 sm:rounded-lg">
                    <table class="min-w-full text-end text-sm text-slate-500">
                        <thead class="text-xs text-slate-600 uppercase bg-slate-100/80 font-bold border-b border-slate-200"><tr><th class="px-6 py-4 font-bold tracking-wider">المورد</th><th class="px-6 py-4 font-bold tracking-wider">التاريخ</th><th class="px-6 py-4 font-bold tracking-wider">الإجمالي</th><th class="px-6 py-4 font-bold tracking-wider">الحالة</th></tr></thead>
                        <tbody>
                            @forelse($recentPurchases as $p)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap"><strong>{{ $p->supplier->name }}</strong></td>
                                <td style="font-size:.82rem;color:var(--text-slate-500);">{{ $p->date }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ number_format($p->total) }} <small class="text-slate-500">ج.م</small></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($p->status==='confirmed') <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium bg-emerald-100 text-emerald-800 ring-1 ring-inset ring-emerald-600/20">✅ مؤكدة</span>
                                    @elseif($p->status==='pending') <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium bg-orange-100 text-orange-800 ring-1 ring-inset ring-orange-600/20">⏳ معلقة</span>
                                    @else <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium bg-slate-100 text-slate-800 ring-1 ring-inset ring-slate-600/20">{{ $p->status }}</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition-colors"><td colspan="10" class="text-center py-12"><div class="flex flex-col items-center justify-center text-slate-400"><span class="text-4xl mb-3">📭</span><p class="text-lg font-medium">لا توجد بيانات</p></div></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Contract Request --}}
<div id="requestModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4" onclick="if(event.target===this) closeRequestModal()">
    <div class="bg-white rounded-2xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-slate-200 px-8 py-6 flex justify-between items-center">
            <h2 class="text-2xl font-bold text-slate-800">📝 تقديم على نصيب</h2>
            <button onclick="closeRequestModal()" class="text-slate-500 hover:text-slate-700 text-2xl">×</button>
        </div>

        <form id="requestForm" method="POST" action="{{ route('udhiya.contract-requests.store') }}" class="p-8 space-y-6">
            @csrf
            <input type="hidden" id="animalId" name="animal_id">

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">🐄 الحيوان</label>
                <input type="text" id="animalCode" disabled class="w-full px-4 py-2 rounded-lg bg-slate-100 text-slate-600 font-semibold">
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">📍 نوع النصيب</label>
                <input type="text" id="shareType" disabled class="w-full px-4 py-2 rounded-lg bg-slate-100 text-slate-600 font-semibold">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">👤 اسم العميل *</label>
                    <input type="text" name="customer_name" required class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">📱 رقم الهاتف *</label>
                    <input type="tel" name="customer_phone" required class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">📧 البريد الإلكتروني</label>
                <input type="email" name="customer_email" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 outline-none transition">
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">💰 السعر المتوقع (اختياري)</label>
                <input type="number" name="share_price" min="0" step="0.01" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 outline-none transition" placeholder="اترك فارغاً للتحديد لاحقاً">
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">📝 ملاحظات (اختياري)</label>
                <textarea name="notes" rows="3" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 outline-none transition resize-none" placeholder="أي معلومات إضافية تود إضافتها"></textarea>
            </div>

            <div class="flex gap-4 pt-4">
                <button type="submit" class="flex-1 bg-indigo-600 text-white py-3 rounded-lg font-semibold hover:bg-indigo-700 transition-all shadow-sm">
                    ✅ إرسال الطلب
                </button>
                <button type="button" onclick="closeRequestModal()" class="flex-1 bg-slate-100 text-slate-700 py-3 rounded-lg font-semibold hover:bg-slate-200 transition-all">
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showRequestModal(animalId, animalCode, shareType) {
    document.getElementById('animalId').value = animalId;
    document.getElementById('animalCode').value = animalCode;
    document.getElementById('shareType').value = shareType;
    document.getElementById('requestModal').classList.remove('hidden');
    document.getElementById('requestModal').classList.add('flex');
}

function closeRequestModal() {
    document.getElementById('requestModal').classList.add('hidden');
    document.getElementById('requestModal').classList.remove('flex');
    document.getElementById('requestForm').reset();
}
</script>

@endsection
