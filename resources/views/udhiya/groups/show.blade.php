@extends('layouts.master')

@section('title', 'مجموعة: ' . $group->name)

@section('content')
<div class="main-container container-fluid">
    <div class="page-header">
        <div class="page-leftheader">
            <h4 class="page-title mb-0">{{ $group->name }}</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('udhiya.groups.index') }}">المجموعات</a></li>
                <li class="breadcrumb-item active">{{ $group->name }}</li>
            </ol>
        </div>
        <div class="page-rightheader">
            <button onclick="window.print()" class="btn btn-light">
                <i class="las la-print mr-1"></i> طباعة القائمة
            </button>
        </div>
    </div>

    <div class="row">
        {{-- Group Info Card --}}
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">بيانات المجموعة</h5>
                </div>
                <div class="card-body">
                    @php
                        $cat      = $group->animal?->product?->mainCategory;
                        $emoji    = match($cat?->code) { 'BQR' => '🐄', 'GHN' => '🐑', 'JDN' => '🐐', 'JML' => '🐪', default => '🐾' };
                        $used     = $group->usedSlots();
                        $total    = $group->totalSlots();
                        $remaining= $group->remainingSlots();
                        $pct      = $total > 0 ? round(($used / $total) * 100) : 0;
                    @endphp

                    <table class="table table-sm table-borderless">
                        <tr>
                            <th>اسم المجموعة</th>
                            <td>{{ $group->name }}</td>
                        </tr>
                        <tr>
                            <th>الحيوان</th>
                            <td>
                                {{ $emoji }}
                                <a href="{{ route('udhiya.animals.show', $group->animal) }}">
                                    {{ $group->animal?->code }}
                                </a>
                                <span class="text-muted">({{ $cat?->name }})</span>
                            </td>
                        </tr>
                        <tr>
                            <th>نوع التقسيم</th>
                            <td>{{ $group->shareLabel() }}</td>
                        </tr>
                        @if($group->slaughter_day)
                        <tr>
                            <th>يوم الذبح</th>
                            <td>{{ $group->slaughter_day->format('Y/m/d') }}</td>
                        </tr>
                        @endif
                        @if($group->notes)
                        <tr>
                            <th>ملاحظات</th>
                            <td>{{ $group->notes }}</td>
                        </tr>
                        @endif
                    </table>

                    {{-- Slots Progress --}}
                    <div class="mt-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>الأنصبة</span>
                            <span><strong>{{ $used }}</strong> / {{ $total }} نصيب</span>
                        </div>
                        <div class="progress mb-1" style="height:12px;">
                            <div class="progress-bar {{ $remaining === 0 ? 'bg-danger' : 'bg-success' }}"
                                 style="width:{{ $pct }}%">{{ $pct }}%</div>
                        </div>
                        <small class="text-muted">{{ $remaining }} نصيب متبقي</small>
                    </div>
                </div>
            </div>

            {{-- Add Member Form --}}
            @if($remaining > 0)
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="card-title mb-0"><i class="las la-user-plus mr-1"></i> إضافة عضو يدوياً</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('udhiya.groups.members.add', $group) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="form-label">العميل <span class="text-danger">*</span></label>
                            <select name="customer_id" class="form-control" required>
                                <option value="">-- اختر العميل --</option>
                                @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">عدد الأنصبة <span class="text-danger">*</span></label>
                            <input type="number" name="shares_count" class="form-control"
                                   value="1" min="1" max="{{ $remaining }}" required>
                            <small class="text-muted">المتاح: {{ $remaining }} نصيب</small>
                        </div>
                        <div class="form-group">
                            <label class="form-label">ملاحظات</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success btn-block">
                            <i class="las la-user-plus mr-1"></i> إضافة
                        </button>
                    </form>
                </div>
            </div>
            @else
            <div class="alert alert-warning mt-3">
                <i class="las la-exclamation-triangle mr-1"></i>
                المجموعة مكتملة — لا تتوفر أنصبة
            </div>
            @endif
        </div>

        {{-- Members Table --}}
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        أعضاء المجموعة
                        <span class="badge badge-primary">{{ $group->members->count() }}</span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($group->members->count())
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>اسم العميل</th>
                                    <th>الهاتف</th>
                                    <th>الأنصبة</th>
                                    <th>الصك</th>
                                    <th>ملاحظات</th>
                                    <th class="no-print">حذف</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($group->members as $i => $member)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>
                                        <strong>{{ $member->customer?->name }}</strong>
                                    </td>
                                    <td>{{ $member->customer?->phone ?? '—' }}</td>
                                    <td>
                                        <span class="badge badge-info">
                                            {{ $member->shares_count }} نصيب
                                        </span>
                                    </td>
                                    <td>
                                        @if($member->contractItem)
                                        <a href="{{ route('udhiya.contracts.show', $member->contractItem->contract_id) }}"
                                           class="text-primary font-weight-bold">
                                            {{ $member->contractItem->contract?->contract_number }}
                                        </a>
                                        @else
                                        <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>{{ $member->notes ?? '—' }}</td>
                                    <td class="no-print">
                                        @if(!$member->contract_item_id)
                                        <form action="{{ route('udhiya.groups.members.remove', [$group, $member]) }}"
                                              method="POST"
                                              onsubmit="return confirm('تأكيد حذف العضو؟')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="las la-trash"></i>
                                            </button>
                                        </form>
                                        @else
                                        <span class="badge badge-secondary" title="مربوط بصك">🔒</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center text-muted py-5">
                        <i class="las la-users" style="font-size:2.5rem;"></i>
                        <p class="mt-2">لا يوجد أعضاء في هذه المجموعة بعد</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print { display: none !important; }
    .page-header, .breadcrumb, .btn { display: none !important; }
    .card { border: 1px solid #ddd !important; box-shadow: none !important; }
}
</style>
@endsection
