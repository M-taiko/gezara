@extends('layouts.master')
@section('css')
@endsection
@section('page-header')
<div class="breadcrumb-header justify-content-between">
<div class="my-auto">
<div class="d-flex">
<h4 class="content-title mb-0 my-auto">الإعدادات</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ إعدادات الشركة</span>
</div>
</div>
<div class="d-flex my-xl-auto right-content">
<div class="pr-1 mb-3 mb-xl-0">
<a href="{{ url('/') }}" class="btn btn-secondary btn-icon ml-2"><i class="mdi mdi-arrow-right"></i> رجوع</a>
</div>
</div>
</div>
@endsection
@section('content')
<div class="row row-sm">
<div class="col-lg-8 mx-auto">
<div class="card">
<div class="card-body">
<h5 class="card-title mb-4">إعدادات الشركة</h5>
@if ($errors->any())
<div class="alert alert-danger">
<ul class="mb-0">
@foreach ($errors->all() as $error)
<li>{{ $error }}</li>
@endforeach
</ul>
</div>
@endif
@if (session('success'))
<div class="alert alert-success">
{{ session('success') }}
</div>
@endif
<form action="{{ route('company.update') }}" method="POST" enctype="multipart/form-data">
@csrf
@method('PUT')
<!-- Company Information Section -->
<div class="row">
<div class="col-md-6">
<div class="form-group mg-b-20">
<label for="name">اسم الشركة *</label>
<input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $company->name ?? '') }}" required>
@error('name')
<span class="invalid-feedback">{{ $message }}</span>
@enderror
</div>
</div>
<div class="col-md-6">
<div class="form-group mg-b-20">
<label for="email">البريد الإلكتروني</label>
<input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $company->email ?? '') }}">
@error('email')
<span class="invalid-feedback">{{ $message }}</span>
@enderror
</div>
</div>
</div>

<div class="row">
<div class="col-md-6">
<div class="form-group mg-b-20">
<label for="phone">رقم الهاتف</label>
<input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $company->phone ?? '') }}">
@error('phone')
<span class="invalid-feedback">{{ $message }}</span>
@enderror
</div>
</div>
<div class="col-md-6">
<div class="form-group mg-b-20">
<label for="website">الموقع الإلكتروني</label>
<input type="url" class="form-control @error('website') is-invalid @enderror" id="website" name="website" value="{{ old('website', $company->website ?? '') }}">
@error('website')
<span class="invalid-feedback">{{ $message }}</span>
@enderror
</div>
</div>
</div>

<div class="form-group mg-b-20">
<label for="address">العنوان</label>
<input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address', $company->address ?? '') }}">
@error('address')
<span class="invalid-feedback">{{ $message }}</span>
@enderror
</div>

<div class="form-group mg-b-20">
<label for="description">الوصف</label>
<textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $company->description ?? '') }}</textarea>
@error('description')
<span class="invalid-feedback">{{ $message }}</span>
@enderror
</div>

<hr class="my-4">

<!-- Logos Section -->
<h6 class="card-subtitle mb-4 font-weight-bold">شعارات الشركة</h6>

<div class="form-group mg-b-20">
<label>الشعار الرئيسي</label>
<div class="card mb-3">
<div class="card-body text-center">
@if($company && $company->logo)
<img id="logoPreview" src="{{ asset($company->logo) }}" alt="Logo" style="max-height: 120px; max-width: 100%;" class="mb-3">
@else
<div id="logoPlaceholder" class="p-5">
<i class="fas fa-image fa-3x text-muted"></i>
<p class="text-muted mt-3">لم يتم رفع شعار بعد</p>
</div>
<img id="logoPreview" src="#" alt="Logo" style="max-height: 120px; max-width: 100%; display: none;" class="mb-3">
@endif
</div>
</div>
<input type="file" class="form-control @error('logo') is-invalid @enderror" name="logo" id="logoInput" accept="image/*">
<small class="form-text text-muted">الحد الأقصى: 2 ميجابايت (JPEG, PNG, JPG, GIF)</small>
@error('logo')
<span class="invalid-feedback">{{ $message }}</span>
@enderror
</div>

