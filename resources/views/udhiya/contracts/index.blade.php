@extends('layouts.master')
@section('page-header')
<div class="page-header">
    <div class="page-leftheader">
        <h4 class="page-title mb-0">الصكوك</h4>
        <ol class="breadcrumb"><li class="breadcrumb-item"><a href="#">الأضاحي</a></li><li class="breadcrumb-item active">الصكوك</li></ol>
    </div>
    <div class="page-rightheader">
        <a href="{{ route('udhiya.contracts.create') }}" class="btn btn-primary">
            <i class="fas fa-plus ml-1"></i> صك جديد
        </a>
    </div>
</div>
@endsection
@section('content')
<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="thead-light">
                <tr><th>رقم الصك</th><th>العميل</th><th>يوم الذبح</th><th>الإجمالي</th><th>المحصّل</th><th>المتبقي</th><th>الحالة</th><th>الإجراءات</th></tr>
            </thead>
            <tbody>
                @forelse($contracts as $contract)
                <tr>
                    <td><a href="{{ route('udhiya.contracts.show', $contract) }}">{{ $contract->contract_number }}</a></td>
                    <td>{{ $contract->customer->name }}</td>
                    <td>{{ $contract->slaughter_day ?? '—' }}</td>
                    <td>{{ number_format($contract->total_amount, 2) }} ج.م</td>
                    <td class="text-success">{{ number_format($contract->paid_amount, 2) }} ج.م</td>
                    <td class="{{ $contract->remaining_amount > 0 ? 'text-danger' : 'text-success' }}">{{ number_format($contract->remaining_amount, 2) }} ج.م</td>
                    <td>
                        @if($contract->status === 'active') <span class="badge badge-warning">نشط</span>
                        @elseif($contract->status === 'completed') <span class="badge badge-success">مكتمل</span>
                        @else <span class="badge badge-danger">ملغى</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('udhiya.contracts.show', $contract) }}" class="btn btn-sm btn-outline-info"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('udhiya.contracts.print', $contract) }}" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="fas fa-print"></i></a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-4">لا توجد صكوك بعد</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($contracts->hasPages())
    <div class="card-footer">{{ $contracts->links() }}</div>
    @endif
</div>
@endsection
