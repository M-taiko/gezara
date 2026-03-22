@extends('layouts.master')
@section('page-header')
<div class="page-header">
    <div class="page-leftheader">
        <h4 class="page-title mb-0">حيوان — {{ $animal->code }}</h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('udhiya.animals.index') }}">الحيوانات</a></li>
            <li class="breadcrumb-item active">{{ $animal->code }}</li>
        </ol>
    </div>
</div>
@endsection
@section('content')
<div class="row">
    {{-- Details Card --}}
    <div class="col-12 col-lg-4">
        <div class="card">
            <div class="card-header"><h6 class="card-title mb-0">البيانات الأساسية</h6></div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr><th class="text-muted">الكود</th><td><strong>{{ $animal->code }}</strong></td></tr>
                    <tr><th class="text-muted">النوع</th><td>{{ $animal->product->name }}</td></tr>
                    <tr><th class="text-muted">الفئة</th><td>{{ $animal->product->mainCategory->name }}</td></tr>
                    <tr><th class="text-muted">المخزن</th><td>{{ $animal->warehouse->name }}</td></tr>
                    <tr><th class="text-muted">الوزن</th><td>{{ $animal->weight ? $animal->weight . ' كجم' : '—' }}</td></tr>
                    <tr><th class="text-muted">التكلفة</th><td>{{ number_format($animal->cost, 2) }} ج.م</td></tr>
                    <tr><th class="text-muted">المورد</th><td>{{ $animal->supplier->name ?? '—' }}</td></tr>
                    <tr><th class="text-muted">الحالة</th><td>
                        @php $labels = \App\Models\Animal::STATUS_LABELS; @endphp
                        <span class="badge badge-{{ ['available'=>'success','partially_allocated'=>'warning','fully_allocated'=>'primary','slaughtered'=>'dark'][$animal->status] ?? 'secondary' }}">
                            {{ $labels[$animal->status] ?? $animal->status }}
                        </span>
                    </td></tr>
                    @if($animal->notes)<tr><th class="text-muted">ملاحظات</th><td>{{ $animal->notes }}</td></tr>@endif
                </table>
            </div>
        </div>

        {{-- Code Edit Card --}}
        <div class="card">
            <div class="card-header"><h6 class="card-title mb-0">تعديل الكود</h6></div>
            <div class="card-body">
                <form action="{{ route('udhiya.animals.update-code', $animal) }}" method="POST">
                    @csrf @method('PATCH')
                    <div class="input-group input-group-sm">
                        <input type="text" name="code" class="form-control"
                               value="{{ $animal->code }}" required>
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-warning">تحديث</button>
                        </div>
                    </div>
                    @error('code')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </form>
            </div>
        </div>

        {{-- Prices Card --}}
        <div class="card">
            <div class="card-header"><h6 class="card-title mb-0">الأسعار</h6></div>
            <div class="card-body">
                <form action="{{ route('udhiya.animals.update-prices', $animal) }}" method="POST">
                    @csrf @method('PATCH')
                    @foreach(['price_full'=>'سعر الكامل','price_seven'=>'سعر السُبع','price_five'=>'سعر الخُمس','price_quarter'=>'سعر الربع','price_half'=>'سعر النصف'] as $field => $label)
                    <div class="form-group mb-2">
                        <label class="small mb-1">{{ $label }}</label>
                        <div class="input-group input-group-sm">
                            <input type="number" name="{{ $field }}" class="form-control" step="0.01" value="{{ $animal->$field }}">
                            <div class="input-group-append"><span class="input-group-text">ج.م</span></div>
                        </div>
                    </div>
                    @endforeach
                    <button type="submit" class="btn btn-sm btn-primary btn-block mt-2">حفظ الأسعار</button>
                </form>
            </div>
        </div>

        {{-- Share Settings --}}
        @if($animal->status === 'available' || $animal->is_grouped)
        <div class="card">
            <div class="card-header"><h6 class="card-title mb-0">نظام الأنصبة</h6></div>
            <div class="card-body">
                @if($animal->is_grouped && $animal->shareSetting)
                    <div class="alert alert-info mb-3">
                        <strong>{{ \App\Models\AnimalShareSetting::SHARE_TYPE_LABELS[$animal->shareSetting->share_type] }}</strong><br>
                        الإجمالي: {{ $animal->shareSetting->total_shares }} |
                        المباع: {{ $animal->shareSetting->sold_shares }} |
                        المتبقي: {{ $animal->shareSetting->remaining_shares }}
                    </div>
                    @if($animal->shareSetting->sold_shares === 0)
                    <form action="{{ route('udhiya.animals.unset-grouped', $animal) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger btn-block" onclick="return confirm('إلغاء نظام الأنصبة؟')">
                            إلغاء نظام الأنصبة
                        </button>
                    </form>
                    @endif
                @else
                    <form action="{{ route('udhiya.animals.set-grouped', $animal) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>نوع الأنصبة</label>
                            <select name="share_type" class="form-control form-control-sm">
                                @foreach(\App\Models\AnimalShareSetting::SHARE_TYPE_LABELS as $val => $label)
                                <option value="{{ $val }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-sm btn-info btn-block">تفعيل نظام الأنصبة</button>
                    </form>
                @endif
            </div>
        </div>
        @endif

        {{-- Transfer --}}
        @if($warehouses->count() > 0)
        <div class="card">
            <div class="card-header"><h6 class="card-title mb-0">نقل إلى مخزن</h6></div>
            <div class="card-body">
                <form action="{{ route('udhiya.animals.transfer', $animal) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <select name="to_warehouse_id" class="form-control form-control-sm">
                            @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <textarea name="notes" class="form-control form-control-sm" placeholder="ملاحظات النقل" rows="2"></textarea>
                    </div>
                    <button type="submit" class="btn btn-sm btn-outline-primary btn-block">نقل</button>
                </form>
            </div>
        </div>
        @endif
    </div>

    <div class="col-12 col-lg-8">
        {{-- Contracts --}}
        <div class="card">
            <div class="card-header"><h6 class="card-title mb-0">الصكوك المرتبطة</h6></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="thead-light"><tr><th>رقم الصك</th><th>العميل</th><th>نوع الحصة</th><th>الأنصبة</th><th>السعر</th><th></th></tr></thead>
                        <tbody>
                            @forelse($animal->contractItems as $item)
                            <tr>
                                <td>{{ $item->contract->contract_number }}</td>
                                <td>{{ $item->contract->customer->name }}</td>
                                <td>{{ $item->share_type ?? 'كامل' }}</td>
                                <td>{{ $item->shares_count }}</td>
                                <td>{{ number_format($item->total_price, 2) }} ج.م</td>
                                <td><a href="{{ route('udhiya.contracts.show', $item->contract) }}" class="btn btn-sm btn-outline-info"><i class="fas fa-eye"></i></a></td>
                            </tr>
                            @empty<tr><td colspan="6" class="text-center text-muted">لا توجد صكوك</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Transfers History --}}
        <div class="card">
            <div class="card-header"><h6 class="card-title mb-0">سجل النقل</h6></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="thead-light"><tr><th>من</th><th>إلى</th><th>بواسطة</th><th>التاريخ</th><th>ملاحظات</th></tr></thead>
                        <tbody>
                            @forelse($animal->transfers as $transfer)
                            <tr>
                                <td>{{ $transfer->fromWarehouse->name }}</td>
                                <td>{{ $transfer->toWarehouse->name }}</td>
                                <td>{{ $transfer->transferredBy->name }}</td>
                                <td>{{ $transfer->transferred_at->format('Y-m-d H:i') }}</td>
                                <td>{{ $transfer->notes ?? '—' }}</td>
                            </tr>
                            @empty<tr><td colspan="5" class="text-center text-muted">لا توجد عمليات نقل</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
