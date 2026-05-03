@extends('layouts.master')
@section('title', 'تعديل مجموعة ذبح')

@section('page-header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 mb-8 mt-2">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
            <span class="text-indigo-600 text-4xl">✏️</span> تعديل مجموعة ذبح
        </h1>
        <p class="text-slate-500 font-medium text-sm mt-1">
            <a href="{{ route('udhiya.groups.index') }}" class="text-indigo-500 hover:text-indigo-700 hover:underline">المجموعات</a> /
            <a href="{{ route('udhiya.groups.show', $group) }}" class="text-indigo-500 hover:text-indigo-700 hover:underline">{{ $group->name }}</a> /
            تعديل
        </p>
    </div>
</div>
@endsection

@section('content')
<form action="{{ route('udhiya.groups.update', $group) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="space-y-8 pb-16">

        {{-- ===== SECTION 1: Main Fields ===== --}}
        <div>
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-md transition-shadow duration-300">
                <div class="px-8 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-sm">1</div>
                    <h6 class="text-lg font-black text-slate-800 m-0">بيانات المجموعة</h6>
                </div>

                <div class="p-8 flex flex-col gap-6">

                    {{-- Name --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">
                            اسم المجموعة <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="name"
                               value="{{ old('name', $group->name) }}"
                               placeholder="مثال: مجموعة عجل 1"
                               required
                               class="w-full rounded-xl border @error('name') border-rose-400 bg-rose-50 @else border-slate-200 bg-slate-50 @enderror focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors py-3 px-4 text-sm font-semibold text-slate-800 shadow-inner">
                        @error('name')
                            <p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Share Type (Read-Only) --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">
                            نوع التقسيم
                            <span class="text-slate-400 font-normal text-xs">(لا يمكن تغييره بعد الإنشاء)</span>
                        </label>
                        <div class="w-full rounded-xl border border-slate-300 bg-slate-100 py-3 px-4 text-sm font-semibold text-slate-700 shadow-inner">
                            {{ $group->shareLabel() }}
                        </div>
                    </div>

                    {{-- Animal --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">
                            الحيوان
                            <span class="text-slate-400 font-normal text-xs">(اختياري)</span>
                            @if($group->isSlaughtered())
                                <span class="block text-amber-600 text-xs font-bold mt-1">⚠️ لا يمكن تغيير الحيوان بعد الذبح</span>
                            @endif
                        </label>
                        <select name="animal_id" id="animalSelect"
                                @if($group->isSlaughtered()) disabled @endif
                                class="w-full @error('animal_id') border-rose-400 @enderror">
                            <option value="">— بدون حيوان —</option>
                            @foreach($animals as $a)
                            @php
                                $aCat  = $a->product?->mainCategory?->name ?? '';
                                $aType = $a->product?->name ?? '';
                                $aEm   = match($a->product?->mainCategory?->code ?? '') {
                                    'BQR' => '🐄', 'GHN' => '🐑', 'JDN' => '🐐', 'JML' => '🐪', default => '🐾'
                                };
                            @endphp
                            <option value="{{ $a->id }}" {{ $group->animal_id == $a->id ? 'selected' : '' }}>
                                {{ $aEm }} {{ $a->code }}{{ $aCat ? ' — ' . $aCat : '' }}{{ $aType ? ' / ' . $aType : '' }}{{ $a->status === 'slaughtered' ? ' (مذبوح)' : '' }}
                            </option>
                            @endforeach
                        </select>
                        @error('animal_id')
                            <p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Animal Type Label --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">
                            نوع الذبيحة <span class="text-slate-400 font-normal text-xs">(اختياري)</span>
                        </label>
                        <select name="animal_type_label"
                                class="w-full rounded-xl border @error('animal_type_label') border-rose-400 bg-rose-50 @else border-slate-200 bg-slate-50 @enderror focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors py-3 px-4 text-sm font-semibold text-slate-800 shadow-inner">
                            <option value="">— اختر نوع الذبيحة —</option>
                            @if(isset($products))
                                @foreach($products as $product)
                                <option value="{{ $product->name }}" {{ old('animal_type_label', $group->animal_type_label) === $product->name ? 'selected' : '' }}>
                                    {{ $product->mainCategory?->name ?? '' }}{{ $product->mainCategory?->name ? ' / ' : '' }}{{ $product->name }}
                                </option>
                                @endforeach
                            @endif
                        </select>
                        @error('animal_type_label')
                            <p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            </div>
        </div>

        {{-- ===== SECTION 2: Members Management ===== --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-8 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-sm">👥</div>
                    <h6 class="text-lg font-black text-slate-800 m-0">أعضاء المجموعة</h6>
                    <span class="text-sm font-bold bg-blue-100 text-blue-700 px-3 py-1 rounded-full">{{ $group->members->count() }} عضو</span>
                </div>
                <button type="button" onclick="document.getElementById('addMemberEditModal').showModal()"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-bold bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-all">
                    ➕ إضافة عضو
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-right">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="px-6 py-3 text-xs font-bold text-slate-600 uppercase">#</th>
                            <th class="px-6 py-3 text-xs font-bold text-slate-600 uppercase">العميل</th>
                            <th class="px-6 py-3 text-xs font-bold text-slate-600 uppercase">الأنصبة</th>
                            <th class="px-6 py-3 text-xs font-bold text-slate-600 uppercase">الصك</th>
                            <th class="px-6 py-3 text-xs font-bold text-slate-600 uppercase">الحالة</th>
                            <th class="px-6 py-3 text-xs font-bold text-slate-600 uppercase">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($group->members as $i => $member)
                        @php
                            $contract = $member->contractItem?->contract;
                            $payment = $contract?->paid_amount ?? 0;
                            $total = $contract?->total_amount ?? 0;
                            $isPaid = $total > 0 && $payment >= $total;
                        @endphp
                        <tr class="hover:bg-slate-50/40">
                            <td class="px-6 py-4 font-bold text-slate-400">{{ $i + 1 }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center font-black text-xs">
                                        {{ mb_substr($member->customer?->name ?? '?', 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800">{{ $member->customer?->name ?? '—' }}</p>
                                        @if($member->customer?->phone)
                                            <p class="text-xs text-slate-400">{{ $member->customer->phone }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold {{ $member->contractItem ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700' }}">
                                    {{ $member->shares_count }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($member->contractItem)
                                    <span class="text-xs font-bold text-indigo-600">📄 {{ $member->contractItem->contract?->contract_number }}</span>
                                @else
                                    <span class="text-xs text-slate-400">بدون صك</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @if($isPaid)
                                    <span class="inline-block bg-emerald-100 text-emerald-700 px-2.5 py-1 rounded-full text-xs font-bold">✅ مدفوع</span>
                                @elseif($member->contractItem)
                                    <span class="inline-block bg-orange-100 text-orange-700 px-2.5 py-1 rounded-full text-xs font-bold">⏳ متبقي</span>
                                @else
                                    <span class="inline-block bg-slate-100 text-slate-600 px-2.5 py-1 rounded-full text-xs font-bold">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 flex gap-1.5">
                                <button type="button" onclick="openEditMemberEditModal({{ $member->id }}, '{{ addslashes($member->customer?->name) }}', {{ $member->shares_count }}, '{{ addslashes($member->notes ?? '') }}')"
                                        class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-500 hover:bg-indigo-500 hover:text-white flex items-center justify-center transition-all text-sm"
                                        title="تعديل{{ $member->contractItem ? ' (سيتم تحديث الصك تلقائياً)' : '' }}">
                                    ✏️
                                </button>
                                @if(!$member->contractItem)
                                    <form action="{{ route('udhiya.groups.members.remove', [$group, $member]) }}" method="POST" onsubmit="return confirm('حذف العضو؟')" style="display: inline;">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="w-7 h-7 rounded-lg bg-rose-50 text-rose-500 hover:bg-rose-500 hover:text-white flex items-center justify-center transition-all text-sm"
                                                title="حذف">
                                            🗑
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <p class="text-4xl mb-3">👥</p>
                                <p class="text-slate-500 font-bold mb-1">لا يوجد أعضاء بعد</p>
                                <p class="text-slate-400 text-sm">ابدأ بإضافة أعضاء للمجموعة</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-8 py-4 bg-slate-50/50 border-t border-slate-100">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <p class="text-xs font-bold text-slate-600 mb-1">عدد الأعضاء</p>
                        <p class="text-lg font-black text-slate-800">{{ $group->members->count() }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-600 mb-1">إجمالي الأنصبة</p>
                        <p class="text-lg font-black text-slate-800">{{ $group->members->sum('shares_count') }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-600 mb-1">متبقي</p>
                        <p class="text-lg font-black text-orange-600">{{ $group->remainingSlots() }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-600 mb-1">السعة الكاملة</p>
                        <p class="text-lg font-black text-indigo-600">{{ $group->totalSlots() }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== SECTION 3: Extra Details + Submit ===== --}}
        <div class="flex flex-col lg:flex-row gap-8">
            <div class="flex-1">
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="px-8 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-sm">2</div>
                    <h6 class="text-lg font-black text-slate-800 m-0">تفاصيل إضافية</h6>
                </div>

                <div class="p-8 flex flex-col gap-6">

                    {{-- Min Price --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">
                            أقل سعر اشتراك <span class="text-slate-400 font-normal text-xs">(للعرض في الموقع)</span>
                        </label>
                        <div class="relative">
                            <input type="number" name="min_price" step="0.01" min="0"
                                   value="{{ old('min_price', $group->min_price) }}"
                                   placeholder="0.00"
                                   class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors py-3 px-4 pl-14 text-sm font-semibold text-slate-800 shadow-inner">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">ج.م</span>
                        </div>
                        @error('min_price')
                            <p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Slaughter Day --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">
                            يوم الذبح
                            <span class="text-slate-400 font-normal text-xs">(اختياري)</span>
                            @if($group->isSlaughtered())
                                <span class="block text-amber-600 text-xs font-bold mt-1">⚠️ لا يمكن تغيير التاريخ بعد الذبح</span>
                            @endif
                        </label>
                        <input type="date" name="slaughter_day"
                               value="{{ old('slaughter_day', $group->slaughter_day?->format('Y-m-d')) }}"
                               @if($group->isSlaughtered()) disabled @endif
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors py-3 px-4 text-sm font-semibold text-slate-800 shadow-inner @if($group->isSlaughtered()) opacity-60 @endif">
                        @error('slaughter_day')
                            <p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">ملاحظات</label>
                        <textarea name="notes" rows="3"
                                  placeholder="أي ملاحظات إضافية..."
                                  class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors py-3 px-4 text-sm font-semibold text-slate-800 shadow-inner resize-none">{{ old('notes', $group->notes) }}</textarea>
                    </div>

                </div>

                <div class="px-8 py-6 border-t border-slate-100 bg-slate-50/80 flex flex-col gap-3">
                    <button type="submit"
                            class="w-full inline-flex justify-center items-center px-6 py-3.5 text-base font-black rounded-xl transition-all bg-emerald-600 text-white hover:bg-emerald-700 shadow-lg shadow-emerald-200 transform hover:-translate-y-0.5">
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M5 13l4 4L19 7"/>
                        </svg>
                        💾 حفظ التعديلات
                    </button>
                    <a href="{{ route('udhiya.groups.show', $group) }}"
                       class="w-full inline-flex justify-center items-center px-6 py-3 text-sm font-bold rounded-xl transition-all bg-white text-slate-600 hover:bg-slate-50 hover:text-slate-900 border border-slate-200 shadow-sm">
                        إلغاء والعودة
                    </a>
                </div>
                </div>
            </div>

            {{-- Sidebar Submit --}}
            <div class="w-full lg:w-80">
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden sticky top-24">
                    <div class="px-8 py-5 border-b border-slate-100 bg-emerald-50/50 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-sm">✓</div>
                        <h6 class="text-lg font-black text-slate-800 m-0">حفظ التغييرات</h6>
                    </div>

                    <div class="p-8 flex flex-col gap-4">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <p class="text-xs font-bold text-blue-700 mb-1">⚠️ ملاحظة مهمة</p>
                            <p class="text-xs text-blue-600">يمكنك تعديل بيانات المجموعة طالما لم يتم الذبح بعد</p>
                        </div>

                        <button type="submit"
                                class="w-full inline-flex justify-center items-center px-6 py-3.5 text-base font-black rounded-xl transition-all bg-emerald-600 text-white hover:bg-emerald-700 shadow-lg shadow-emerald-200 transform hover:-translate-y-0.5">
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            💾 حفظ التعديلات
                        </button>
                        <a href="{{ route('udhiya.groups.show', $group) }}"
                           class="w-full inline-flex justify-center items-center px-6 py-3 text-sm font-bold rounded-xl transition-all bg-white text-slate-600 hover:bg-slate-50 hover:text-slate-900 border border-slate-200 shadow-sm">
                            إلغاء والعودة
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</form>

{{-- ===== ADD MEMBER MODAL (Edit Page) ===== --}}
<dialog id="addMemberEditModal" class="rounded-2xl shadow-2xl max-w-2xl w-full">
    <div class="bg-gradient-to-r from-emerald-50 to-teal-50 px-8 py-6 border-b border-slate-200">
        <h3 class="text-xl font-black text-slate-800 m-0">➕ إضافة عضو للمجموعة</h3>
        <p class="text-sm text-slate-600 mt-1">{{ $group->remainingSlots() }} أنصبة متاحة</p>
    </div>

    <form action="{{ route('udhiya.groups.members.add', $group) }}" method="POST" class="p-8 space-y-6">
        @csrf

        {{-- Tabs: Select Existing or Create New --}}
        <div class="flex gap-2 border-b border-slate-200">
            <button type="button" onclick="showTabEdit('existing')" class="px-4 py-2 font-bold text-sm border-b-2 border-emerald-600 text-emerald-700">
                👤 عميل موجود
            </button>
            <button type="button" onclick="showTabEdit('new')" class="px-4 py-2 font-bold text-sm text-slate-600 hover:text-slate-800 border-b-2 border-transparent">
                ➕ عميل جديد
            </button>
        </div>

        {{-- Tab 1: Existing Customer --}}
        <div id="tab-existing-edit" class="space-y-4">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">العميل</label>
                <select name="customer_id" class="w-full rounded-lg border border-slate-300 bg-white text-slate-800 p-2.5 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                    <option value="">-- اختر عميلاً --</option>
                    @foreach(\App\Models\Customer::orderBy('name')->get() as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }}
                            @if($customer->phone)
                                ({{ $customer->phone }})
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Tab 2: New Customer --}}
        <div id="tab-new-edit" class="space-y-4" style="display: none;">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">اسم العميل *</label>
                <input type="text" name="new_customer_name" class="w-full rounded-lg border border-slate-300 p-2.5 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100" placeholder="أدخل اسم العميل">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">رقم الهاتف</label>
                <input type="text" name="new_customer_phone" class="w-full rounded-lg border border-slate-300 p-2.5 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100" placeholder="رقم الهاتف (اختياري)">
            </div>
        </div>

        {{-- Shares Count --}}
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">عدد الأنصبة</label>
            <input type="number" name="shares_count" min="1" max="{{ $group->remainingSlots() }}" required class="w-full rounded-lg border border-slate-300 p-2.5 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100" placeholder="1">
            <p class="text-xs text-slate-500 mt-1">الحد الأقصى: {{ $group->remainingSlots() }} أنصبة</p>
        </div>

        {{-- Contract Number (Optional) --}}
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">رقم الصك (اختياري)</label>
            <input type="text" name="contract_number" class="w-full rounded-lg border border-slate-300 p-2.5 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100" placeholder="إذا تُركت فارغة، سيتم إنشاء الصك لاحقاً">
        </div>

        {{-- Notes --}}
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">ملاحظات</label>
            <textarea name="notes" rows="2" class="w-full rounded-lg border border-slate-300 p-2.5 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100" placeholder="ملاحظات إضافية (اختيارية)"></textarea>
        </div>

        {{-- Actions --}}
        <div class="flex gap-3 pt-4 border-t border-slate-200">
            <button type="submit" class="flex-1 px-4 py-2.5 bg-emerald-600 text-white font-bold rounded-lg hover:bg-emerald-700 transition-all">
                ✓ إضافة العضو
            </button>
            <button type="button" onclick="document.getElementById('addMemberEditModal').close()" class="flex-1 px-4 py-2.5 bg-slate-100 text-slate-700 font-bold rounded-lg hover:bg-slate-200 transition-all">
                إلغاء
            </button>
        </div>
    </form>
</dialog>

{{-- ===== EDIT MEMBER MODAL (Edit Page) ===== --}}
<dialog id="editMemberEditModal" class="rounded-2xl shadow-2xl max-w-2xl w-full">
    <div class="bg-gradient-to-r from-indigo-50 to-blue-50 px-8 py-6 border-b border-slate-200">
        <h3 class="text-xl font-black text-slate-800 m-0">✏️ تعديل بيانات العضو</h3>
        <p id="memberNameEditDisplay" class="text-sm text-slate-600 mt-1"></p>
    </div>

    <form id="editMemberEditForm" method="POST" class="p-8 space-y-6">
        @csrf @method('PATCH')

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">عدد الأنصبة</label>
            <input type="number" name="shares_count" id="editSharesCountEdit" min="1" required
                   class="w-full rounded-lg border border-slate-300 p-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
            <p id="sharesHintEdit" class="text-xs text-slate-500 mt-1">الحد الأقصى: <span id="maxSharesCountEdit"></span> أنصبة</p>
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">ملاحظات</label>
            <textarea name="notes" id="editNotesEdit" rows="2" class="w-full rounded-lg border border-slate-300 p-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"></textarea>
        </div>

        <div class="flex gap-3 pt-4 border-t border-slate-200">
            <button type="submit" class="flex-1 px-4 py-2.5 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 transition-all">
                ✓ حفظ التعديلات
            </button>
            <button type="button" onclick="document.getElementById('editMemberEditModal').close()" class="flex-1 px-4 py-2.5 bg-slate-100 text-slate-700 font-bold rounded-lg hover:bg-slate-200 transition-all">
                إلغاء
            </button>
        </div>
    </form>
</dialog>

@endsection

@push('js')
<script>
$(function () {
    @if(!$group->isSlaughtered())
    $('#animalSelect').select2({
        dir: 'rtl',
        placeholder: 'ابحث بالكود أو الفئة أو النوع...',
        allowClear: true,
        width: '100%',
        language: { noResults: function() { return 'لا توجد نتائج'; } },
    });
    @endif
});

function showTabEdit(tab) {
    document.getElementById('tab-existing-edit').style.display = tab === 'existing' ? 'block' : 'none';
    document.getElementById('tab-new-edit').style.display = tab === 'new' ? 'block' : 'none';
    document.querySelectorAll('[onclick*="showTabEdit"]').forEach(btn => {
        btn.classList.remove('border-b-2', 'border-emerald-600', 'text-emerald-700');
        btn.classList.add('border-b-2', 'border-transparent', 'text-slate-600', 'hover:text-slate-800');
    });
    event.target.classList.add('border-b-2', 'border-emerald-600', 'text-emerald-700');
    event.target.classList.remove('border-b-2', 'border-transparent', 'text-slate-600', 'hover:text-slate-800');
}

function openEditMemberEditModal(memberId, memberName, sharesCount, notes) {
    document.getElementById('memberNameEditDisplay').textContent = memberName;
    document.getElementById('editSharesCountEdit').value = sharesCount;
    document.getElementById('editNotesEdit').value = notes;

    // Calculate max shares: total slots + current member's shares (since we're replacing theirs)
    // totalSlots = {{ $group->totalSlots() }}, usedSlots = {{ $group->usedSlots() }}, currentShares = sharesCount
    const totalSlots = {{ $group->totalSlots() }};
    const usedSlots = {{ $group->usedSlots() }};
    const maxShares = totalSlots - (usedSlots - sharesCount);

    document.getElementById('editSharesCountEdit').max = maxShares;
    document.getElementById('maxSharesCountEdit').textContent = maxShares;

    const form = document.getElementById('editMemberEditForm');
    form.action = `{{ route('udhiya.groups.members.update', [$group, ':memberId']) }}`.replace(':memberId', memberId);

    document.getElementById('editMemberEditModal').showModal();
}
</script>
@endpush
