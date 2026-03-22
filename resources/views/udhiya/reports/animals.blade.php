@extends('layouts.master')
@section('page-header')
<div class="page-header">
    <div class="page-leftheader">
        <h4 class="page-title mb-0">تقرير الحيوانات</h4>
        <ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('udhiya.reports.index') }}">التقارير</a></li><li class="breadcrumb-item active">الحيوانات</li></ol>
    </div>
</div>
@endsection
@section('content')
<div class="row mb-4">
    @foreach(['total'=>['لون','إجمالي الحيوانات',$summary['total'],'primary'],'available'=>['لون','متاح',$summary['available'],'success'],'partially'=>['لون','مخصص جزئياً',$summary['partially'],'warning'],'fully'=>['لون','مخصص كلياً',$summary['fully'],'info'],'slaughtered'=>['لون','مذبوح',$summary['slaughtered'],'dark']] as $key => $s)
    <div class="col-xl-2 col-md-4 col-6">
        <div class="card">
            <div class="card-body text-center py-3">
                <h4 class="mb-0">{{ $s[2] }}</h4>
                <small class="text-muted">{{ $s[1] }}</small>
            </div>
        </div>
    </div>
    @endforeach
</div>
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr><th>الكود</th><th>النوع</th><th>الفئة</th><th>المخزن</th><th>التكلفة</th><th>الحالة</th><th>نظام الأنصبة</th></tr>
                </thead>
                <tbody>
                    @foreach($animals as $animal)
                    <tr>
                        <td><a href="{{ route('udhiya.animals.show', $animal) }}">{{ $animal->code }}</a></td>
                        <td>{{ $animal->product->name }}</td>
                        <td>{{ $animal->product->mainCategory->name }}</td>
                        <td>{{ $animal->warehouse->name }}</td>
                        <td>{{ number_format($animal->cost, 2) }} ج.م</td>
                        <td>
                            @php $labels = \App\Models\Animal::STATUS_LABELS; @endphp
                            <span class="badge badge-{{ ['available'=>'success','partially_allocated'=>'warning','fully_allocated'=>'primary','slaughtered'=>'dark'][$animal->status] ?? 'secondary' }}">
                                {{ $labels[$animal->status] ?? $animal->status }}
                            </span>
                        </td>
                        <td>
                            @if($animal->is_grouped && $animal->shareSetting)
                            <div class="progress" style="height:18px;" title="{{ $animal->shareSetting->sold_shares }}/{{ $animal->shareSetting->total_shares }}">
                                <div class="progress-bar" style="width:{{ $animal->shareSetting->completionPercentage() }}%">
                                    {{ $animal->shareSetting->completionPercentage() }}%
                                </div>
                            </div>
                            @else <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
