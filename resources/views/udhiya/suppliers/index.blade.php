@extends('layouts.master')

@section('page-header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 mb-8 mt-2">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
            <span class="text-indigo-600 text-4xl">🏭</span> الموردون وشركاء العمل
        </h1>
        <p class="text-slate-500 font-medium text-sm mt-1">إدارة قاعدة الموردين والمزارع المحصل منها المواشي والمتابعة المالية</p>
    </div>
    <div class="flex h-full items-center">
        <button type="button" @click="$dispatch('open-add-supplier-modal')" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-bold rounded-xl transition-all bg-indigo-600 text-white hover:bg-indigo-700 shadow-md shadow-indigo-200 transform hover:-translate-y-0.5">
            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            إضافة مورد جديد
        </button>
    </div>
</div>
@endsection

@section('content')

<div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden mb-12 flex flex-col h-full hover:shadow-md transition-shadow duration-300 relative z-10">
    <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <h3 class="text-lg font-black text-slate-800 flex items-center gap-2">قائمة الموردين</h3>
        <span class="inline-flex items-center gap-1.5 rounded-xl px-3 py-1.5 text-xs font-bold bg-white text-indigo-700 shadow-sm border border-slate-200">
            <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
            مقيد بالنظام {{ $suppliers->total() }} مورد
        </span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-right border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-sm">
                    <th class="px-6 py-4 font-bold"># م</th>
                    <th class="px-6 py-4 font-bold">اسم الجهة / المورد</th>
                    <th class="px-6 py-4 font-bold">الهاتف</th>
                    <th class="px-6 py-4 font-bold">العنوان</th>
                    <th class="px-6 py-4 font-bold">الرصيد الدائن / المدين</th>
                    <th class="px-6 py-4 font-bold">المشتريات السابقة</th>
                    <th class="px-6 py-4 font-bold text-center">إجراءات التحكم</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm font-medium text-slate-700">
                @forelse($suppliers as $supplier)
                <tr class="hover:bg-slate-50/50 transition-colors group">
                    <td class="px-6 py-4 whitespace-nowrap text-slate-400 font-bold">{{ $loop->iteration }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-slate-100/80 text-slate-500 flex items-center justify-center font-bold text-lg border border-slate-200">{{ mb_substr($supplier->name, 0, 1) }}</div>
                            <strong class="text-slate-800 text-base">{{ $supplier->name }}</strong>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-slate-500">{{ $supplier->phone ?? '—' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-slate-500 max-w-[200px] truncate" title="{{ $supplier->address }}">{{ $supplier->address ?? '—' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($supplier->balance > 0)
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-bold bg-rose-50 text-rose-700 border border-rose-100">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                                {{ number_format($supplier->balance, 2) }} ج.م
                                <span class="font-normal">(له)</span>
                            </span>
                        @elseif($supplier->balance < 0)
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                                {{ number_format(abs($supplier->balance), 2) }} ج.م
                                <span class="font-normal">(عليه)</span>
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">متزن تماماً (0)</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-slate-100 text-slate-700 font-bold shadow-sm border border-slate-200">{{ $supplier->purchases_count }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-left">
                        <div class="flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button type="button" @click="$dispatch('open-edit-supplier-modal', { id: '{{ $supplier->id }}', name: '{{ addslashes($supplier->name) }}', phone: '{{ addslashes($supplier->phone ?? '') }}', address: '{{ addslashes($supplier->address ?? '') }}', notes: '{{ addslashes(str_replace(array("\r", "\n"), ' ', $supplier->notes)) }}' })"
                                    class="w-8 h-8 rounded-lg bg-orange-50 text-orange-600 hover:bg-orange-600 hover:text-white flex items-center justify-center transition-colors" title="تعديل بيانات المورد">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </button>
                            <a href="{{ route('udhiya.reports.supplier', $supplier) }}" class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 hover:bg-sky-600 hover:text-white flex items-center justify-center transition-colors" title="كشف حساب المعاملات">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </a>
                            <form action="{{ route('udhiya.suppliers.destroy', $supplier) }}" method="POST" class="inline" onsubmit="return confirm('هل تريد حذف هذا المورد نهائياً؟ إجراء لا يمكن التراجع عنه.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white flex items-center justify-center transition-colors" title="استبعاد المورد">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="flex flex-col items-center justify-center py-16 text-slate-400">
                            <div class="text-7xl mb-4">🏭</div>
                            <h3 class="text-xl font-bold text-slate-700 mb-2">لا يوجد أي موردين بعد</h3>
                            <p class="text-sm font-medium">ابدأ الآن بإضافة الموردين والمزارع لبناء قاعدة معلوماتك.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($suppliers->hasPages())
    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">{{ $suppliers->links() }}</div>
    @endif
</div>

{{-- ===================== ADD SUPPLIER MODAL ===================== --}}
<div x-data="{ open: false }" 
     @open-add-supplier-modal.window="open = true" 
     @close-modals.window="open = false"
     class="relative z-50">
    <div x-show="open" x-transition.opacity class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40"></div>
    <div x-show="open" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6" @click.self="open = false">
        <form action="{{ route('udhiya.suppliers.store') }}" method="POST" class="bg-white rounded-3xl shadow-2xl w-full max-w-xl overflow-hidden flex flex-col">
            @csrf
            
            <div class="px-8 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h3 class="text-xl font-black text-slate-800 flex items-center gap-2">
                    <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                    تسجيل مورد جديد
                </h3>
                <button type="button" @click="open = false" class="text-slate-400 hover:text-rose-500 transition-colors bg-white hover:bg-rose-50 rounded-xl p-2 focus:outline-none">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            
            <div class="p-8 pb-4">
                <div class="mb-6">
                    <label class="block text-sm font-bold text-slate-700 mb-2">اسم المورد / الشركة <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors py-3 px-4 text-sm font-bold text-slate-800 shadow-inner" placeholder="مزرعة السالم...">
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-bold text-slate-700 mb-2">رقم الهاتف</label>
                    <input type="text" name="phone" dir="ltr" class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors py-3 px-4 text-sm font-bold text-slate-800 shadow-inner text-right" placeholder="01X XXXX XXXX">
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-bold text-slate-700 mb-2">العنوان بالتفصيل</label>
                    <input type="text" name="address" class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors py-3 px-4 text-sm font-bold text-slate-800 shadow-inner" placeholder="المحافظة - المركز...">
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-bold text-slate-700 mb-2">تعليقات وملاحظات تهمك</label>
                    <textarea name="notes" rows="3" class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors py-3 px-4 text-sm font-bold text-slate-800 shadow-inner resize-none"></textarea>
                </div>
            </div>
            
            <div class="px-8 py-5 border-t border-slate-100 bg-slate-50 flex justify-end gap-3 flex-shrink-0">
                <button type="button" @click="open = false" class="px-6 py-2.5 rounded-xl text-sm font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 hover:text-slate-800 transition-colors">تراجع</button>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-md shadow-indigo-200 transition-all transform hover:-translate-y-0.5">
                    إدراج المورد بالدفتر
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ===================== EDIT SUPPLIER MODAL ===================== --}}
<div x-data="{ open: false, sId: '', sName: '', sPhone: '', sAddress: '', sNotes: '',
               initEdit(e) {
                   this.sId = e.detail.id;
                   this.sName = e.detail.name;
                   this.sPhone = e.detail.phone;
                   this.sAddress = e.detail.address;
                   this.sNotes = e.detail.notes;
                   this.open = true;
               }
             }" 
     @open-edit-supplier-modal.window="initEdit($event)"
     @close-modals.window="open = false"
     class="relative z-50">
    <div x-show="open" x-transition.opacity class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40"></div>
    <div x-show="open" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6" @click.self="open = false">
        <form :action="'{{ url('udhiya/suppliers') }}/' + sId" method="POST" class="bg-white rounded-3xl shadow-2xl w-full max-w-xl overflow-hidden flex flex-col">
            @csrf @method('PUT')
            
            <div class="px-8 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h3 class="text-xl font-black text-slate-800 flex items-center gap-2">
                    <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    تحديث بيانات المورد
                </h3>
                <button type="button" @click="open = false" class="text-slate-400 hover:text-rose-500 transition-colors bg-white hover:bg-rose-50 rounded-xl p-2 focus:outline-none">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            
            <div class="p-8 pb-4">
                <div class="mb-6">
                    <label class="block text-sm font-bold text-slate-700 mb-2">الاسم <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" x-model="sName" required class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors py-3 px-4 text-sm font-bold text-slate-800 shadow-inner">
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-bold text-slate-700 mb-2">رقم الهاتف</label>
                    <input type="text" name="phone" x-model="sPhone" dir="ltr" class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors py-3 px-4 text-sm font-bold text-slate-800 shadow-inner text-right">
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-bold text-slate-700 mb-2">العنوان</label>
                    <input type="text" name="address" x-model="sAddress" class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors py-3 px-4 text-sm font-bold text-slate-800 shadow-inner">
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-bold text-slate-700 mb-2">ملاحظات</label>
                    <textarea name="notes" x-model="sNotes" rows="3" class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors py-3 px-4 text-sm font-bold text-slate-800 shadow-inner resize-none"></textarea>
                </div>
            </div>
            
            <div class="px-8 py-5 border-t border-slate-100 bg-slate-50 flex justify-end gap-3 flex-shrink-0">
                <button type="button" @click="open = false" class="px-6 py-2.5 rounded-xl text-sm font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 hover:text-slate-800 transition-colors">إلغاء الأمر</button>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-orange-500 hover:bg-orange-600 shadow-md shadow-orange-200 transition-all transform hover:-translate-y-0.5">
                    تعديل البيانات وحفظها
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
        setTimeout(() => {
            window.dispatchEvent(new CustomEvent('open-add-supplier-modal'));
        }, 100);
    });
</script>
@endif
@endpush
