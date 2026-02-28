@extends('layouts.master')

@section('title', 'إنشاء مجموعة جديدة')

@section('content')
<div class="main-container container-fluid">
    <div class="page-header">
        <div class="page-leftheader">
            <h4 class="page-title mb-0">إنشاء مجموعة جديدة</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('udhiya.groups.index') }}">المجموعات</a></li>
                <li class="breadcrumb-item active">جديد</li>
            </ol>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">بيانات المجموعة</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('udhiya.groups.store') }}" method="POST">
                        @csrf

                        {{-- Name --}}
                        <div class="form-group">
                            <label class="form-label">اسم المجموعة <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" placeholder="مثال: مجموعة عجل 1" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Animal --}}
                        <div class="form-group">
                            <label class="form-label">الحيوان <span class="text-danger">*</span></label>
                            <select name="animal_id" id="animalSelect"
                                    class="form-control @error('animal_id') is-invalid @enderror" required>
                                <option value="">-- اختر الحيوان --</option>
                                @foreach($animals as $animal)
                                <option value="{{ $animal->id }}"
                                        data-cat="{{ $animal->product?->mainCategory?->code }}"
                                        {{ old('animal_id') == $animal->id ? 'selected' : '' }}>
                                    {{ $animal->code }} — {{ $animal->product?->name }}
                                    ({{ $animal->product?->mainCategory?->name }})
                                </option>
                                @endforeach
                            </select>
                            @error('animal_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Share Type --}}
                        <div class="form-group">
                            <label class="form-label">نوع التقسيم <span class="text-danger">*</span></label>
                            <select name="share_type" class="form-control @error('share_type') is-invalid @enderror" required>
                                <option value="">-- اختر نوع التقسيم --</option>
                                @foreach($shareLabels as $val => $label)
                                <option value="{{ $val }}" {{ old('share_type') === $val ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                                @endforeach
                            </select>
                            @error('share_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Slaughter Day --}}
                        <div class="form-group">
                            <label class="form-label">يوم الذبح</label>
                            <input type="date" name="slaughter_day"
                                   class="form-control @error('slaughter_day') is-invalid @enderror"
                                   value="{{ old('slaughter_day') }}">
                            @error('slaughter_day')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Notes --}}
                        <div class="form-group">
                            <label class="form-label">ملاحظات</label>
                            <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('udhiya.groups.index') }}" class="btn btn-light">إلغاء</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="las la-save mr-1"></i> إنشاء المجموعة
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
