@extends('layouts.master')

@section('page-header')
<div class="page-header">
    <div class="page-leftheader">
        <h4 class="page-title"><span class="page-title-emoji">🛒</span> المشتريات</h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('udhiya.dashboard') }}">الرئيسية</a></li>
            <li class="breadcrumb-item active">المشتريات</li>
        </ol>
    </div>
    <div class="page-rightheader">
        <a href="{{ route('udhiya.purchases.create') }}" class="btn btn-primary">
            ➕ مشترى جديد
        </a>
    </div>
</div>
@endsection

@section('content')

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>🛒 قائمة المشتريات</span>
        <span class="badge badge-primary">{{ $purchases->total() }} فاتورة</span>
    </div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>المورد</th>
                    <th>التاريخ</th>
                    <th>الإجمالي</th>
                    <th>المدفوع</th>
                    <th>المتبقي</th>
                    <th>الحالة</th>
                    <th class="text-center">إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($purchases as $purchase)
                @php $remaining = $purchase->total - $purchase->paid; @endphp
                <tr>
                    <td style="color:var(--text-muted);font-size:.82rem;">{{ $purchase->id }}</td>
                    <td><strong>{{ $purchase->supplier->name }}</strong></td>
                    <td style="font-size:.82rem;color:var(--text-muted);">{{ $purchase->date }}</td>
                    <td>{{ number_format($purchase->total) }} <small class="text-muted">ج.م</small></td>
                    <td><span class="badge badge-success">{{ number_format($purchase->paid) }} ج.م</span></td>
                    <td>
                        @if($remaining > 0)
                            <span class="badge badge-danger">{{ number_format($remaining) }} ج.م</span>
                        @else
                            <span class="badge badge-success">مسدد ✅</span>
                        @endif
                    </td>
                    <td>
                        @if($purchase->status === 'confirmed')
                            <span class="badge badge-success">✅ مؤكدة</span>
                        @elseif($purchase->status === 'pending')
                            <span class="badge badge-warning">⏳ معلقة</span>
                        @else
                            <span class="badge badge-secondary">{{ $purchase->status }}</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <a href="{{ route('udhiya.purchases.show', $purchase) }}"
                           class="btn btn-sm btn-info btn-action" title="عرض">👁️</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="empty-state">
                            <span class="empty-icon">🛒</span>
                            <p>لا توجد مشتريات بعد</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($purchases->hasPages())
    <div class="card-footer">{{ $purchases->links() }}</div>
    @endif
</div>

@endsection
