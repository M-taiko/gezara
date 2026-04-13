@extends('layouts.master')

@section('page-header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8 mt-2">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
            <span class="text-rose-500 text-4xl">💸</span> المصروفات
        </h1>
        <p class="text-slate-500 font-medium text-sm mt-1">
            <a href="{{ route('udhiya.dashboard') }}" class="text-indigo-500 hover:text-indigo-700 hover:underline">الرئيسية</a> / المصروفات
        </p>
    </div>
    <a href="{{ route('udhiya.reports.profit') }}"
       class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-100 transition-all">
        📈 تقرير الأرباح
    </a>
</div>
@endsection 

@section('content')
<div class="flex flex-col lg:flex-row gap-6 pb-16">

    {{-- ═══ RIGHT: Add Form + Summary ═══ --}}
    <div class="w-full lg:w-80 flex-shrink-0 flex flex-col gap-5">

        {{-- Add Expense Form --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-rose-100 bg-gradient-to-b from-rose-50 to-white">
                <h6 class="text-base font-black text-rose-900 m-0">➕ تسجيل مصروف جديد</h6>
            </div>
            <form action="{{ route('udhiya.expenses.store') }}" method="POST">
                @csrf
                <div class="px-6 py-5 space-y-4">

                    {{-- Animal (optional) --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">
                            الحيوان
                            <span class="text-slate-400 font-normal">(اختياري — اتركه فارغاً للمصروفات العامة)</span>
                        </label>
                        <select name="animal_id"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-rose-400 focus:ring-2 focus:ring-rose-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                            <option value="">— مصروف عام —</option>
                            @foreach($animals as $animal)
                            <option value="{{ $animal->id }}">
                                🐄 {{ $animal->code }} — {{ $animal->product->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">نوع المصروف <span class="text-rose-500">*</span></label>
                        <select name="category" required
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-rose-400 focus:ring-2 focus:ring-rose-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                            <option value="">— اختر النوع —</option>
                            @foreach(\App\Models\Expense::CATEGORIES as $key => $cat)
                            <option value="{{ $key }}">{{ $cat['emoji'] }} {{ $cat['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">البيان <span class="text-rose-500">*</span></label>
                        <input type="text" name="description" required placeholder="مثال: علف شهر أبريل"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-rose-400 focus:ring-2 focus:ring-rose-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">المبلغ <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <input type="number" name="amount" required min="0.01" step="0.01" placeholder="0.00"
                                   class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-rose-400 focus:ring-2 focus:ring-rose-100 py-2.5 px-3 text-sm font-bold text-slate-800 transition-colors">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">ج.م</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">التاريخ <span class="text-rose-500">*</span></label>
                        <input type="date" name="date" required value="{{ date('Y-m-d') }}"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-rose-400 focus:ring-2 focus:ring-rose-100 py-2.5 px-3 text-sm font-bold text-slate-800 transition-colors">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">ملاحظات</label>
                        <textarea name="notes" rows="2" placeholder="اختياري..."
                                  class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-rose-400 focus:ring-2 focus:ring-rose-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors resize-none"></textarea>
                    </div>

                    <button type="submit"
                            class="w-full inline-flex justify-center items-center gap-2 px-5 py-3 text-sm font-black rounded-xl bg-rose-600 text-white hover:bg-rose-700 shadow-md shadow-rose-200/60 transition-all">
                        💸 تسجيل المصروف
                    </button>
                </div>
            </form>
        </div>

        {{-- Summary by category --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                <h6 class="text-base font-black text-slate-800 m-0">📊 ملخص المصروفات</h6>
            </div>
            <div class="px-6 py-5 space-y-3">
                @foreach(\App\Models\Expense::CATEGORIES as $key => $cat)
                @if($totals[$key] > 0)
                <div class="flex items-center justify-between py-1.5 border-b border-slate-50">
                    <span class="text-sm font-semibold text-slate-600">{{ $cat['emoji'] }} {{ $cat['label'] }}</span>
                    <span class="text-sm font-black text-rose-700">{{ number_format($totals[$key], 0) }} ج.م</span>
                </div>
                @endif
                @endforeach
                <div class="pt-2 flex items-center justify-between">
                    <span class="text-sm font-black text-slate-800">الإجمالي</span>
                    <span class="text-lg font-black text-rose-600">{{ number_format($grandTotal, 0) }} <span class="text-xs text-rose-400">ج.م</span></span>
                </div>
            </div>
        </div>

    </div>

    {{-- ═══ LEFT: Table ═══ --}}
    <div class="flex-1 min-w-0">

        {{-- Filter bar --}}
        <form method="GET" action="{{ route('udhiya.expenses.index') }}" class="flex flex-wrap gap-3 mb-5">
            <select name="animal_id" onchange="this.form.submit()"
                    class="rounded-xl border border-slate-200 bg-white focus:border-indigo-400 py-2.5 px-3 text-sm font-semibold text-slate-800 shadow-sm">
                <option value="">كل الحيوانات</option>
                <option value="general" {{ request('animal_id') === 'general' ? 'selected' : '' }}>مصروفات عامة</option>
                @foreach($animals as $animal)
                <option value="{{ $animal->id }}" {{ request('animal_id') == $animal->id ? 'selected' : '' }}>
                    {{ $animal->code }} — {{ $animal->product->name }}
                </option>
                @endforeach
            </select>
            <select name="category" onchange="this.form.submit()"
                    class="rounded-xl border border-slate-200 bg-white focus:border-indigo-400 py-2.5 px-3 text-sm font-semibold text-slate-800 shadow-sm">
                <option value="">كل الأنواع</option>
                @foreach(\App\Models\Expense::CATEGORIES as $key => $cat)
                <option value="{{ $key }}" {{ request('category') === $key ? 'selected' : '' }}>
                    {{ $cat['emoji'] }} {{ $cat['label'] }}
                </option>
                @endforeach
            </select>
            <input type="date" name="from" value="{{ request('from') }}" onchange="this.form.submit()"
                   class="rounded-xl border border-slate-200 bg-white py-2.5 px-3 text-sm font-semibold text-slate-800 shadow-sm">
            <input type="date" name="to" value="{{ request('to') }}" onchange="this.form.submit()"
                   class="rounded-xl border border-slate-200 bg-white py-2.5 px-3 text-sm font-semibold text-slate-800 shadow-sm">
            @if(request()->hasAny(['animal_id','category','from','to']))
            <a href="{{ route('udhiya.expenses.index') }}"
               class="inline-flex items-center px-4 py-2.5 text-sm font-bold rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 transition-all">
                ✕ مسح
            </a>
            @endif
        </form>

        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <h6 class="text-base font-black text-slate-800 m-0">سجل المصروفات</h6>
                <span class="text-xs font-bold text-slate-400">{{ $expenses->total() }} مصروف</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-right">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs font-bold">
                            <th class="px-5 py-3">التاريخ</th>
                            <th class="px-5 py-3">الحيوان</th>
                            <th class="px-5 py-3">النوع</th>
                            <th class="px-5 py-3">البيان</th>
                            <th class="px-5 py-3 text-left">المبلغ</th>
                            <th class="px-5 py-3 hidden md:table-cell">ملاحظات</th>
                            <th class="px-5 py-3 w-12"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($expenses as $expense)
                        <tr class="hover:bg-rose-50/20 transition-colors">
                            <td class="px-5 py-3 text-sm font-semibold text-slate-600 whitespace-nowrap">
                                {{ $expense->date->format('d/m/Y') }}
                            </td>
                            <td class="px-5 py-3">
                                @if($expense->animal)
                                <a href="{{ route('udhiya.animals.show', $expense->animal) }}"
                                   class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-black bg-indigo-50 text-indigo-700 hover:bg-indigo-100 border border-indigo-100 transition-colors">
                                    🐄 {{ $expense->animal->code }}
                                </a>
                                @else
                                <span class="text-xs text-slate-400 font-semibold">عام</span>
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                @php $cat = \App\Models\Expense::CATEGORIES[$expense->category] ?? ['emoji'=>'📦','label'=>$expense->category]; @endphp
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-black bg-rose-50 text-rose-700 border border-rose-100">
                                    {{ $cat['emoji'] }} {{ $cat['label'] }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-sm font-semibold text-slate-800">
                                {{ $expense->description }}
                            </td>
                            <td class="px-5 py-3 text-left text-base font-black text-rose-600 whitespace-nowrap">
                                {{ number_format($expense->amount, 2) }}
                                <span class="text-xs text-rose-400 font-normal">ج.م</span>
                            </td>
                            <td class="px-5 py-3 text-xs text-slate-400 hidden md:table-cell">
                                {{ $expense->notes ?? '—' }}
                            </td>
                            <td class="px-5 py-3 text-center">
                                <form action="{{ route('udhiya.expenses.destroy', $expense) }}" method="POST"
                                      onsubmit="return confirm('حذف هذا المصروف؟')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="w-8 h-8 rounded-lg bg-rose-50 text-rose-400 hover:bg-rose-500 hover:text-white flex items-center justify-center transition-colors mx-auto text-xs">
                                        🗑
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7">
                                <div class="py-16 text-center">
                                    <div class="text-5xl mb-4">💸</div>
                                    <p class="text-slate-400 font-bold">لا توجد مصروفات مسجّلة</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($expenses->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $expenses->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
