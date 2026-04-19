@extends('layouts.master')
@section('page-header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 mb-8 mt-2">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
            <span class="text-indigo-600 text-4xl">💰</span> {{ $advance->advance_number }}
        </h1>
        <p class="text-slate-500 font-medium text-sm mt-1">
            <a href="{{ route('udhiya.advances.index') }}" class="text-indigo-500 hover:text-indigo-700 hover:underline">السلف</a>
            / {{ $advance->advance_number }}
        </p>
    </div>
    @if($advance->status === 'active')
    <form action="{{ route('udhiya.advances.destroy', $advance) }}" method="POST" onsubmit="return confirm('هل تريد إلغاء هذه السلفة؟')">
        @csrf @method('DELETE')
        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-500 hover:text-white border border-rose-100 transition-all">
            🚫 إلغاء السلفة
        </button>
    </form>
    @endif
</div>
@endsection

@section('content')
<div class="flex flex-col lg:flex-row gap-6 pb-16">
    {{-- Main Content --}}
    <div class="flex-1 min-w-0 space-y-6">
        {{-- Details Card --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                <h6 class="text-lg font-black text-slate-800 m-0">تفاصيل السلفة</h6>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <p class="text-xs font-bold text-slate-600 mb-1">النوع</p>
                        <p class="text-sm font-black text-slate-800">{{ $advance->getTypeLabel() }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-600 mb-1">الاسم</p>
                        <p class="text-sm font-black text-slate-800">{{ $advance->getName() }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-600 mb-1">المبلغ الأصلي</p>
                        <p class="text-sm font-black text-indigo-600">{{ number_format($advance->amount, 2) }} ج.م</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-600 mb-1">المتبقي</p>
                        <p class="text-sm font-black {{ $advance->remaining > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                            {{ number_format($advance->remaining, 2) }} ج.م
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-600 mb-1">الخزينة</p>
                        <p class="text-sm font-semibold text-slate-700">{{ $advance->wallet?->name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-600 mb-1">التاريخ</p>
                        <p class="text-sm font-semibold text-slate-700">{{ $advance->date->format('d/m/Y') }}</p>
                    </div>
                </div>

                @if($advance->notes)
                <div class="pt-4 border-t border-slate-100">
                    <p class="text-xs font-bold text-slate-600 mb-1">ملاحظات</p>
                    <p class="text-sm text-slate-700">{{ $advance->notes }}</p>
                </div>
                @endif

                {{-- Progress Bar --}}
                <div class="pt-4 border-t border-slate-100">
                    <p class="text-xs font-bold text-slate-600 mb-2">تقدم التسديد</p>
                    <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden">
                        <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 h-3 rounded-full transition-all duration-300"
                             style="width: {{ ($advance->amount - $advance->remaining) / $advance->amount * 100 }}%"></div>
                    </div>
                    <p class="text-xs text-slate-500 mt-1 text-center">
                        {{ number_format($advance->amount - $advance->remaining, 2) }} / {{ number_format($advance->amount, 2) }} ج.م
                    </p>
                </div>
            </div>
        </div>

        {{-- Transactions Table --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <h6 class="text-lg font-black text-slate-800 m-0">العمليات</h6>
                <span class="text-sm font-bold text-slate-400">({{ $advance->transactions->count() }})</span>
            </div>

            @if($advance->transactions->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-right">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs font-bold">
                            <th class="px-6 py-3">النوع</th>
                            <th class="px-6 py-3 text-center">المبلغ</th>
                            <th class="px-6 py-3">الخزينة</th>
                            <th class="px-6 py-3">التاريخ</th>
                            <th class="px-6 py-3">ملاحظات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($advance->transactions as $transaction)
                        <tr class="hover:bg-slate-50/40 transition-colors">
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-black
                                    {{ $transaction->type === 'receipt' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                    {{ $transaction->getTypeLabel() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center font-black
                                {{ $transaction->type === 'receipt' ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ ($transaction->type === 'receipt' ? '+' : '-') . number_format($transaction->amount, 2) }} ج.م
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $transaction->wallet?->name ?? '—' }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $transaction->date->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $transaction->notes ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="px-6 py-12 text-center">
                <div class="text-4xl mb-3">📝</div>
                <p class="text-slate-400 font-semibold">لا توجد عمليات بعد</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="w-full lg:w-80">
        @if($advance->status === 'active')
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden lg:sticky lg:top-24">
            <div class="px-6 py-5 border-b border-slate-100 bg-gradient-to-b from-emerald-50 to-white">
                <h6 class="text-lg font-black text-emerald-900 m-0">تسجيل عملية</h6>
            </div>

            <form action="{{ route('udhiya.advances.transaction', $advance) }}" method="POST" class="p-6 space-y-4">
                @csrf

                {{-- Type --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-2">نوع العملية <span class="text-rose-500">*</span></label>
                    <select name="type" required class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                        <option value="">-- اختر --</option>
                        <option value="receipt">استلام</option>
                        <option value="return">رد</option>
                    </select>
                </div>

                {{-- Amount --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-2">المبلغ <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <input type="number" name="amount" step="0.01" min="0.01" max="{{ $advance->remaining }}" required
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 py-2.5 px-3 pl-12 text-sm font-black text-slate-800 transition-colors">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 text-xs font-bold">ج.م</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">الحد الأقصى: {{ number_format($advance->remaining, 2) }} ج.م</p>
                </div>

                {{-- Wallet --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-2">الخزينة <span class="text-rose-500">*</span></label>
                    <select name="wallet_id" required class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                        <option value="">-- اختر الخزينة --</option>
                        @foreach($wallets as $w)
                        <option value="{{ $w->id }}">{{ $w->getTypeLabel() }} - {{ $w->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Notes --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-2">ملاحظات</label>
                    <textarea name="notes" rows="2" placeholder="ملاحظات..."
                              class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors resize-none"></textarea>
                </div>

                {{-- Submit --}}
                <button type="submit" class="w-full rounded-xl bg-emerald-600 text-white font-black py-2.5 hover:bg-emerald-700 shadow-md shadow-emerald-200/60 transition-all">
                    ✅ تسجيل العملية
                </button>
            </form>
        </div>
        @else
        <div class="bg-gradient-to-br from-slate-50 to-slate-100 rounded-3xl p-6 border border-slate-200 text-center">
            <div class="text-4xl mb-3">✅</div>
            <p class="text-slate-700 font-bold">السلفة {{ $advance->getStatusLabel() }}</p>
            <p class="text-slate-500 text-sm mt-2">لا يمكن إضافة عمليات</p>
        </div>
        @endif
    </div>
</div>
@endsection
