@extends('layouts.master')

@section('page-header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 mb-8 mt-2">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
            <span class="text-amber-600 text-4xl">✏️</span> تعديل الدفعة
        </h1>
        <p class="text-slate-500 font-medium text-sm mt-1">
            <a href="{{ route('udhiya.collections.index') }}" class="text-indigo-500 hover:text-indigo-700 hover:underline">التحصيلات</a> /
            {{ $payment->receipt_number }} / تعديل
        </p>
    </div>
</div>
@endsection

@section('content')

<div class="flex flex-col lg:flex-row gap-6 pb-16">

    {{-- ═══ RIGHT: Form ═══ --}}
    <div class="w-full lg:w-96 flex-shrink-0">

        {{-- Edit Payment Form --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden sticky top-24">
            <div class="px-6 py-5 border-b border-amber-100 bg-gradient-to-b from-amber-50 to-white">
                <h6 class="text-base font-black text-amber-900 m-0">✏️ تعديل الدفعة</h6>
                <p class="text-xs text-amber-600 mt-1">إيصال: <strong>{{ $payment->receipt_number }}</strong></p>
            </div>
            <form action="{{ route('udhiya.collections.update', $payment) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="px-6 py-5 space-y-4">

                    {{-- Customer Info (read-only) --}}
                    <div class="p-3 bg-blue-50 border border-blue-200 rounded-xl">
                        <p class="text-xs text-blue-700"><strong>👤 العميل:</strong> {{ $payment->contract->customer->name }}</p>
                    </div>

                    {{-- Contract (required) --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">
                            الصك <span class="text-rose-500">*</span>
                        </label>
                        <select name="contract_id" required id="contractSelect"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-amber-400 focus:ring-2 focus:ring-amber-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                            @foreach($contracts as $contract)
                            <option value="{{ $contract->id }}" {{ $payment->contract_id == $contract->id ? 'selected' : '' }}
                                    data-total="{{ $contract->total_amount }}"
                                    data-paid="{{ $contract->paid_amount }}"
                                    data-remaining="{{ $contract->remaining_amount }}">
                                {{ $contract->contract_number }} — متبقي {{ number_format($contract->remaining_amount, 0) }} ج.م
                            </option>
                            @endforeach
                        </select>
                        <div id="contractInfo" class="mt-2 p-2.5 rounded-lg bg-blue-50 border border-blue-200">
                            <p class="text-xs text-blue-700"><strong>الإجمالي:</strong> <span id="totalAmount">{{ number_format($payment->contract->total_amount, 0) }}</span> ج.م</p>
                            <p class="text-xs text-blue-700"><strong>المدفوع:</strong> <span id="paidAmount">{{ number_format($payment->contract->paid_amount, 0) }}</span> ج.م</p>
                            <p class="text-xs text-blue-700"><strong>المتبقي:</strong> <span id="remainingAmount">{{ number_format($payment->contract->remaining_amount, 0) }}</span> ج.م</p>
                        </div>
                    </div>

                    {{-- Amount (required) --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">المبلغ <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <input type="number" name="amount" required min="0.01" step="0.01"
                                   placeholder="0.00" id="amountInput" value="{{ $payment->amount }}"
                                   class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-amber-400 focus:ring-2 focus:ring-amber-100 py-2.5 px-3 text-sm font-bold text-slate-800 transition-colors">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">ج.م</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">الحد الأقصى: <span id="maxAmount">{{ number_format($payment->contract->remaining_amount + $payment->amount, 0) }}</span> ج.م</p>
                    </div>

                    {{-- Payment Method (required) --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">طريقة الدفع <span class="text-rose-500">*</span></label>
                        <select name="payment_method" required
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-amber-400 focus:ring-2 focus:ring-amber-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                            <option value="cash" {{ $payment->payment_method == 'cash' ? 'selected' : '' }}>💵 نقدي</option>
                            <option value="bank" {{ $payment->payment_method == 'bank' ? 'selected' : '' }}>🏦 بنك</option>
                            <option value="transfer" {{ $payment->payment_method == 'transfer' ? 'selected' : '' }}>📲 تحويل</option>
                        </select>
                    </div>

                    {{-- Wallet (optional) --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">الخزينة <span class="text-slate-400 font-normal text-xs">(اختياري)</span></label>
                        <select name="wallet_id"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-amber-400 focus:ring-2 focus:ring-amber-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                            <option value="">— بدون خزينة —</option>
                            @foreach($wallets as $wallet)
                            <option value="{{ $wallet->id }}" {{ $payment->wallet_id == $wallet->id ? 'selected' : '' }}>
                                {{ $wallet->getTypeLabel() }} — {{ $wallet->name }} ({{ number_format($wallet->balance, 2) }} ج.م)
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Date (required) --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">التاريخ <span class="text-rose-500">*</span></label>
                        <input type="date" name="date" required value="{{ $payment->date->format('Y-m-d') }}"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-amber-400 focus:ring-2 focus:ring-amber-100 py-2.5 px-3 text-sm font-bold text-slate-800 transition-colors">
                    </div>

                    {{-- Attachments (optional) --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">📎 المرفقات</label>
                        <input type="file" name="attachments[]" multiple
                               accept="image/*,.pdf"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-amber-400 focus:ring-2 focus:ring-amber-100 py-2.5 px-3 text-sm text-slate-800 transition-colors">
                        <p class="text-xs text-slate-500 mt-1">صور الصك أو إيصالات الدفع (JPG, PNG, PDF)</p>

                        @if($payment->attachments && count($payment->attachments) > 0)
                        <div class="mt-3 space-y-2">
                            <p class="text-xs font-bold text-slate-600">المرفقات الحالية:</p>
                            @php $paths = $payment->attachment_paths ? json_decode($payment->attachment_paths, true) : []; @endphp
                            @foreach($payment->attachments as $index => $attachment)
                            <div class="flex items-center justify-between gap-2 p-2 bg-slate-50 rounded-lg border border-slate-200">
                                @if(isset($paths[$index]))
                                <a href="{{ asset('storage/' . $paths[$index]) }}" target="_blank"
                                   class="text-xs text-indigo-600 hover:underline truncate flex-1">
                                    📄 {{ $attachment }}
                                </a>
                                @else
                                <span class="text-xs text-slate-600 truncate flex-1">📄 {{ $attachment }}</span>
                                @endif
                                <label class="flex items-center gap-1 text-xs cursor-pointer">
                                    <input type="checkbox" name="remove_attachments[]" value="{{ $index }}"
                                           class="rounded">
                                    <span class="text-rose-600">حذف</span>
                                </label>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>

                    {{-- Notes (optional) --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">ملاحظات</label>
                        <textarea name="notes" rows="2" placeholder="مثال: دفعة جزئية"
                                  class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-amber-400 focus:ring-2 focus:ring-amber-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors resize-none">{{ $payment->notes }}</textarea>
                    </div>

                    {{-- Receipt Number (editable) --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">رقم الإيصال <span class="text-slate-400 font-normal text-xs">(اختياري)</span></label>
                        <input type="text" name="reference_number" placeholder="RCP-2026-0001"
                               value="{{ $payment->reference_number }}"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-amber-400 focus:ring-2 focus:ring-amber-100 py-2.5 px-3 text-sm font-bold text-slate-800 transition-colors">
                        <p class="text-xs text-slate-500 mt-1">الرقم التسلسلي للإيصال (مثال: RCP-2026-0001)</p>
                    </div>

                    {{-- Original Payment Date --}}
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs text-slate-600">
                        <p><strong>تاريخ الإنشاء:</strong> {{ $payment->created_at->format('d/m/Y H:i') }}</p>
                        <p class="mt-1"><strong>آخر تعديل:</strong> {{ $payment->updated_at->format('d/m/Y H:i') }}</p>
                    </div>

                    <div class="flex gap-3">
                        <button type="submit"
                                class="flex-1 inline-flex justify-center items-center gap-2 px-5 py-3 text-sm font-black rounded-xl bg-amber-600 text-white hover:bg-amber-700 shadow-md shadow-amber-200/60 transition-all">
                            💾 حفظ التعديلات
                        </button>
                        <a href="{{ route('udhiya.collections.index') }}"
                           class="flex-1 inline-flex justify-center items-center px-5 py-3 text-sm font-bold rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 transition-all">
                            إلغاء
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- Delete Card --}}
        <div class="bg-white rounded-3xl shadow-sm border border-rose-100 overflow-hidden mt-6">
            <div class="px-6 py-5 border-b border-rose-100 bg-gradient-to-b from-rose-50 to-white">
                <h6 class="text-base font-black text-rose-900 m-0">🗑️ حذف الدفعة</h6>
            </div>
            <div class="px-6 py-5">
                <p class="text-sm text-rose-700 mb-4">
                    ⚠️ حذف هذه الدفعة سيؤثر على الرصيد المتبقي من الصك.
                </p>
                <form action="{{ route('udhiya.collections.destroy', $payment) }}" method="POST"
                      onsubmit="return confirm('هل أنت متأكد من حذف هذه الدفعة؟\nسيتم تحديث رصيد الصك تلقائياً.')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="w-full inline-flex justify-center items-center gap-2 px-5 py-3 text-sm font-black rounded-xl bg-rose-600 text-white hover:bg-rose-700 shadow-md shadow-rose-200/60 transition-all">
                        🗑️ حذف الدفعة
                    </button>
                </form>
            </div>
        </div>

    </div>

    {{-- ═══ LEFT: Payment Details ═══ --}}
    <div class="flex-1 min-w-0">

        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                <h6 class="text-base font-black text-slate-800 m-0">📋 بيانات الدفعة</h6>
            </div>

            <div class="p-6 space-y-6">

                {{-- Payment Summary --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-blue-50 rounded-2xl p-4 border border-blue-100">
                        <p class="text-xs text-blue-600 font-bold mb-1">إجمالي الصك</p>
                        <p class="text-2xl font-black text-blue-900">{{ number_format($payment->contract->total_amount, 0) }}<span class="text-xs text-blue-400 font-normal"> ج.م</span></p>
                    </div>
                    <div class="bg-emerald-50 rounded-2xl p-4 border border-emerald-100">
                        <p class="text-xs text-emerald-600 font-bold mb-1">المدفوع</p>
                        <p class="text-2xl font-black text-emerald-900">{{ number_format($payment->contract->paid_amount, 0) }}<span class="text-xs text-emerald-400 font-normal"> ج.م</span></p>
                    </div>
                    <div class="bg-rose-50 rounded-2xl p-4 border border-rose-100 col-span-2">
                        <p class="text-xs text-rose-600 font-bold mb-1">المتبقي</p>
                        <p class="text-2xl font-black text-rose-900">{{ number_format($payment->contract->remaining_amount, 0) }}<span class="text-xs text-rose-400 font-normal"> ج.م</span></p>
                    </div>
                </div>

                {{-- Contract Info --}}
                <div class="border-t border-slate-100 pt-6">
                    <h6 class="text-sm font-black text-slate-700 mb-4">📄 بيانات الصك</h6>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-slate-600 font-semibold">رقم الصك</span>
                            <a href="{{ route('udhiya.contracts.show', $payment->contract) }}"
                               class="text-indigo-600 font-bold hover:underline">
                                {{ $payment->contract->contract_number }}
                            </a>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-600 font-semibold">العميل</span>
                            <a href="{{ route('udhiya.reports.customer', $payment->contract->customer) }}"
                               class="text-indigo-600 font-bold hover:underline">
                                {{ $payment->contract->customer->name }}
                            </a>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-600 font-semibold">تاريخ الصك</span>
                            <span class="font-bold text-slate-800">{{ $payment->contract->created_at->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-600 font-semibold">الحالة</span>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold
                                {{ $payment->contract->remaining_amount > 0 ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                                {{ $payment->contract->remaining_amount > 0 ? '⏳ نشط' : '✅ مكتمل' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Other Payments --}}
                @php
                    $otherPayments = $payment->contract->payments->where('id', '!=', $payment->id);
                @endphp
                @if($otherPayments->count())
                <div class="border-t border-slate-100 pt-6">
                    <h6 class="text-sm font-black text-slate-700 mb-4">💰 الدفعات الأخرى</h6>
                    <div class="space-y-2 max-h-48 overflow-y-auto">
                        @foreach($otherPayments as $p)
                        <div class="flex justify-between items-center p-3 bg-slate-50 rounded-lg border border-slate-100 text-sm">
                            <div>
                                <p class="font-bold text-slate-800">{{ $p->receipt_number }}</p>
                                <p class="text-xs text-slate-500">{{ $p->date->format('d/m/Y') }}</p>
                            </div>
                            <span class="font-black text-emerald-600">{{ number_format($p->amount, 0) }} ج.م</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

            </div>
        </div>

    </div>

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const contractSelect = document.getElementById('contractSelect');
    const amountInput = document.getElementById('amountInput');
    const totalAmount = document.getElementById('totalAmount');
    const paidAmount = document.getElementById('paidAmount');
    const remainingAmount = document.getElementById('remainingAmount');
    const maxAmount = document.getElementById('maxAmount');

    function updateContractInfo() {
        const selected = contractSelect.options[contractSelect.selectedIndex];
        const total = selected.getAttribute('data-total');
        const paid = selected.getAttribute('data-paid');
        const remaining = selected.getAttribute('data-remaining');

        if (total) {
            totalAmount.textContent = new Intl.NumberFormat('ar-EG').format(total);
            paidAmount.textContent = new Intl.NumberFormat('ar-EG').format(paid);
            remainingAmount.textContent = new Intl.NumberFormat('ar-EG').format(remaining);

            // Update max amount
            const currentAmount = parseFloat(amountInput.value) || 0;
            const maxAllowed = parseFloat(remaining) + currentAmount;
            maxAmount.textContent = new Intl.NumberFormat('ar-EG').format(maxAllowed);
            amountInput.max = maxAllowed;
        }
    }

    contractSelect.addEventListener('change', updateContractInfo);
    updateContractInfo();
});
</script>
@endpush
