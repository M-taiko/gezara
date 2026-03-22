@extends('layouts.master')
@section('page-header')
<div class="page-header">
    <div class="page-leftheader">
        <h4 class="page-title mb-0">جدول الذبح</h4>
        <ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('udhiya.reports.index') }}">التقارير</a></li><li class="breadcrumb-item active">الذبح</li></ol>
    </div>
</div>
@endsection
@section('content')
@forelse($contracts as $date => $dayContracts)
<div class="card mb-3">
    <div class="card-header bg-primary text-white">
        <h6 class="mb-0"><i class="fas fa-calendar-day ml-2"></i>{{ $date }} — {{ $dayContracts->count() }} صك</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="thead-light"><tr><th>الترتيب</th><th>رقم الصك</th><th>العميل</th><th>الهاتف</th><th>الحيوانات</th><th>الإجمالي</th><th></th></tr></thead>
                <tbody>
                    @foreach($dayContracts->sortBy('slaughter_order') as $c)
                    <tr>
                        <td>{{ $c->slaughter_order ?? '—' }}</td>
                        <td>{{ $c->contract_number }}</td>
                        <td>{{ $c->customer->name }}</td>
                        <td>{{ $c->customer->phone }}</td>
                        <td>{{ $c->items->count() }}</td>
                        <td>{{ number_format($c->total_amount, 2) }} ج.م</td>
                        <td><a href="{{ route('udhiya.contracts.show', $c) }}" class="btn btn-sm btn-outline-info"><i class="fas fa-eye"></i></a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@empty
<div class="card"><div class="card-body text-center text-muted py-5"><i class="fas fa-calendar fa-3x mb-3"></i><p>لا يوجد جدول ذبح مسجّل</p></div></div>
@endforelse
@endsection
