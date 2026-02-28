@extends('layouts.master')
@section('page-header')
<div class="page-header">
    <div class="page-leftheader">
        <h4 class="page-title mb-0">التقارير</h4>
        <ol class="breadcrumb"><li class="breadcrumb-item"><a href="#">الأضاحي</a></li><li class="breadcrumb-item active">التقارير</li></ol>
    </div>
</div>
@endsection
@section('content')
<div class="row">
    @foreach([
        ['route' => route('udhiya.reports.animals'), 'icon' => 'fa-horse', 'title' => 'تقرير الحيوانات', 'desc' => 'نسب الإشغال وحالات الحيوانات', 'color' => 'primary'],
        ['route' => route('udhiya.reports.profit'), 'icon' => 'fa-chart-line', 'title' => 'تقرير الأرباح', 'desc' => 'الإيرادات والتكاليف والربح الصافي', 'color' => 'success'],
        ['route' => route('udhiya.reports.slaughter'), 'icon' => 'fa-calendar-alt', 'title' => 'جدول الذبح', 'desc' => 'مواعيد الذبح مرتبة بالتاريخ والترتيب', 'color' => 'warning'],
    ] as $report)
    <div class="col-xl-4 col-md-6">
        <a href="{{ $report['route'] }}" class="card card-link text-decoration-none">
            <div class="card-body text-center py-5">
                <div class="icon-box bg-{{ $report['color'] }} text-white rounded-circle mx-auto mb-3" style="width:70px;height:70px;line-height:70px;font-size:28px;">
                    <i class="fas {{ $report['icon'] }}"></i>
                </div>
                <h5 class="text-dark">{{ $report['title'] }}</h5>
                <p class="text-muted mb-0">{{ $report['desc'] }}</p>
            </div>
        </a>
    </div>
    @endforeach
</div>
@endsection
