@extends('layouts.master')
@section('css')
<style>
	.user-search-result {
		cursor: pointer;
		transition: background-color 0.2s;
		margin-bottom: 10px;
	}
	.user-search-result:hover {
		background-color: #f5f5f5;
	}
	.user-search-result.dark-mode {
		background-color: transparent;
	}
	.user-search-result.dark-mode:hover {
		background-color: #333;
	}
	.user-info-card {
		border-left: 4px solid #007bff;
	}
	.user-info-card .user-avatar {
		width: 100px;
		height: 100px;
		border-radius: 50%;
		object-fit: cover;
	}
	.user-search-result .user-avatar {
		width: 50px;
		height: 50px;
		border-radius: 50%;
		object-fit: cover;
	}
	.status-indicator {
		width: 12px;
		height: 12px;
		border-radius: 50%;
		display: inline-block;
		margin-right: 5px;
	}
	.status-indicator.active {
		background-color: #00c853;
	}
	.status-indicator.inactive {
		background-color: #999;
	}
	.tab-content {
		min-height: 400px;
	}
	.search-results-container {
		max-height: 600px;
		overflow-y: auto;
	}
</style>
@endsection
@section('page-header')
<div class="breadcrumb-header justify-content-between">
	<div class="my-auto">
		<div class="d-flex">
			<h4 class="content-title mb-0 my-auto">الرسائل</h4>
			<span class="text-muted mt-1 tx-13 mr-2 mb-0">/ المحادثات والمستخدمون</span>
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
	<div class="col-lg-12">
		<div class="card">
			<div class="card-header d-flex justify-content-between align-items-center">
				<h6 class="card-title mb-0">الرسائل والمحادثات</h6>
			</div>
			<div class="card-body">
				<!-- Tabs Navigation -->
				<ul class="nav nav-tabs" role="tablist">
					<li class="nav-item">
						<a class="nav-link active" data-toggle="tab" href="#conversations" role="tab">
							<i class="fas fa-comments mr-2"></i>المحادثات
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" data-toggle="tab" href="#all-users" role="tab">
							<i class="fas fa-users mr-2"></i>جميع المستخدمين
						</a>
					</li>
				</ul>

				<!-- Tab Content -->
				<div class="tab-content">
					<!-- المحادثات Tab -->
					<div id="conversations" class="tab-pane fade show active" role="tabpanel">
						<div class="row mt-4">
							<div class="col-lg-6">
								<h6 class="mb-3">Your المحادثات</h6>
								<div class="list-group" id="conversationsList">
									@if($conversationUsers->count() > 0)
										@foreach($conversationUsers as $conversationUser)
											@php
												$lastMessage = \App\Models\Message::where(function ($q) use ($user, $conversationUser) {
													$q->where('sender_id', $user->id)->where('receiver_id', $conversationUser->id)
													  ->orWhere('sender_id', $conversationUser->id)->where('receiver_id', $user->id);
												})->latest()->first();
												$unreadCount = \App\Models\Message::where('sender_id', $conversationUser->id)
													->where('receiver_id', $user->id)
													->where('is_read', false)
													->count();
											@endphp
											<a href="{{ route('messages.show', $conversationUser) }}" class="list-group-item list-group-item-action py-3 border-bottom">
												<div class="d-flex align-items-center">
													<div class="position-relative mr-3">
														<img src="{{ $conversationUser->profile && $conversationUser->profile->avatar ? asset($conversationUser->profile->avatar) : URL::asset('assets/img/faces/6.jpg') }}" alt="avatar" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
														<span class="status-indicator @if($conversationUser->status === 'active') active @else inactive @endif" style="position: absolute; bottom: 0; right: 0;"></span>
													</div>
													<div class="flex-grow-1">
														<div class="d-flex justify-content-between align-items-center">
															<h6 class="mb-0 font-weight-semibold">{{ $conversationUser->name }}</h6>
															<small class="text-muted">{{ $lastMessage ? $lastMessage->created_at->diffForHumans() : 'لا رسائل' }}</small>
														</div>
														<p class="mb-0 text-muted small">{{ $lastMessage ? (strlen($lastMessage->content) > 40 ? substr($lastMessage->content, 0, 40) . '...' : $lastMessage->content) : 'ابدأ محادثة' }}</p>
													</div>
													@if($unreadCount > 0)
														<span class="badge badge-danger ml-2">{{ $unreadCount }}</span>
													@endif
												</div>
											</a>
										@endforeach
									@else
										<div class="alert alert-info">
											<i class="fas fa-info-circle mr-2"></i>
											No conversations yet. Use the "جميع المستخدمين" tab to start messaging!
										</div>
									@endif
								</div>
							</div>
						</div>
					</div>

					<!-- جميع المستخدمين Tab -->
					<div id="all-users" class="tab-pane fade" role="tabpanel">
						<div class="row mt-4">
							<div class="col-lg-8">
								<!-- Search Bar -->
								<div class="form-group mb-4">
									<label class="form-label">البحث عن مستخدمين</label>
									<input type="text" class="form-control form-control-lg" id="userSearchInput" placeholder="ابحث بالاسم أو البريد الإلكتروني...">
									<small class="form-text text-muted">ابدأ الكتابة للبحث عن مستخدمين</small>
								</div>

								<!-- Search Results -->
								<div id="searchResults" class="search-results-container">
									<div class="text-center text-muted py-5">
										<i class="fas fa-search fa-3x mb-3"></i>
										<p>انقر على حقل البحث لعرض المستخدمين</p>
									</div>
								</div>
							</div>

							<!-- User Info Panel -->
							<div class="col-lg-4">
								<div id="userInfoPanel">
									<div class="card bg-light text-center py-5">
										<i class="fas fa-user-circle fa-5x text-muted mb-3"></i>
										<p class="text-muted">اختر مستخدماً لعرض التفاصيل</p>
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
<script src="{{URL::asset('assets/plugins/lightslider/js/lightslider.min.js')}}"></script>
<script>
	$(function() {
		'use strict';

		// Load initial users when input is focused
		let usersLoaded = false;
		$('#userSearchInput').on('focus', function() {
			if (!usersLoaded) {
				loadInitialUsers();
				usersLoaded = true;
			}
		});

		// User Search Functionality
		let searchTimeout;
		$('#userSearchInput').on('keyup', function() {
			clearTimeout(searchTimeout);
			const query = $(this).val().trim();

			if (query.length < 1) {
				loadInitialUsers();
				return;
			}

			searchTimeout = setTimeout(function() {
				$.ajax({
					url: '/api/users/search',
					method: 'GET',
					data: { q: query },
					dataType: 'json',
					success: function(response) {
						displaySearchResults(response.users);
					},
					error: function() {
						$('#searchResults').html(
							'<div class="alert alert-danger">Error searching users</div>'
						);
					}
				});
			}, 300);
		});

		/**
		 * Load initial 10 users without search
		 */
		function loadInitialUsers() {
			$.ajax({
				url: '/api/users/search',
				method: 'GET',
				data: { q: '' },
				dataType: 'json',
				success: function(response) {
					if (response.users.length === 0) {
						$('#searchResults').html(
							'<div class="alert alert-info">' +
							'<i class="fas fa-info-circle mr-2"></i>No users available' +
							'</div>'
						);
					} else {
						displaySearchResults(response.users);
					}
				}
			});
		}

		/**
		 * Display search results
		 */
		function displaySearchResults(users) {
			if (users.length === 0) {
				$('#searchResults').html(
					'<div class="alert alert-warning">' +
					'<i class="fas fa-search mr-2"></i>No users found' +
					'</div>'
				);
				return;
			}

			let html = '';
			users.forEach(function(user) {
				const avatar = user.profile && user.profile.avatar
					? user.profile.avatar
					: '/assets/img/faces/6.jpg';

				html += '<div class="card user-search-result mb-3" data-user-id="' + user.id + '">' +
					'<div class="card-body">' +
					'<div class="d-flex align-items-center">' +
					'<img src="' + avatar + '" alt="avatar" class="rounded-circle user-avatar mr-3" style="object-fit: cover;">' +
					'<div class="flex-grow-1">' +
					'<h6 class="mb-1 font-weight-semibold">' + user.name + '</h6>' +
					'<p class="mb-0 text-muted small">' + user.email + '</p>' +
					'<div class="mt-2">' +
					'<span class="status-indicator ' + (user.status === 'active' ? 'active' : 'inactive') + '"></span>' +
					'<small class="text-muted">' + (user.status === 'active' ? 'نشط' : 'غير نشط') + '</small>' +
					'</div>' +
					'</div>' +
					'<a href="/messages/' + user.id + '" class="btn btn-sm btn-primary">' +
					'<i class="fas fa-paper-plane mr-1"></i>Message' +
					'</a>' +
					'</div>' +
					'</div>' +
					'</div>';
			});

			$('#searchResults').html(html);

			// Add click handler to user cards
			$('.user-search-result').on('click', function() {
				const userId = $(this).data('user-id');
				displayUserInfo(userId);
			});
		}

		/**
		 * Display user information in side panel
		 */
		function displayUserInfo(userId) {
			const users = [];
			// This will be populated from search results
			const user = $('[data-user-id="' + userId + '"]').data('user');

			$.ajax({
				url: '/api/users/' + userId,
				method: 'GET',
				dataType: 'json',
				success: function(response) {
					const user = response.user;
					const avatar = user.profile && user.profile.avatar
						? user.profile.avatar
						: '/assets/img/faces/6.jpg';

					const html = '<div class="card user-info-card">' +
						'<div class="card-body text-center">' +
						'<img src="' + avatar + '" alt="avatar" class="rounded-circle mb-3" style="width: 100px; height: 100px; object-fit: cover;">' +
						'<h5 class="card-title">' + user.name + '</h5>' +
						'<p class="text-muted">' + user.email + '</p>' +
						'<div class="mb-3">' +
						'<span class="status-indicator ' + (user.status === 'active' ? 'active' : 'inactive') + '" style="display: inline-block; width: 15px; height: 15px;"></span>' +
						'<small>' + (user.status === 'active' ? 'نشط' : 'غير نشط') + '</small>' +
						'</div>' +
						(user.profile && user.profile.job_title ? '<p class="mb-2"><small class="text-muted">' + user.profile.job_title + '</small></p>' : '') +
						(user.profile && user.profile.bio ? '<p class="mb-3"><small>' + user.profile.bio + '</small></p>' : '') +
						'<a href="/messages/' + user.id + '" class="btn btn-primary btn-block">' +
						'<i class="fas fa-paper-plane mr-2"></i>Start Messaging' +
						'</a>' +
						'</div>' +
						'</div>';

					$('#userInfoPanel').html(html);
				},
				error: function() {
					$('#userInfoPanel').html(
						'<div class="alert alert-danger">Error loading user information</div>'
					);
				}
			});
		}

		// Initialize scrollbar
		if (window.matchMedia('(min-width: 992px)').matches) {
			new PerfectScrollbar('#conversationsList', {
				suppressScrollX: true
			});
		}
	});
</script>
@endsection
