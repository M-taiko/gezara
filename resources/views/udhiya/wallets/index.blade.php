@extends('layouts.master')

@section('page-header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-8 mb-8">
    <div>
        <h1 class="text-4xl font-black text-slate-800 tracking-tight flex items-center gap-3">
            <span class="text-5xl">💰</span> إدارة الخزائن
        </h1>
        <p class="text-slate-500 font-medium text-sm mt-2">
            تتبع أموالك من خلال خزائن منفصلة وآمنة
        </p>
    </div>
    <button type="button" onclick="document.getElementById('addWalletModal').classList.remove('hidden')"
            class="inline-flex items-center gap-2 px-6 py-3 text-sm font-bold rounded-xl bg-gradient-to-r from-indigo-600 to-indigo-700 text-white hover:shadow-lg shadow-md transition-all">
        ➕ خزينة جديدة
    </button>
</div>
@endsection

@section('content')

@if($wallets->isEmpty())
<div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-2xl border-2 border-dashed border-indigo-200 flex flex-col items-center justify-center py-24 text-center">
    <div class="text-8xl mb-4 animate-bounce">💰</div>
    <h2 class="text-2xl font-black text-indigo-900 mb-2">ابدأ الآن!</h2>
    <p class="text-indigo-700 text-base mb-8 max-w-sm">قم بإنشاء خزائنك الأولى لتتمكن من تتبع وإدارة أموالك بكفاءة</p>
    <button type="button" onclick="document.getElementById('addWalletModal').classList.remove('hidden')"
            class="inline-flex items-center gap-2 px-8 py-3 text-base font-bold rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 shadow-lg transition-all">
        ➕ إنشاء خزينتك الأولى
    </button>
</div>
@else

{{-- Summary Stats --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-10">
    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-6 border border-blue-200">
        <div class="text-blue-600 text-3xl mb-2">🏦</div>
        <div class="text-xs font-semibold text-blue-700 uppercase mb-1">عدد الخزائن</div>
        <div class="text-3xl font-black text-blue-900">{{ $wallets->count() }}</div>
    </div>
    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-2xl p-6 border border-green-200">
        <div class="text-green-600 text-3xl mb-2">💵</div>
        <div class="text-xs font-semibold text-green-700 uppercase mb-1">إجمالي الأموال</div>
        <div class="text-3xl font-black text-green-900">{{ number_format($wallets->sum('balance'), 0) }}</div>
    </div>
    <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-2xl p-6 border border-yellow-200">
        <div class="text-yellow-600 text-3xl mb-2">📊</div>
        <div class="text-xs font-semibold text-yellow-700 uppercase mb-1">الخزائن النشطة</div>
        <div class="text-3xl font-black text-yellow-900">{{ $wallets->where('is_active', true)->count() }}</div>
    </div>
    <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl p-6 border border-purple-200">
        <div class="text-purple-600 text-3xl mb-2">📈</div>
        <div class="text-xs font-semibold text-purple-700 uppercase mb-1">معاملات</div>
        <div class="text-3xl font-black text-purple-900">{{ \App\Models\WalletTransaction::count() }}</div>
    </div>
</div>

{{-- Wallets Grid --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
    @foreach($wallets as $wallet)
    <div class="bg-white rounded-2xl shadow-md border border-slate-100 overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
        {{-- Header with Icon --}}
        <div class="h-3 bg-gradient-to-r from-indigo-500 to-blue-500"></div>

        <div class="p-6">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <div class="text-3xl mb-2">{{ substr($wallet->getTypeLabel(), 0, 1) }}</div>
                    <h3 class="text-lg font-black text-slate-800">{{ $wallet->name }}</h3>
                    <p class="text-xs text-slate-500 mt-1">{{ $wallet->notes ?? 'بدون ملاحظات' }}</p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-bold {{$wallet->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700'}}">
                    {{ $wallet->is_active ? '🟢 نشط' : '⚫ معطل' }}
                </span>
            </div>

            {{-- Balance Display --}}
            <div class="bg-gradient-to-r from-slate-50 to-slate-100 rounded-xl p-4 mb-4 border border-slate-200">
                <div class="text-xs text-slate-600 font-semibold mb-1">الرصيد الحالي</div>
                <div class="text-3xl font-black text-indigo-600 font-mono">{{ number_format($wallet->balance, 0) }}<span class="text-sm text-slate-600"> ج.م</span></div>
            </div>

            {{-- Transaction Count --}}
            @if($wallet->transactions->count())
            <div class="text-xs text-slate-600 mb-4 font-semibold">
                📊 {{ $wallet->transactions->count() }} معاملة في هذه الخزينة
            </div>
            @endif

            {{-- Action Buttons --}}
            <div class="flex gap-2">
                <button type="button" onclick="editWallet({{ $wallet->toJson() }})"
                        class="flex-1 px-4 py-2 text-sm font-bold rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition-colors">
                    ✏️ تعديل
                </button>
                @if($wallet->balance == 0)
                <form action="{{ route('udhiya.wallets.destroy', $wallet) }}" method="POST" class="flex-1"
                      onsubmit="return confirm('هل تريد حذف {{ addslashes($wallet->name) }}؟')">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full px-4 py-2 text-sm font-bold rounded-lg bg-rose-600 text-white hover:bg-rose-700 transition-colors">
                        🗑️ حذف
                    </button>
                </form>
                @endif
            </div>
        </div>

        {{-- Last Transactions Preview --}}
        @if($wallet->transactions->count())
        <div class="border-t border-slate-100 bg-slate-50/50 px-6 py-4">
            <div class="font-bold text-slate-700 text-xs mb-3 uppercase">آخر المعاملات</div>
            <div class="space-y-2">
                @foreach($wallet->transactions->take(2) as $tx)
                <div class="flex justify-between items-center p-2 bg-white rounded-lg border border-slate-100">
                    <span class="text-xs font-semibold {{ $tx->type === 'in' ? 'text-green-600' : 'text-red-600' }}">
                        {{ $tx->type === 'in' ? '📥 دخول' : '📤 خروج' }}
                    </span>
                    <span class="text-xs font-bold text-slate-800">{{ number_format($tx->amount, 0) }} ج.م</span>
                    <span class="text-xs text-slate-500">{{ $tx->date->format('m/d') }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    @endforeach
</div>

{{-- Transfer Widget --}}
@if($wallets->count() >= 2)
<div class="bg-gradient-to-r from-indigo-50 to-blue-50 rounded-2xl shadow-md border border-indigo-200 overflow-hidden">
    <div class="px-8 py-6 border-b border-indigo-200 bg-gradient-to-r from-indigo-500 to-blue-500">
        <h2 class="text-2xl font-black text-white flex items-center gap-3">
            <span>🔄</span> تحويل بين الخزائن
        </h2>
        <p class="text-indigo-100 text-sm mt-1">قم بتحويل الأموال من خزينة إلى أخرى بسهولة</p>
    </div>
    <div class="p-8">
        <form action="{{ route('udhiya.wallets.transfer') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @csrf

            {{-- From Wallet --}}
            <div class="space-y-2">
                <label class="block text-sm font-bold text-slate-800 flex items-center gap-2">
                    <span class="text-red-500">📤</span> من الخزينة
                    <span class="text-red-500">*</span>
                </label>
                <select name="from_wallet_id" required class="w-full rounded-lg border-2 border-slate-200 bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 py-3 px-4 text-sm font-semibold text-slate-800 transition-colors">
                    <option value="">— اختر الخزينة —</option>
                    @foreach($wallets->where('is_active', true) as $w)
                    <option value="{{ $w->id }}">{{ $w->getTypeLabel() }} {{ $w->name }} • {{ number_format($w->balance, 0) }} ج.م</option>
                    @endforeach
                </select>
            </div>

            {{-- To Wallet --}}
            <div class="space-y-2">
                <label class="block text-sm font-bold text-slate-800 flex items-center gap-2">
                    <span class="text-green-500">📥</span> إلى الخزينة
                    <span class="text-red-500">*</span>
                </label>
                <select name="to_wallet_id" required class="w-full rounded-lg border-2 border-slate-200 bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 py-3 px-4 text-sm font-semibold text-slate-800 transition-colors">
                    <option value="">— اختر الخزينة —</option>
                    @foreach($wallets->where('is_active', true) as $w)
                    <option value="{{ $w->id }}">{{ $w->getTypeLabel() }} {{ $w->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Amount --}}
            <div class="space-y-2">
                <label class="block text-sm font-bold text-slate-800 flex items-center gap-2">
                    <span>💰</span> المبلغ (ج.م)
                    <span class="text-red-500">*</span>
                </label>
                <input type="number" name="amount" min="0.01" step="0.01" required placeholder="0.00"
                       class="w-full rounded-lg border-2 border-slate-200 bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 py-3 px-4 text-sm font-bold text-slate-800 text-center transition-colors" dir="ltr">
            </div>

            {{-- Date --}}
            <div class="space-y-2">
                <label class="block text-sm font-bold text-slate-800 flex items-center gap-2">
                    <span>📅</span> التاريخ
                    <span class="text-red-500">*</span>
                </label>
                <input type="date" name="date" value="{{ date('Y-m-d') }}" required
                       class="w-full rounded-lg border-2 border-slate-200 bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 py-3 px-4 text-sm font-semibold text-slate-800 transition-colors">
            </div>

            {{-- Notes --}}
            <div class="md:col-span-2 space-y-2">
                <label class="block text-sm font-bold text-slate-800 flex items-center gap-2">
                    <span>📝</span> ملاحظات
                </label>
                <input type="text" name="notes" placeholder="مثال: تحويل يومي، دفع مستحقات..."
                       class="w-full rounded-lg border-2 border-slate-200 bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 py-3 px-4 text-sm font-semibold text-slate-800 transition-colors">
            </div>

            {{-- Buttons --}}
            <div class="md:col-span-2 flex justify-end gap-3 pt-4 border-t-2 border-slate-200">
                <button type="button" onclick="document.querySelectorAll('form')[1].reset()"
                        class="px-6 py-2.5 rounded-lg text-sm font-bold text-slate-700 bg-white border-2 border-slate-300 hover:bg-slate-50 transition-all">
                    🔄 مسح
                </button>
                <button type="submit" class="px-8 py-2.5 rounded-lg text-sm font-bold text-white bg-gradient-to-r from-indigo-600 to-blue-600 hover:shadow-lg shadow-md transition-all">
                    ✅ تحويل الآن
                </button>
            </div>
        </form>
    </div>
</div>
@endif

@endif

{{-- Modal: Add Wallet --}}
<div id="addWalletModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden flex flex-col z-10 max-h-[90vh] overflow-y-auto">
        {{-- Header --}}
        <div class="sticky top-0 px-8 py-6 border-b border-slate-100 bg-gradient-to-r from-indigo-600 to-blue-600 flex justify-between items-center">
            <h3 class="text-2xl font-black text-white flex items-center gap-2">
                <span>➕</span> خزينة جديدة
            </h3>
            <button type="button" onclick="document.getElementById('addWalletModal').classList.add('hidden')"
                    class="text-white hover:bg-white/20 rounded-lg p-2 transition-colors">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Form --}}
        <form action="{{ route('udhiya.wallets.store') }}" method="POST" class="p-8 flex flex-col gap-6">
            @csrf

            {{-- Name --}}
            <div class="space-y-2">
                <label class="block text-sm font-bold text-slate-800">
                    📝 اسم الخزينة <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" required placeholder="مثال: فودافون كاش"
                       class="w-full rounded-lg border-2 border-slate-200 bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 py-3 px-4 text-sm font-semibold text-slate-800 transition-colors">
            </div>

            {{-- Type --}}
            <div class="space-y-2">
                <label class="block text-sm font-bold text-slate-800">
                    🏷️ النوع <span class="text-red-500">*</span>
                </label>
                <select name="type" required class="w-full rounded-lg border-2 border-slate-200 bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 py-3 px-4 text-sm font-semibold text-slate-800 transition-colors">
                    <option value="">— اختر النوع —</option>
                    <option value="cash">💵 نقدي</option>
                    <option value="mobile">📲 محفظة رقمية</option>
                    <option value="bank">🏦 بنك</option>
                </select>
            </div>

            {{-- Notes --}}
            <div class="space-y-2">
                <label class="block text-sm font-bold text-slate-800">
                    📋 ملاحظات
                </label>
                <textarea name="notes" rows="3" placeholder="أضف ملاحظات إضافية (اختياري)..."
                          class="w-full rounded-lg border-2 border-slate-200 bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 py-3 px-4 text-sm font-semibold text-slate-800 resize-none transition-colors"></textarea>
            </div>

            {{-- Buttons --}}
            <div class="flex justify-end gap-3 pt-6 border-t-2 border-slate-100">
                <button type="button" onclick="document.getElementById('addWalletModal').classList.add('hidden')"
                        class="px-6 py-2.5 rounded-lg text-sm font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors">
                    إلغاء
                </button>
                <button type="submit" class="px-8 py-2.5 rounded-lg text-sm font-bold text-white bg-gradient-to-r from-indigo-600 to-blue-600 hover:shadow-lg shadow-md transition-all">
                    ➕ إنشاء الخزينة
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Edit Wallet --}}
<div id="editWalletModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden flex flex-col z-10 max-h-[90vh] overflow-y-auto">
        {{-- Header --}}
        <div class="sticky top-0 px-8 py-6 border-b border-slate-100 bg-gradient-to-r from-indigo-600 to-blue-600 flex justify-between items-center">
            <h3 class="text-2xl font-black text-white flex items-center gap-2">
                <span>✏️</span> تعديل الخزينة
            </h3>
            <button type="button" onclick="document.getElementById('editWalletModal').classList.add('hidden')"
                    class="text-white hover:bg-white/20 rounded-lg p-2 transition-colors">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Form --}}
        <form id="editWalletForm" method="POST" class="p-8 flex flex-col gap-6">
            @csrf @method('PATCH')

            {{-- Name --}}
            <div class="space-y-2">
                <label class="block text-sm font-bold text-slate-800">
                    📝 اسم الخزينة <span class="text-red-500">*</span>
                </label>
                <input type="text" id="editName" name="name" required
                       class="w-full rounded-lg border-2 border-slate-200 bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 py-3 px-4 text-sm font-semibold text-slate-800 transition-colors">
            </div>

            {{-- Type --}}
            <div class="space-y-2">
                <label class="block text-sm font-bold text-slate-800">
                    🏷️ النوع <span class="text-red-500">*</span>
                </label>
                <select id="editType" name="type" required class="w-full rounded-lg border-2 border-slate-200 bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 py-3 px-4 text-sm font-semibold text-slate-800 transition-colors">
                    <option value="cash">💵 نقدي</option>
                    <option value="mobile">📲 محفظة رقمية</option>
                    <option value="bank">🏦 بنك</option>
                </select>
            </div>

            {{-- Notes --}}
            <div class="space-y-2">
                <label class="block text-sm font-bold text-slate-800">
                    📋 ملاحظات
                </label>
                <textarea id="editNotes" name="notes" rows="3"
                          class="w-full rounded-lg border-2 border-slate-200 bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 py-3 px-4 text-sm font-semibold text-slate-800 resize-none transition-colors"></textarea>
            </div>

            {{-- Status Toggle --}}
            <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-lg border-2 border-slate-200">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" id="editActive" name="is_active" value="1" class="h-5 w-5 rounded border-slate-300 cursor-pointer">
                <label for="editActive" class="text-sm font-bold text-slate-800 cursor-pointer flex-1">
                    🟢 تفعيل الخزينة
                </label>
            </div>

            {{-- Buttons --}}
            <div class="flex justify-end gap-3 pt-6 border-t-2 border-slate-100">
                <button type="button" onclick="document.getElementById('editWalletModal').classList.add('hidden')"
                        class="px-6 py-2.5 rounded-lg text-sm font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors">
                    إلغاء
                </button>
                <button type="submit" class="px-8 py-2.5 rounded-lg text-sm font-bold text-white bg-gradient-to-r from-indigo-600 to-blue-600 hover:shadow-lg shadow-md transition-all">
                    ✅ حفظ التغييرات
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
@push('js')
<script>
function editWallet(wallet) {
    document.getElementById('editName').value = wallet.name;
    document.getElementById('editType').value = wallet.type;
    document.getElementById('editNotes').value = wallet.notes || '';
    document.getElementById('editActive').checked = wallet.is_active;
    document.getElementById('editWalletForm').action = `/udhiya/wallets/${wallet.id}`;
    document.getElementById('editWalletModal').classList.remove('hidden');
}
</script>
@endpush