<div class="form-group mg-b-20">
<label>شعار الشريط الجانبي (موسّع)</label>
<div class="card mb-3">
<div class="card-body text-center">
@if($company && $company->sidebar_logo_expanded)
<img id="sidebarExpandedPreview" src="{{ asset($company->sidebar_logo_expanded) }}" alt="Sidebar Logo" style="max-height: 100px; max-width: 100%;" class="mb-3">
@else
<div id="sidebarExpandedPlaceholder" class="p-5">
<i class="fas fa-image fa-3x text-muted"></i>
<p class="text-muted mt-3">لم يتم رفع شعار بعد</p>
</div>
<img id="sidebarExpandedPreview" src="#" alt="Sidebar Logo" style="max-height: 100px; max-width: 100%; display: none;" class="mb-3">
@endif
</div>
</div>
<input type="file" class="form-control @error('sidebar_logo_expanded') is-invalid @enderror" name="sidebar_logo_expanded" id="sidebarExpandedInput" accept="image/*">
<small class="form-text text-muted">يُستخدم عند توسيع الشريط الجانبي (بحد أقصى 2 ميجابايت)</small>
@error('sidebar_logo_expanded')
<span class="invalid-feedback">{{ $message }}</span>
@enderror
</div>

<div class="form-group mg-b-20">
<label>شعار الشريط الجانبي (مطوي)</label>
<div class="card mb-3">
<div class="card-body text-center">
@if($company && $company->sidebar_logo_collapsed)
<img id="sidebarCollapsedPreview" src="{{ asset($company->sidebar_logo_collapsed) }}" alt="Sidebar Logo" style="max-height: 100px; max-width: 100%;" class="mb-3">
@else
<div id="sidebarCollapsedPlaceholder" class="p-5">
<i class="fas fa-image fa-3x text-muted"></i>
<p class="text-muted mt-3">لم يتم رفع شعار بعد</p>
</div>
<img id="sidebarCollapsedPreview" src="#" alt="Sidebar Logo" style="max-height: 100px; max-width: 100%; display: none;" class="mb-3">
@endif
</div>
</div>
<input type="file" class="form-control @error('sidebar_logo_collapsed') is-invalid @enderror" name="sidebar_logo_collapsed" id="sidebarCollapsedInput" accept="image/*">
<small class="form-text text-muted">يُستخدم عند طي الشريط الجانبي (بحد أقصى 2 ميجابايت)</small>
@error('sidebar_logo_collapsed')
<span class="invalid-feedback">{{ $message }}</span>
@enderror
</div>

<hr class="my-4">

<div class="form-group">
<button type="submit" class="btn btn-primary"><i class="fas fa-save mr-2"></i>حفظ التغييرات</button>
<a href="{{ url('/') }}" class="btn btn-secondary ml-2"><i class="fas fa-times mr-2"></i>إلغاء</a>
</div>
</form>
</div>
</div>
</div>
</div>
@endsection
@section('js')
<script>
document.getElementById('logoInput').addEventListener('change', function(e) {
const file = e.target.files[0];
if (file) {
const reader = new FileReader();
reader.onload = function(event) {
document.getElementById('logoPreview').src = event.target.result;
document.getElementById('logoPreview').style.display = 'block';
document.getElementById('logoPlaceholder').style.display = 'none';
};
reader.readAsDataURL(file);
}
});
document.getElementById('sidebarExpandedInput').addEventListener('change', function(e) {
const file = e.target.files[0];
if (file) {
const reader = new FileReader();
reader.onload = function(event) {
document.getElementById('sidebarExpandedPreview').src = event.target.result;
document.getElementById('sidebarExpandedPreview').style.display = 'block';
document.getElementById('sidebarExpandedPlaceholder').style.display = 'none';
};
reader.readAsDataURL(file);
}
});
document.getElementById('sidebarCollapsedInput').addEventListener('change', function(e) {
const file = e.target.files[0];
if (file) {
const reader = new FileReader();
reader.onload = function(event) {
document.getElementById('sidebarCollapsedPreview').src = event.target.result;
document.getElementById('sidebarCollapsedPreview').style.display = 'block';
document.getElementById('sidebarCollapsedPlaceholder').style.display = 'none';
};
reader.readAsDataURL(file);
}
});
</script>
@endsection
