@extends('layouts.master')
@section('page-header')
<div class="page-header">
    <div class="page-leftheader">
        <h4 class="page-title mb-0">صك #{{ $contract->contract_number }}</h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('udhiya.contracts.index') }}">الصكوك</a></li>
            <li class="breadcrumb-item active">{{ $contract->contract_number }}</li>
        </ol>
    </div>
    <div class="page-rightheader">
        <a href="{{ route('udhiya.contracts.print', $contract) }}" target="_blank" class="btn btn-outline-secondary ml-2">
            <i class="fas fa-print ml-1"></i> طباعة الصك
        </a>
        @if($contract->status !== 'cancelled')
        <form action="{{ route('udhiya.contracts.destroy', $contract) }}" method="POST" class="d-inline"
              onsubmit="return confirm('هل تريد إلغاء هذا الصك؟')">
            @csrf @method('DELETE')
            <button class="btn btn-outline-danger"><i class="fas fa-ban ml-1"></i> إلغاء الصك</button>
        </form>
        @endif
    </div>
</div>
@endsection
@section('content')
<div class="row">
    <div class="col-xl-4">
        <div class="card">
            <div class="card-header"><h6 class="card-title mb-0">تفاصيل الصك</h6></div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr><th class="text-muted">رقم الصك</th><td><strong>{{ $contract->contract_number }}</strong></td></tr>
                    <tr><th class="text-muted">العميل</th><td>{{ $contract->customer->name }}</td></tr>
                    <tr><th class="text-muted">هاتف العميل</th><td>{{ $contract->customer->phone }}</td></tr>
                    <tr><th class="text-muted">يوم الذبح</th><td>{{ $contract->slaughter_day ?? '—' }}</td></tr>
                    <tr><th class="text-muted">ترتيب الذبح</th><td>{{ $contract->slaughter_order ?? '—' }}</td></tr>
                    <tr><th class="text-muted">الإجمالي</th><td><strong>{{ number_format($contract->total_amount, 2) }} ج.م</strong></td></tr>
                    <tr><th class="text-muted">المحصّل</th><td class="text-success">{{ number_format($contract->paid_amount, 2) }} ج.م</td></tr>
                    <tr><th class="text-muted">المتبقي</th><td class="{{ $contract->remaining_amount > 0 ? 'text-danger' : 'text-success' }}">{{ number_format($contract->remaining_amount, 2) }} ج.م</td></tr>
                    <tr><th class="text-muted">الحالة</th><td>
                        @if($contract->status === 'active') <span class="badge badge-warning">نشط</span>
                        @elseif($contract->status === 'completed') <span class="badge badge-success">مكتمل</span>
                        @else <span class="badge badge-danger">ملغى</span>
                        @endif
                    </td></tr>
                    @if($contract->notes)<tr><th class="text-muted">ملاحظات</th><td>{{ $contract->notes }}</td></tr>@endif
                </table>
            </div>
        </div>

        {{-- Add Payment --}}
        @if($contract->status === 'active' && $contract->remaining_amount > 0)
        <div class="card">
            <div class="card-header"><h6 class="card-title mb-0">إضافة دفعة</h6></div>
            <div class="card-body">
                <form action="{{ route('udhiya.payments.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="contract_id" value="{{ $contract->id }}">
                    <div class="form-group">
                        <label>المبلغ <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="amount" class="form-control" step="0.01" min="0.01"
                                   max="{{ $contract->remaining_amount }}" required>
                            <div class="input-group-append"><span class="input-group-text">ج.م</span></div>
                        </div>
                        <small class="text-muted">الحد الأقصى: {{ number_format($contract->remaining_amount, 2) }} ج.م</small>
                    </div>
                    <div class="form-group">
                        <label>طريقة الدفع</label>
                        <select name="payment_method" class="form-control">
                            <option value="cash">نقدي</option>
                            <option value="bank">بنك</option>
                            <option value="transfer">تحويل</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>التاريخ <span class="text-danger">*</span></label>
                        <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="form-group">
                        <label>ملاحظات</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                    <button type="submit" class="btn btn-success btn-block">
                        <i class="fas fa-money-bill ml-1"></i> تسجيل الدفعة
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>

    <div class="col-xl-8">
        {{-- Contract Items --}}
        <div class="card">
            <div class="card-header"><h6 class="card-title mb-0">الحيوانات والأنصبة</h6></div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead class="thead-light"><tr><th>الحيوان</th><th>النوع</th><th>نوع الحصة</th><th>الأنصبة</th><th>سعر الوحدة</th><th>الإجمالي</th></tr></thead>
                    <tbody>
                        @foreach($contract->items as $item)
                        <tr>
                            <td><a href="{{ route('udhiya.animals.show', $item->animal) }}">{{ $item->animal->code }}</a></td>
                            <td>{{ $item->animal->product->name }}</td>
                            <td>{{ $item->share_type === 'full' ? 'كامل' : ($item->share_type ?? '—') }}</td>
                            <td>{{ $item->shares_count }}</td>
                            <td>{{ number_format($item->unit_price, 2) }} ج.م</td>
                            <td><strong>{{ number_format($item->total_price, 2) }} ج.م</strong></td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-light"><td colspan="5" class="text-left font-weight-bold">الإجمالي</td><td class="font-weight-bold">{{ number_format($contract->total_amount, 2) }} ج.م</td></tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Payments --}}
        <div class="card">
            <div class="card-header"><h6 class="card-title mb-0">الدفعات ({{ $contract->payments->count() }})</h6></div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead class="thead-light"><tr><th>رقم الإيصال</th><th>التاريخ</th><th>طريقة الدفع</th><th>المبلغ</th><th>ملاحظات</th><th></th></tr></thead>
                    <tbody>
                        @forelse($contract->payments as $payment)
                        <tr>
                            <td>{{ $payment->receipt_number }}</td>
                            <td>{{ $payment->date }}</td>
                            <td>{{ ['cash'=>'نقدي','bank'=>'بنك','transfer'=>'تحويل'][$payment->payment_method] }}</td>
                            <td class="text-success"><strong>{{ number_format($payment->amount, 2) }} ج.م</strong></td>
                            <td>{{ $payment->notes ?? '—' }}</td>
                            <td><a href="{{ route('udhiya.payments.print', $payment) }}" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="fas fa-print"></i></a></td>
                        </tr>
                        @empty<tr><td colspan="6" class="text-center text-muted">لا توجد دفعات بعد</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
