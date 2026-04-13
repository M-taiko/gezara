@extends('layouts.master')

@section('page-header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 mb-8 mt-2">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
            <span class="text-indigo-600 text-4xl">💰</span> الخزائن والمحافظ
        </h1>
        <p class="text-slate-500 font-medium text-sm mt-1">
            <a href="{{ route('udhiya.dashboard') }}" class="text-indigo-500 hover:text-indigo-700 hover:underline">الرئيسية</a> / الخزائن
        </p>
    </div>
    <button type="button" onclick="document.getElementById('addWalletModal').classList.remove('hidden')"
            class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 shadow-md transition-all">
        ➕ خزينة جديدة
    </button>
</div>
@endsection

@section('content')

@if($wallets->isEmpty())
<div class="bg-white rounded-3xl shadow-sm border border-slate-100 flex flex-col items-center justify-center py-20 text-center">
    <div class="text-6xl mb-4">💰</div>
    <h5 class="text-lg font-black text-slate-600 mb-2">لا توجد خزائن بعد</h5>
    <p class="text-slate-400 text-sm mb-6">ابدأ بإضافة خزينة جديدة لتتمكن من تتبع أموالك</p>
    <button type="button" onclick="document.getElementById('addWalletModal').classList.remove('hidden')"
            class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 shadow-md transition-all">
        ➕ أضف خزينة الآن
    </button>
</div>
@else

{{-- Wallets Grid --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    @foreach($wallets as $wallet)
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-md transition-shadow">
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex justify-between items-start">
            <div>
                <h6 class="text-base font-black text-slate-800 m-0">{{ $wallet->getTypeLabel() }} {{ $wallet->name }}</h6>
                <p class="text-xs text-slate-400 mt-1">{{ $wallet->notes ?? '—' }}</p>
            </div>
            <span class="px-2.5 py-1 rounded-lg text-xs font-black {{ $wallet->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700' }}">
                {{ $wallet->is_active ? '✅ نشط' : '🔒 معطل' }}
            </span>
        </div>

        <div class="p-6">
            <div class="text-center mb-6 pb-6 border-b border-slate-100">
                <div class="text-4xl font-black text-indigo-600">{{ number_format($wallet->balance, 2) }}</div>
                <div class="text-xs text-slate-400 font-semibold mt-1">ج.م</div>
            </div>

            <div class="flex gap-2">
                <button type="button" onclick="editWallet({{ $wallet->toJson() }})"
                        class="flex-1 px-3 py-2 text-xs font-bold rounded-xl bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition-colors">
                    ✏️ تعديل
                </button>
                @if($wallet->balance == 0)
                <form action="{{ route('udhiya.wallets.destroy', $wallet) }}" method="POST" class="flex-1"
                      onsubmit="return confirm('هل تريد حذف {{ addslashes($wallet->name) }}؟')">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full px-3 py-2 text-xs font-bold rounded-xl bg-rose-50 text-rose-700 hover:bg-rose-100 transition-colors">
                        🗑️ حذف
                    </button>
                </form>
                @endif
            </div>
        </div>

        {{-- Last Transactions --}}
        @if($wallet->transactions->count())
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 text-xs">
            <div class="font-bold text-slate-600 mb-2">آخر المعاملات:</div>
            @foreach($wallet->transactions->take(3) as $tx)
            <div class="flex justify-between items-center py-1.5 text-slate-600">
                <span>{{ $tx->type === 'in' ? '➕ دخول' : '➖ خروج' }} {{ $tx->amount }} ج.م</span>
                <span class="text-slate-400">{{ $tx->date->format('Y/m/d') }}</span>
            </div>
            @endforeach
        </div>
        @endif
    </div>
    @endforeach
</div>

{{-- Transfer Widget --}}
@if($wallets->count() >= 2)
<div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
        <h6 class="text-base font-black text-slate-800 m-0">🔄 تحويل بين الخزائن</h6>
    </div>
    <div class="p-6">
        <form action="{{ route('udhiya.wallets.transfer') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @csrf
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">من الخزينة <span class="text-rose-500">*</span></label>
                <select name="from_wallet_id" required class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-3 px-4 text-sm font-semibold text-slate-800">
                    <option value="">— اختر الخزينة —</option>
                    @foreach($wallets->where('is_active', true) as $w)
                    <option value="{{ $w->id }}">{{ $w->getTypeLabel() }} {{ $w->name }} ({{ number_format($w->balance, 2) }} ج.م)</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">إلى الخزينة <span class="text-rose-500">*</span></label>
                <select name="to_wallet_id" required class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-3 px-4 text-sm font-semibold text-slate-800">
                    <option value="">— اختر الخزينة —</option>
                    @foreach($wallets->where('is_active', true) as $w)
                    <option value="{{ $w->id }}">{{ $w->getTypeLabel() }} {{ $w->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">المبلغ (ج.م) <span class="text-rose-500">*</span></label>
                <input type="number" name="amount" min="0.01" step="0.01" required placeholder="0.00"
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-3 px-4 text-sm font-bold text-slate-800 text-center" dir="ltr">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">التاريخ <span class="text-rose-500">*</span></label>
                <input type="date" name="date" value="{{ date('Y-m-d') }}" required
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-3 px-4 text-sm font-semibold text-slate-800">
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-slate-700 mb-2">ملاحظات</label>
                <input type="text" name="notes" placeholder="اختياري..."
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-slate-400 py-3 px-4 text-sm font-semibold text-slate-800">
            </div>

            <div class="md:col-span-2 flex justify-end gap-3">
                <button type="button" onclick="document.querySelector('form').reset()"
                        class="px-6 py-2.5 rounded-xl text-sm font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50">مسح</button>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-md transition-all">
                    🔄 تحويل الآن
                </button>
            </div>
        </form>
    </div>
</div>
@endif

@endif

{{-- Modal: Add Wallet --}}
<div id="addWalletModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="this.parentElement.classList.add('hidden')"></div>
    <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden flex flex-col z-10">
        <div class="px-8 py-5 border-b border-slate-100 flex justify-between items-center bg-indigo-50/50">
            <h3 class="text-xl font-black text-slate-800">➕ خزينة جديدة</h3>
            <button type="button" onclick="document.getElementById('addWalletModal').classList.add('hidden')"
                    class="text-slate-400 hover:text-rose-500 bg-white hover:bg-rose-50 rounded-xl p-2">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="{{ route('udhiya.wallets.store') }}" method="POST" class="p-8 flex flex-col gap-5">
            @csrf
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">اسم الخزينة <span class="text-rose-500">*</span></label>
                <input type="text" name="name" required placeholder="مثال: فودافون كاش"
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-3 px-4 text-sm font-bold text-slate-800">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">النوع <span class="text-rose-500">*</span></label>
                <select name="type" required class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-3 px-4 text-sm font-semibold text-slate-800">
                    <option value="">— اختر النوع —</option>
                    <option value="cash">💵 نقدي</option>
                    <option value="mobile">📲 محفظة رقمية</option>
                    <option value="bank">🏦 بنك</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">ملاحظات</label>
                <textarea name="notes" rows="2" placeholder="اختياري..."
                          class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-slate-400 py-3 px-4 text-sm font-semibold text-slate-800 resize-none"></textarea>
            </div>
            <div class="flex justify-end gap-3 pt-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('addWalletModal').classList.add('hidden')"
                        class="px-6 py-2.5 rounded-xl text-sm font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50">إلغاء</button>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-md transition-all">
                    ➕ إضافة الخزينة
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Edit Wallet --}}
<div id="editWalletModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="this.parentElement.classList.add('hidden')"></div>
    <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden flex flex-col z-10">
        <div class="px-8 py-5 border-b border-slate-100 flex justify-between items-center bg-indigo-50/50">
            <h3 class="text-xl font-black text-slate-800">✏️ تعديل الخزينة</h3>
            <button type="button" onclick="document.getElementById('editWalletModal').classList.add('hidden')"
                    class="text-slate-400 hover:text-rose-500 bg-white hover:bg-rose-50 rounded-xl p-2">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="editWalletForm" method="POST" class="p-8 flex flex-col gap-5">
            @csrf @method('PATCH')
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">اسم الخزينة <span class="text-rose-500">*</span></label>
                <input type="text" id="editName" name="name" required
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-3 px-4 text-sm font-bold text-slate-800">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">النوع <span class="text-rose-500">*</span></label>
                <select id="editType" name="type" required class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-3 px-4 text-sm font-semibold text-slate-800">
                    <option value="cash">💵 نقدي</option>
                    <option value="mobile">📲 محفظة رقمية</option>
                    <option value="bank">🏦 بنك</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">ملاحظات</label>
                <textarea id="editNotes" name="notes" rows="2"
                          class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-slate-400 py-3 px-4 text-sm font-semibold text-slate-800 resize-none"></textarea>
            </div>
            <div class="form-check flex items-center gap-2">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" id="editActive" name="is_active" value="1" class="rounded border-slate-300">
                <label for="editActive" class="text-sm font-semibold text-slate-600">نشط</label>
            </div>
            <div class="flex justify-end gap-3 pt-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('editWalletModal').classList.add('hidden')"
                        class="px-6 py-2.5 rounded-xl text-sm font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50">إلغاء</button>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-md transition-all">
                    ✅ حفظ التغييرات
                </button>
            </div>
        </form>
    </div>
</div>

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
