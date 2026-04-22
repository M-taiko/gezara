@extends('layouts.master')

@section('page-header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 mb-8 mt-2">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
            <span class="text-indigo-600 text-4xl">🙋</span> العملاء والمشتركون
        </h1>
        <p class="text-slate-500 font-medium text-sm mt-1">إدارة قاعدة بيانات العملاء وربطهم بالصكوك والمجموعات</p>
    </div>
    <div class="flex h-full items-center">
        <button type="button" @click="$dispatch('open-add-customer-modal')"
                class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-bold rounded-xl transition-all bg-indigo-600 text-white hover:bg-indigo-700 shadow-md shadow-indigo-200 transform hover:-translate-y-0.5">
            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
            </svg>
            تسجيل عميل جديد
        </button>
    </div>
</div>
@endsection

@section('content')

<div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden mb-12 hover:shadow-md transition-shadow duration-300">
    <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <h3 class="text-lg font-black text-slate-800">قائمة العملاء</h3>
        <div class="flex items-center gap-3 flex-wrap">
            {{-- Search --}}
            <form method="GET" action="{{ route('udhiya.customers.index') }}" class="flex items-center gap-2">
                <div class="relative">
                    <input type="text" name="search" value="{{ $search ?? '' }}"
                           placeholder="ابحث بالاسم أو الهاتف..."
                           class="w-56 rounded-xl border border-slate-200 bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2 pr-9 pl-3 text-sm font-semibold text-slate-800 transition-colors">
                    <svg class="w-4 h-4 text-slate-400 absolute top-2.5 right-2.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <button type="submit"
                        class="inline-flex items-center px-3 py-2 text-xs font-bold rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 transition-colors">
                    بحث
                </button>
                @if($search)
                <a href="{{ route('udhiya.customers.index') }}"
                   class="inline-flex items-center px-3 py-2 text-xs font-bold rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 transition-colors">
                    ✕ مسح
                </a>
                @endif
            </form>
            <span class="inline-flex items-center gap-1.5 rounded-xl px-3 py-1.5 text-xs font-bold bg-white text-indigo-700 shadow-sm border border-slate-200">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                {{ $customers->total() }} عميل
            </span>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-right border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs font-bold uppercase tracking-wide">
                    <th class="px-5 py-4">#</th>
                    <th class="px-5 py-4">اسم العميل</th>
                    <th class="px-5 py-4">الهاتف</th>
                    <th class="px-5 py-4 text-center">الصكوك</th>
                    <th class="px-5 py-4">المجموعات</th>
                    <th class="px-5 py-4">العنوان</th>
                    <th class="px-5 py-4 text-center">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                @forelse($customers as $customer)
                <tr class="hover:bg-slate-50/50 transition-colors group">
                    <td class="px-5 py-4 text-slate-400 font-bold text-xs">{{ $loop->iteration }}</td>

                    {{-- Name --}}
                    <td class="px-5 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-black text-base border border-indigo-100 flex-shrink-0">
                                {{ mb_substr($customer->name, 0, 1) }}
                            </div>
                            <div>
                                <strong class="text-slate-800 text-sm block">{{ $customer->name }}</strong>
                                @if($customer->notes)
                                <span class="text-slate-400 text-xs">{{ Str::limit($customer->notes, 30) }}</span>
                                @endif
                            </div>
                        </div>
                    </td>

                    {{-- Phone --}}
                    <td class="px-5 py-4 whitespace-nowrap text-slate-600 font-medium" dir="ltr" style="text-align:right">
                        {{ $customer->phone ?? '—' }}
                    </td>

                    {{-- Contracts count --}}
                    <td class="px-5 py-4 text-center">
                        @if($customer->contracts_count > 0)
                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-emerald-100 text-emerald-700 font-black text-xs border border-emerald-200">
                            {{ $customer->contracts_count }}
                        </span>
                        @else
                        <span class="text-slate-300 font-bold text-xs">—</span>
                        @endif
                    </td>

                    {{-- Groups --}}
                    <td class="px-5 py-4">
                        @if($customer->groupMembers->count())
                        <div class="flex flex-wrap gap-1">
                            @foreach($customer->groupMembers as $member)
                            <a href="{{ route('udhiya.groups.show', $member->group_id) }}"
                               class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-bold bg-purple-50 text-purple-700 border border-purple-100 hover:bg-purple-100 transition-colors whitespace-nowrap">
                                👥 {{ $member->group->name }}
                            </a>
                            @endforeach
                        </div>
                        @else
                        <span class="text-slate-300 text-xs">—</span>
                        @endif
                    </td>

                    {{-- Address --}}
                    <td class="px-5 py-4 text-slate-500 max-w-[160px] truncate text-xs" title="{{ $customer->address }}">
                        {{ $customer->address ?? '—' }}
                    </td>

                    {{-- Actions --}}
                    <td class="px-5 py-4 whitespace-nowrap">
                        <div class="flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            {{-- Edit --}}
                            <button type="button"
                                    @click="$dispatch('open-edit-customer-modal', {
                                        id: '{{ $customer->id }}',
                                        name: '{{ addslashes($customer->name) }}',
                                        phone: '{{ addslashes($customer->phone ?? '') }}',
                                        address: '{{ addslashes($customer->address ?? '') }}',
                                        notes: '{{ addslashes(str_replace(["\r","\n"], ' ', $customer->notes ?? '')) }}'
                                    })"
                                    class="w-8 h-8 rounded-lg bg-orange-50 text-orange-600 hover:bg-orange-600 hover:text-white flex items-center justify-center transition-colors" title="تعديل">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                </svg>
                            </button>
                            {{-- Report --}}
                            <a href="{{ route('udhiya.reports.customer', $customer) }}"
                               class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 hover:bg-sky-600 hover:text-white flex items-center justify-center transition-colors" title="تقرير">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </a>
                            {{-- Print Statement --}}
                            <a href="{{ route('udhiya.customers.statement', $customer) }}"
                               target="_blank"
                               class="w-8 h-8 rounded-lg bg-green-50 text-green-600 hover:bg-green-600 hover:text-white flex items-center justify-center transition-colors" title="طباعة كشف الحساب">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                </svg>
                            </a>
                            {{-- Delete --}}
                            <form action="{{ route('udhiya.customers.destroy', $customer) }}" method="POST" class="inline"
                                  onsubmit="return confirm('هل تريد حذف العميل {{ addslashes($customer->name) }}؟')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white flex items-center justify-center transition-colors" title="حذف">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="flex flex-col items-center justify-center py-16 text-slate-400">
                            <div class="text-6xl mb-4">🙋</div>
                            <h3 class="text-lg font-black text-slate-600 mb-2">لا يوجد عملاء بعد</h3>
                            <p class="text-sm">ابدأ بتسجيل عملائك لربطهم بالصكوك والمجموعات</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($customers->hasPages())
    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">{{ $customers->links() }}</div>
    @endif
