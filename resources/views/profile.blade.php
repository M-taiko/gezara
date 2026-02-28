@extends('layouts.master')
@section('css')
@endsection
@section('page-header')
	<!-- breadcrumb -->
	<div class="breadcrumb-header justify-content-between">
		<div class="my-auto">
			<div class="d-flex">
				<h4 class="content-title mb-0 my-auto">الحساب الشخصي</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ ملفي الشخصي</span>
			</div>
		</div>
		<div class="d-flex my-xl-auto right-content">
			<div class="pr-1 mb-3 mb-xl-0">
				<a href="{{ route('profile.edit') }}" class="btn btn-primary ml-2"><i class="mdi mdi-pencil"></i> تعديل الملف</a>
			</div>
			<div class="pr-1 mb-3 mb-xl-0">
				<form action="{{ route('logout') }}" method="POST" style="display:inline;">
					@csrf
					<button type="submit" class="btn btn-danger ml-2"><i class="mdi mdi-logout"></i> تسجيل الخروج</button>
				</form>
			</div>
		</div>
	</div>
	<!-- breadcrumb -->
@endsection
@section('content')
	<!-- row -->
	<div class="row row-sm">
		<div class="col-lg-4">
			<div class="card mg-b-20">
				<div class="card-body">
					<div class="pl-0">
						<div class="main-profile-overview">
							<div class="main-img-user profile-user">
								@if($profile && $profile->avatar)
									<img alt="" src="{{ asset($profile->avatar) }}"><a class="fas fa-camera profile-edit" href="JavaScript:void(0);"></a>
								@else
									<img alt="" src="{{URL::asset('assets/img/faces/6.jpg')}}"><a class="fas fa-camera profile-edit" href="JavaScript:void(0);"></a>
								@endif
							</div>
							<div class="d-flex justify-content-between mg-b-20">
								<div>
									<h5 class="main-profile-name">{{ $user->name }}</h5>
									<p class="main-profile-name-text">{{ $profile && $profile->job_title ? $profile->job_title : 'لم يتم تحديد المسمى الوظيفي' }}</p>
								</div>
							</div>
							<h6>نبذة شخصية</h6>
							<div class="main-profile-bio">
								{{ $profile && $profile->bio ? $profile->bio : 'لم تتم إضافة نبذة شخصية بعد.' }}
							</div><!-- main-profile-bio -->
							<div class="row">
								<div class="col-md-4 col mb20">
									<h5>البريد الإلكتروني</h5>
									<h6 class="text-small text-muted mb-0">{{ $user->email }}</h6>
								</div>
								<div class="col-md-4 col mb20">
									<h5>رقم الهاتف</h5>
									<h6 class="text-small text-muted mb-0">{{ $profile && $profile->phone ? $profile->phone : 'غير محدد' }}</h6>
								</div>
								<div class="col-md-4 col mb20">
									<h5>عضو منذ</h5>
									<h6 class="text-small text-muted mb-0">{{ $user->created_at->format('M d, Y') }}</h6>
								</div>
							</div>
							<hr class="mg-y-30">
							<h6>الموقع الجغرافي</h6>
							<div class="main-profile-social-list">
								<div class="media">
									<div class="media-icon bg-primary-transparent text-primary">
										<i class="icon ion-md-home"></i>
									</div>
									<div class="media-body">
										<span>العنوان</span> <a href="">{{ $profile && $profile->address ? $profile->address : 'غير محدد' }}</a>
									</div>
								</div>
								<div class="media">
									<div class="media-icon bg-success-transparent text-success">
										<i class="icon ion-md-locate"></i>
									</div>
									<div class="media-body">
										<span>المدينة</span> <a href="">{{ $profile && $profile->city ? $profile->city : 'غير محدد' }}</a>
									</div>
								</div>
								<div class="media">
									<div class="media-icon bg-info-transparent text-info">
										<i class="icon ion-md-globe"></i>
									</div>
									<div class="media-body">
										<span>الدولة</span> <a href="">{{ $profile && $profile->country ? $profile->country : 'غير محدد' }}</a>
									</div>
								</div>
							</div>
						</div><!-- main-profile-overview -->
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-8">
			<div class="card">
				<div class="card-body">
					<div class="tabs-menu">
						<!-- Tabs -->
						<ul class="nav nav-tabs profile navtab-custom panel-tabs">
							<li class="active">
								<a href="#about" data-toggle="tab" aria-expanded="true"> <span class="visible-xs"><i class="las la-user-circle tx-16 mr-1"></i></span> <span class="hidden-xs">عن الحساب</span> </a>
							</li>
							<li class="">
								<a href="#settings" data-toggle="tab" aria-expanded="false"> <span class="visible-xs"><i class="las la-cog tx-16 mr-1"></i></span> <span class="hidden-xs">الإعدادات</span> </a>
							</li>
						</ul>
					</div>
					<div class="tab-content border-left border-bottom border-right border-top-0 p-4">
						<div class="tab-pane active" id="about">
							<h4 class="tx-15 text-uppercase mb-3">معلومات الملف الشخصي</h4>
							<div class="row mg-b-20">
								<div class="col-md-6">
									<div class="form-group">
										<label>الاسم الكامل</label>
										<input type="text" class="form-control" value="{{ $user->name }}" disabled>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label>البريد الإلكتروني</label>
										<input type="email" class="form-control" value="{{ $user->email }}" disabled>
									</div>
								</div>
							</div>
							<div class="row mg-b-20">
								<div class="col-md-6">
									<div class="form-group">
										<label>رقم الهاتف</label>
										<input type="text" class="form-control" value="{{ $profile && $profile->phone ? $profile->phone : '' }}" disabled>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label>المسمى الوظيفي</label>
										<input type="text" class="form-control" value="{{ $profile && $profile->job_title ? $profile->job_title : '' }}" disabled>
									</div>
								</div>
							</div>
							<div class="row mg-b-20">
								<div class="col-md-6">
									<div class="form-group">
										<label>City</label>
										<input type="text" class="form-control" value="{{ $profile && $profile->city ? $profile->city : '' }}" disabled>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label>Country</label>
										<input type="text" class="form-control" value="{{ $profile && $profile->country ? $profile->country : '' }}" disabled>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label>Bio</label>
								<textarea class="form-control" rows="4" disabled>{{ $profile && $profile->bio ? $profile->bio : '' }}</textarea>
							</div>
						</div>
						<div class="tab-pane" id="settings">
							<h4 class="tx-15 text-uppercase mb-3">Account Settings</h4>
							<div class="form-group mg-b-20">
								<label>Member Since</label>
								<input type="text" class="form-control" value="{{ $user->created_at->format('F d, Y') }}" disabled>
							</div>
							<div class="form-group mg-b-20">
								<label>Last Updated</label>
								<input type="text" class="form-control" value="{{ $user->updated_at->format('F d, Y H:i') }}" disabled>
							</div>
							<div class="form-group">
								<a href="{{ route('profile.edit') }}" class="btn btn-primary">Edit Profile</a>
								<form action="{{ route('logout') }}" method="POST" style="display:inline;">
									@csrf
									<button type="submit" class="btn btn-danger">Logout</button>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- row closed -->
@endsection
@section('js')
@endsection
