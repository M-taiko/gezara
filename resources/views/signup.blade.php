@extends('layouts.master2')
@section('css')
<link href="{{URL::asset('assets/plugins/sidemenu-responsive-tabs/css/sidemenu-responsive-tabs.css')}}" rel="stylesheet">
@endsection
@section('content')
	<div class="container-fluid">
		<div class="row no-gutter">
			<div class="col-md-6 col-lg-6 col-xl-7 d-none d-md-flex bg-primary-transparent">
				<div class="row wd-100p mx-auto text-center">
					<div class="col-md-12 col-lg-12 col-xl-12 my-auto mx-auto wd-100p">
						<?php $company = \App\Models\Company::getInstance(); ?>
						@if($company && $company->logo)
							<img src="{{ asset($company->logo) }}" class="my-auto ht-xl-80p wd-md-100p wd-xl-80p mx-auto" alt="{{ $company->name ?? 'الشعار' }}">
							@if($company->name)
								<h3 class="mt-4 text-white">{{ $company->name }}</h3>
							@endif
							@if($company->description)
								<p class="text-white mt-2">{{ $company->description }}</p>
							@endif
						@else
							<img src="{{URL::asset('assets/img/media/login.png')}}" class="my-auto ht-xl-80p wd-md-100p wd-xl-80p mx-auto" alt="logo">
						@endif
					</div>
				</div>
			</div>
			<div class="col-md-6 col-lg-6 col-xl-5 bg-white">
				<div class="login d-flex align-items-center py-2">
					<div class="container p-0">
						<div class="row">
							<div class="col-md-10 col-lg-10 col-xl-9 mx-auto">
								<div class="card-sigin">
									<div class="mb-5 d-flex">
										<a href="{{ url('/') }}"><img src="{{URL::asset('assets/img/brand/favicon.png')}}" class="sign-favicon ht-40" alt="logo"></a>
										<h1 class="main-logo1 ml-1 mr-0 my-auto tx-28">مشروع <span>الأضاحي</span></h1>
									</div>
									<div class="main-signup-header">
										<h2 class="text-primary">إنشاء حساب جديد</h2>
										<h5 class="font-weight-normal mb-4">يستغرق التسجيل دقيقة واحدة فقط.</h5>
										@if ($errors->any())
											<div class="alert alert-danger">
												<ul class="mb-0">
													@foreach ($errors->all() as $error)
														<li>{{ $error }}</li>
													@endforeach
												</ul>
											</div>
										@endif
										<form action="{{ route('register') }}" method="POST">
											@csrf
											<div class="form-group">
												<label>الاسم الكامل</label>
												<input class="form-control @error('name') is-invalid @enderror"
													name="name" value="{{ old('name') }}"
													placeholder="أدخل اسمك الكامل" type="text">
												@error('name')
													<span class="invalid-feedback d-block">{{ $message }}</span>
												@enderror
											</div>
											<div class="form-group">
												<label>البريد الإلكتروني</label>
												<input class="form-control @error('email') is-invalid @enderror"
													name="email" value="{{ old('email') }}"
													placeholder="أدخل بريدك الإلكتروني" type="email">
												@error('email')
													<span class="invalid-feedback d-block">{{ $message }}</span>
												@enderror
											</div>
											<div class="form-group">
												<label>كلمة المرور</label>
												<input class="form-control @error('password') is-invalid @enderror"
													name="password"
													placeholder="أدخل كلمة المرور (6 أحرف على الأقل)" type="password">
												@error('password')
													<span class="invalid-feedback d-block">{{ $message }}</span>
												@enderror
											</div>
											<div class="form-group">
												<label>تأكيد كلمة المرور</label>
												<input class="form-control"
													name="password_confirmation"
													placeholder="أعد إدخال كلمة المرور" type="password">
											</div>
											<button type="submit" class="btn btn-main-primary btn-block">إنشاء الحساب</button>
										</form>
										<div class="main-signup-footer mt-5">
											<p>لديك حساب بالفعل؟ <a href="{{ route('signin') }}">تسجيل الدخول</a></p>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
@section('js')
@endsection
