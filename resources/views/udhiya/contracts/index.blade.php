@extends('layouts.master')

@section('page-header')
<div class="page-header">
    <div class="page-leftheader">
        <h4 class="page-title"><span class="page-title-emoji">📋</span> الصكوك</h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('udhiya.dashboard') }}">الرئيسية</a></li>
            <li class="breadcrumb-item active">الصكوك</li>
        </ol>
    </div>
    <div class="page-rightheader">
        <a href="{{ route('udhiya.contracts.create') }}" class="btn btn-primary">
            ➕ صك جديد
        </a>
    </div>
</div>
@endsection

@section('content')

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>📋 قائمة الصكوك</span>
        <span class="badge badge-primary">{{ $contracts->total() }} صك</span>
    </div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>رقم الصك</th>
                    <th>العميل</th>
                    <th>يوم الذبح</th>
                    <th>الإجمالي</th>
                    <th>المحصّل</th>
                    <th>المتبقي</th>
                    <th>الحالة</th>
                    <th class="text-center">إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contracts as $contract)
                <tr>
                    <td>
                        <a href="{{ route('udhiya.contracts.show', $contract) }}"
                           style="color:var(--primary);font-weight:700;">
                            {{ $contract->contract_number }}
                        </a>
                    </td>
                    <td><strong>{{ $contract->customer->name }}</strong></td>
                    <td style="font-size:.82rem;color:var(--text-muted);">{{ $contract->slaughter_day ?? '—' }}</td>
                    <td>{{ number_format($contract->total_amount) }} <small class="text-muted">ج.م</small></td>
                    <td><span class="badge badge-success">{{ number_format($contract->paid_amount) }} ج.م</span></td>
                    <td>
                        @if($contract->remaining_amount > 0)
                            <span class="badge badge-danger">{{ number_format($contract->remaining_amount) }} ج.م</span>
                        @else
                            <span class="badge badge-success">مسدد ✅</span>
                        @endif
                    </td>
                    <td>
                        @if($contract->status === 'active')
                            <span class="badge badge-warning">🟡 نشط</span>
                        @elseif($contract->status === 'completed')
                            <span class="badge badge-success">✅ مكتمل</span>
                        @else
                            <span class="badge badge-danger">🚫 ملغى</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            <a href="{{ route('udhiya.contracts.show', $contract) }}"
                               class="btn btn-sm btn-info btn-action" title="عرض">👁️</a>
                            <a href="{{ route('udhiya.contracts.print', $contract) }}" target="_blank"
                               class="btn btn-sm btn-secondary btn-action" title="طباعة">🖨️</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="empty-state">
                            <span class="empty-icon">📋</span>
                            <p>لا توجد صكوك بعد</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($contracts->hasPages())
    <div class="card-footer">{{ $contracts->links() }}</div>
    @endif
</div>

@endsection