</div>

{{-- ADD CUSTOMER MODAL --}}
<div x-data="{ open: false }"
     @open-add-customer-modal.window="open = true"
     @close-modals.window="open = false"
     class="relative z-50">
    <div x-show="open" x-transition.opacity class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40"></div>
    <div x-show="open" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6" @click.self="open = false">
        <form action="{{ route('udhiya.customers.store') }}" method="POST"
              class="bg-white rounded-3xl shadow-2xl w-full max-w-xl overflow-hidden flex flex-col">
            @csrf
            <div class="px-8 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h3 class="text-xl font-black text-slate-800">➕ إضافة عميل جديد</h3>
                <button type="button" @click="open = false" class="text-slate-400 hover:text-rose-500 bg-white hover:bg-rose-50 rounded-xl p-2">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-8 flex flex-col gap-5">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">الاسم <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 py-3 px-4 text-sm font-bold text-slate-800 shadow-inner"
                           placeholder="أحمد محمد علي...">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">رقم الهاتف</label>
                    <input type="text" name="phone" dir="ltr"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 py-3 px-4 text-sm font-bold text-slate-800 shadow-inner text-right"
                           placeholder="01X XXXX XXXX">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">العنوان</label>
                    <input type="text" name="address"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 py-3 px-4 text-sm font-bold text-slate-800 shadow-inner"
                           placeholder="المدينة، الحي...">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">ملاحظات</label>
                    <textarea name="notes" rows="2"
                              class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 py-3 px-4 text-sm font-bold text-slate-800 shadow-inner resize-none"></textarea>
                </div>
            </div>
            <div class="px-8 py-5 border-t border-slate-100 bg-slate-50 flex justify-end gap-3">
                <button type="button" @click="open = false"
                        class="px-6 py-2.5 rounded-xl text-sm font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50">إلغاء</button>
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-md shadow-indigo-200 transition-all transform hover:-translate-y-0.5">
                    إضافة العميل
                </button>
            </div>
        </form>
    </div>
</div>

{{-- EDIT CUSTOMER MODAL --}}
<div x-data="{
        open: false, cId: '', cName: '', cPhone: '', cAddress: '', cNotes: '',
        initEdit(e) {
            this.cId = e.detail.id; this.cName = e.detail.name;
            this.cPhone = e.detail.phone; this.cAddress = e.detail.address;
            this.cNotes = e.detail.notes; this.open = true;
        }
     }"
     @open-edit-customer-modal.window="initEdit($event)"
     @close-modals.window="open = false"
     class="relative z-50">
    <div x-show="open" x-transition.opacity class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40"></div>
    <div x-show="open" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6" @click.self="open = false">
        <form :action="'{{ url('udhiya/customers') }}/' + cId" method="POST"
              class="bg-white rounded-3xl shadow-2xl w-full max-w-xl overflow-hidden flex flex-col">
            @csrf @method('PUT')
            <div class="px-8 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h3 class="text-xl font-black text-slate-800">✏️ تعديل بيانات العميل</h3>
                <button type="button" @click="open = false" class="text-slate-400 hover:text-rose-500 bg-white hover:bg-rose-50 rounded-xl p-2">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-8 flex flex-col gap-5">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">الاسم <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" x-model="cName" required
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 py-3 px-4 text-sm font-bold text-slate-800 shadow-inner">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">رقم الهاتف</label>
                    <input type="text" name="phone" x-model="cPhone" dir="ltr"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 py-3 px-4 text-sm font-bold text-slate-800 shadow-inner text-right">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">العنوان</label>
                    <input type="text" name="address" x-model="cAddress"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 py-3 px-4 text-sm font-bold text-slate-800 shadow-inner">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">ملاحظات</label>
                    <textarea name="notes" x-model="cNotes" rows="2"
                              class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 py-3 px-4 text-sm font-bold text-slate-800 shadow-inner resize-none"></textarea>
                </div>
            </div>
            <div class="px-8 py-5 border-t border-slate-100 bg-slate-50 flex justify-end gap-3">
                <button type="button" @click="open = false"
                        class="px-6 py-2.5 rounded-xl text-sm font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50">إلغاء</button>
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-orange-500 hover:bg-orange-600 shadow-md shadow-orange-200 transition-all transform hover:-translate-y-0.5">
                    حفظ التعديلات
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('js')
@if($errors->any())
<script>
    document.addEventListener('alpine:init', () => {
        setTimeout(() => window.dispatchEvent(new CustomEvent('open-add-customer-modal')), 100);
    });
</script>
@endif
@endpush
