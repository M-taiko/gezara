@extends('layouts.master')
@section('page-header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-8 mb-16">
    <div>
        <h1 class="text-2xl font-black text-slate-800 tracking-tight flex items-center gap-4">تقرير الحيوانات</h1>
        <div class="mt-2 flex items-center text-sm text-slate-500"><li class="breadcrumb-item"><a href="{{ route('udhiya.reports.index') }}">التقارير</a></li><span>الحيوانات</span></div>
    </div>
</div>
@endsection
@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-12 mb-12">
    @foreach(['total'=>['لون','إجمالي الحيوانات',$summary['total'],'primary'],'available'=>['لون','متاح',$summary['available'],'success'],'partially'=>['لون','مخصص جزئياً',$summary['partially'],'warning'],'fully'=>['لون','مخصص كلياً',$summary['fully'],'info'],'slaughtered'=>['لون','مذبوح',$summary['slaughtered'],'dark']] as $key => $s)
    <div class="col-xl-2 col-md-4 col-6">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-12 flex flex-col h-full hover:shadow-md transition-shadow duration-300">
            <div class="p-12 flex-1 text-center py-3">
                <h4 class="mb-0">{{ $s[2] }}</h4>
                <small class="text-slate-500">{{ $s[1] }}</small>
            </div>
        </div>
    </div>
    @endforeach
</div>
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-12 flex flex-col h-full hover:shadow-md transition-shadow duration-300">
    <div class="p-12 flex-1 p-0">
        <div class="overflow-x-auto ring-1 ring-slate-100 sm:rounded-lg">
            <table class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr><th class="px-6 py-4 font-bold tracking-wider">الكود</th><th class="px-6 py-4 font-bold tracking-wider">النوع</th><th class="px-6 py-4 font-bold tracking-wider">الفئة</th><th class="px-6 py-4 font-bold tracking-wider">المخزن</th><th class="px-6 py-4 font-bold tracking-wider">التكلفة</th><th class="px-6 py-4 font-bold tracking-wider">الحالة</th><th class="px-6 py-4 font-bold tracking-wider">نظام الأنصبة</th></tr>
                </thead>
                <tbody>
                    @foreach($animals as $animal)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap"><a href="{{ route('udhiya.animals.show', $animal) }}">{{ $animal->code }}</a></td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $animal->product->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $animal->product->mainCategory->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $animal->warehouse->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ number_format($animal->cost, 2) }} ج.م</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php $labels = \App\Models\Animal::STATUS_LABELS; @endphp
                            <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium badge-{{ ['available'=>'success','partially_allocated'=>'warning','fully_allocated'=>'primary','slaughtered'=>'dark'][$animal->status] ?? 'secondary' }}">
                                {{ $labels[$animal->status] ?? $animal->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($animal->is_grouped && $animal->shareSetting)
                            <div class="progress" style="height:18px;" title="{{ $animal->shareSetting->sold_shares }}/{{ $animal->shareSetting->total_shares }}">
                                <div class="progress-bar" style="width:{{ $animal->shareSetting->completionPercentage() }}%">
                                    {{ $animal->shareSetting->completionPercentage() }}%
                                </div>
                            </div>
                            @else <span class="text-slate-500">—</span>
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
