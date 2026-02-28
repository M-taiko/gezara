@extends('layouts.master')

@section('title', 'مجموعات الذبح')

@section('content')
<div class="main-container container-fluid">
    <div class="page-header">
        <div class="page-leftheader">
            <h4 class="page-title mb-0">مجموعات الذبح</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('udhiya.dashboard') }}">الأضاحي</a></li>
                <li class="breadcrumb-item active">المجموعات</li>
            </ol>
        </div>
        <div class="page-rightheader">
            <a href="{{ route('udhiya.groups.create') }}" class="btn btn-primary">
                <i class="las la-plus mr-1"></i> مجموعة جديدة
            </a>
        </div>
    </div>

    {{-- Search --}}
    <div class="row mb-3">
        <div class="col-md-5">
            <form method="GET" action="{{ route('udhiya.groups.index') }}">
                <div class="input-group">
                    <input type="text" name="search" class="form-control"
                           placeholder="ابحث باسم المجموعة أو اسم العميل..."
                           value="{{ $search }}">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">
                            <i class="las la-search"></i>
                        </button>
                        @if($search)
                        <a href="{{ route('udhiya.groups.index') }}" class="btn btn-light">
                            <i class="las la-times"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        @forelse($groups as $group)
        @php
            $used      = $group->usedSlots();
            $total     = $group->totalSlots();
            $remaining = $group->remainingSlots();
            $pct       = $total > 0 ? round(($used / $total) * 100) : 0;
            $cat       = $group->animal?->product?->mainCategory;
            $emoji     = match($cat?->code) { 'BQR' => '🐄', 'GHN' => '🐑', 'JDN' => '🐐', 'JML' => '🐪', default => '🐾' };
        @endphp
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0 font-weight-bold">
                        {{ $emoji }} {{ $group->name }}
                    </h6>
                    <span class="badge badge-{{ $remaining > 0 ? 'success' : 'danger' }}">
                        {{ $remaining > 0 ? 'متاح' : 'مكتمل' }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2 text-muted" style="font-size:.85rem;">
                        <span><i class="las la-paw mr-1"></i>{{ $group->animal?->code }}</span>
                        <span>{{ $cat?->name }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 text-muted" style="font-size:.85rem;">
                        <span><i class="las la-share-alt mr-1"></i>{{ $group->shareLabel() }}</span>
                        @if($group->slaughter_day)
                        <span><i class="las la-calendar mr-1"></i>{{ $group->slaughter_day->format('Y/m/d') }}</span>
                        @endif
                    </div>

                    {{-- Progress --}}
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1" style="font-size:.82rem;">
                            <span>الأنصبة المشغولة</span>
                            <span><strong>{{ $used }}</strong> / {{ $total }}</span>
                        </div>
                        <div class="progress" style="height:8px;">
                            <div class="progress-bar {{ $remaining === 0 ? 'bg-danger' : 'bg-success' }}"
                                 style="width:{{ $pct }}%"></div>
                        </div>
                    </div>

                    {{-- Members preview --}}
                    @if($group->members->count())
                    <div style="font-size:.82rem;">
                        <strong>الأعضاء:</strong>
                        @foreach($group->members->take(3) as $member)
                        <div class="text-muted">
                            <i class="las la-user mr-1"></i>
                            {{ $member->customer?->name }}
                            <span class="badge badge-light">{{ $member->shares_count }} نصيب</span>
                            @if($member->contractItem)
                            <a href="{{ route('udhiya.contracts.show', $member->contractItem->contract_id) }}"
                               class="text-primary" style="font-size:.75rem;">
                                ({{ $member->contractItem->contract?->contract_number }})
                            </a>
                            @endif
                        </div>
                        @endforeach
                        @if($group->members->count() > 3)
                        <div class="text-muted">... و {{ $group->members->count() - 3 }} آخرين</div>
                        @endif
                    </div>
                    @else
                    <p class="text-muted mb-0" style="font-size:.85rem;">لا يوجد أعضاء بعد</p>
                    @endif
                </div>
                <div class="card-footer text-left">
                    <a href="{{ route('udhiya.groups.show', $group) }}" class="btn btn-sm btn-primary">
                        <i class="las la-eye mr-1"></i> عرض التفاصيل
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center text-muted py-5">
                    <i class="las la-layer-group" style="font-size:3rem;"></i>
                    <h5 class="mt-2">{{ $search ? 'لا توجد نتائج للبحث' : 'لا توجد مجموعات بعد' }}</h5>
                    @unless($search)
                    <a href="{{ route('udhiya.groups.create') }}" class="btn btn-primary mt-2">إنشاء أول مجموعة</a>
                    @endunless
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection
