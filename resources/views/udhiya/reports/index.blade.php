@extends('layouts.master')

@section('page-header')
<div class="page-header">
    <div class="page-leftheader">
        <h4 class="page-title"><span class="page-title-emoji">📊</span> التقارير</h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('udhiya.dashboard') }}">الرئيسية</a></li>
            <li class="breadcrumb-item active">التقارير</li>
        </ol>
    </div>
</div>
@endsection

@section('content')

<div class="row g-3">
    <div class="col-12 col-md-4">
        <a href="{{ route('udhiya.reports.animals') }}" class="text-decoration-none">
            <div class="card h-100 text-center" style="cursor:pointer;transition:transform .2s;"
                 onmouseover="this.style.transform='translateY(-4px)'"
                 onmouseout="this.style.transform='translateY(0)'">
                <div class="card-body py-5">
                    <div style="font-size:3rem;margin-bottom:.75rem;">🐄</div>
                    <h5 style="font-weight:700;color:var(--primary);">تقرير الحيوانات</h5>
                    <p style="font-size:.85rem;color:var(--text-muted);margin:0;">نسب الإشغال وحالات الحيوانات</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-12 col-md-4">
        <a href="{{ route('udhiya.reports.profit') }}" class="text-decoration-none">
            <div class="card h-100 text-center" style="cursor:pointer;transition:transform .2s;"
                 onmouseover="this.style.transform='translateY(-4px)'"
                 onmouseout="this.style.transform='translateY(0)'">
                <div class="card-body py-5">
                    <div style="font-size:3rem;margin-bottom:.75rem;">💹</div>
                    <h5 style="font-weight:700;color:var(--primary);">تقرير الأرباح</h5>
                    <p style="font-size:.85rem;color:var(--text-muted);margin:0;">الإيرادات والتكاليف والربح الصافي</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-12 col-md-4">
        <a href="{{ route('udhiya.reports.slaughter') }}" class="text-decoration-none">
            <div class="card h-100 text-center" style="cursor:pointer;transition:transform .2s;"
                 onmouseover="this.style.transform='translateY(-4px)'"
                 onmouseout="this.style.transform='translateY(0)'">
                <div class="card-body py-5">
                    <div style="font-size:3rem;margin-bottom:.75rem;">📅</div>
                    <h5 style="font-weight:700;color:var(--primary);">جدول الذبح</h5>
                    <p style="font-size:.85rem;color:var(--text-muted);margin:0;">مواعيد الذبح مرتبة بالتاريخ والترتيب</p>
                </div>
            </div>
        </a>
    </div>
</div>

@endsection
