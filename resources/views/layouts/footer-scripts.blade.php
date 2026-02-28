{{-- ============================================================ --}}
{{-- Toast Notifications                                          --}}
{{-- ============================================================ --}}
@if(session('toast_success') || session('toast_error') || session('toast_warning') || session('toast_info'))
<div id="toast-container" style="position:fixed;bottom:20px;left:20px;z-index:9999;min-width:300px;">
    @if(session('toast_success'))
    <div class="toast-msg toast-success" style="background:#28a745;color:#fff;padding:14px 18px;border-radius:6px;margin-top:8px;box-shadow:0 4px 12px rgba(0,0,0,.2);display:flex;align-items:center;gap:10px;">
        <i class="fas fa-check-circle"></i> {{ session('toast_success') }}
    </div>
    @endif
    @if(session('toast_error'))
    <div class="toast-msg toast-error" style="background:#dc3545;color:#fff;padding:14px 18px;border-radius:6px;margin-top:8px;box-shadow:0 4px 12px rgba(0,0,0,.2);display:flex;align-items:center;gap:10px;">
        <i class="fas fa-times-circle"></i> {{ session('toast_error') }}
    </div>
    @endif
    @if(session('toast_warning'))
    <div class="toast-msg toast-warning" style="background:#ffc107;color:#212529;padding:14px 18px;border-radius:6px;margin-top:8px;box-shadow:0 4px 12px rgba(0,0,0,.2);display:flex;align-items:center;gap:10px;">
        <i class="fas fa-exclamation-triangle"></i> {{ session('toast_warning') }}
    </div>
    @endif
    @if(session('toast_info'))
    <div class="toast-msg toast-info" style="background:#17a2b8;color:#fff;padding:14px 18px;border-radius:6px;margin-top:8px;box-shadow:0 4px 12px rgba(0,0,0,.2);display:flex;align-items:center;gap:10px;">
        <i class="fas fa-info-circle"></i> {{ session('toast_info') }}
    </div>
    @endif
</div>
<script>
setTimeout(function() {
    var c = document.getElementById('toast-container');
    if (c) { c.style.transition = 'opacity 0.5s'; c.style.opacity = '0'; setTimeout(function(){ c.remove(); }, 500); }
}, 4000);
</script>
@endif
{{-- ============================================================ --}}

<!-- Back-to-top -->
<a href="#top" id="back-to-top"><i class="las la-angle-double-up"></i></a>
<!-- JQuery min js -->
<script src="{{URL::asset('assets/plugins/jquery/jquery.min.js')}}"></script>
<!-- Bootstrap Bundle js -->
<script src="{{URL::asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<!-- Ionicons js -->
<script src="{{URL::asset('assets/plugins/ionicons/ionicons.js')}}"></script>
<!-- Moment js -->
<script src="{{URL::asset('assets/plugins/moment/moment.js')}}"></script>

<!-- Rating js-->
<script src="{{URL::asset('assets/plugins/rating/jquery.rating-stars.js')}}"></script>
<script src="{{URL::asset('assets/plugins/rating/jquery.barrating.js')}}"></script>

<!--Internal  Perfect-scrollbar js -->
<script src="{{URL::asset('assets/plugins/perfect-scrollbar/perfect-scrollbar.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/perfect-scrollbar/p-scroll.js')}}"></script>
<!--Internal Sparkline js -->
<script src="{{URL::asset('assets/plugins/jquery-sparkline/jquery.sparkline.min.js')}}"></script>
<!-- Custom Scroll bar Js-->
<script src="{{URL::asset('assets/plugins/mscrollbar/jquery.mCustomScrollbar.concat.min.js')}}"></script>
<!-- right-sidebar js -->
<script src="{{URL::asset('assets/plugins/sidebar/sidebar-rtl.js')}}"></script>
<script src="{{URL::asset('assets/plugins/sidebar/sidebar-custom.js')}}"></script>
<!-- Eva-icons js -->
<script src="{{URL::asset('assets/js/eva-icons.min.js')}}"></script>
<!-- Messages Notifications js -->
@auth
<script src="{{URL::asset('assets/js/messages.js')}}"></script>
@endauth
@yield('js')
<!-- Sticky js -->
<script src="{{URL::asset('assets/js/sticky.js')}}"></script>
<!-- custom js -->
<script src="{{URL::asset('assets/js/custom.js')}}"></script><!-- Left-menu js-->
<script src="{{URL::asset('assets/plugins/side-menu/sidemenu.js')}}"></script>

<!-- Dark Mode Toggle Handler -->
<script>
	document.addEventListener('DOMContentLoaded', function() {
		// Initialize icon visibility on page load
		function initializeDarkModeIcon() {
			const isDarkMode = document.body.classList.contains('dark-mode');
			document.querySelectorAll('.dark-mode-toggle').forEach(button => {
				if (isDarkMode) {
					button.querySelector('.light-layout').style.display = 'none';
					button.querySelector('.dark-layout').style.display = 'block';
				} else {
					button.querySelector('.light-layout').style.display = 'block';
					button.querySelector('.dark-layout').style.display = 'none';
				}
			});
		}

		// Handle Dark Mode Toggle
		document.querySelectorAll('.dark-mode-toggle').forEach(button => {
			button.addEventListener('click', function(e) {
				e.preventDefault();
				const mode = this.dataset.mode;
				const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ||
					document.querySelector('input[name="_token"]')?.value;

				fetch('/api/dark-mode/toggle', {
					method: 'POST',
					headers: {
						'X-CSRF-TOKEN': csrfToken,
						'Content-Type': 'application/json',
					},
					body: JSON.stringify({ mode: mode })
				})
				.then(response => response.json())
				.then(data => {
					if (data.success) {
						// Update body class immediately
						if (mode === 'dark') {
							document.body.classList.add('dark-mode');
							// Switch icons
							document.querySelectorAll('.dark-mode-toggle .light-layout').forEach(el => el.style.display = 'none');
							document.querySelectorAll('.dark-mode-toggle .dark-layout').forEach(el => el.style.display = 'block');
							// Update data-mode for next toggle
							document.querySelectorAll('.dark-mode-toggle').forEach(btn => btn.dataset.mode = 'light');
						} else {
							document.body.classList.remove('dark-mode');
							// Switch icons
							document.querySelectorAll('.dark-mode-toggle .light-layout').forEach(el => el.style.display = 'block');
							document.querySelectorAll('.dark-mode-toggle .dark-layout').forEach(el => el.style.display = 'none');
							// Update data-mode for next toggle
							document.querySelectorAll('.dark-mode-toggle').forEach(btn => btn.dataset.mode = 'dark');
						}
						// Reload after a moment for full effect
						setTimeout(() => location.reload(), 800);
					}
				})
				.catch(error => console.error('Error:', error));
			});
		});

		// Initialize icons
		initializeDarkModeIcon();
	});
</script>

<!-- Full Screen Mode Persistence -->
<script>
	// Function to apply fullscreen state
	function applyFullscreenState() {
		const isFullscreen = localStorage.getItem('isFullscreen') === 'true';
		const body = document.body;
		const fullscreenBtn = document.querySelector('.full-screen-link');

		if (isFullscreen) {
			body.classList.add('side-header');
			body.classList.add('compact-navbar');
			if (fullscreenBtn) {
				fullscreenBtn.classList.add('active');
			}
		} else {
			body.classList.remove('side-header');
			body.classList.remove('compact-navbar');
			if (fullscreenBtn) {
				fullscreenBtn.classList.remove('active');
			}
		}
	}

	// Apply fullscreen state on page load
	document.addEventListener('DOMContentLoaded', function() {
		applyFullscreenState();

		// Handle fullscreen button click
		const fullscreenBtn = document.querySelector('.full-screen-link');
		if (fullscreenBtn) {
			fullscreenBtn.addEventListener('click', function(e) {
				e.preventDefault();
				const body = document.body;
				const isCurrentlyFullscreen = body.classList.contains('side-header');

				if (isCurrentlyFullscreen) {
					body.classList.remove('side-header');
					body.classList.remove('compact-navbar');
					localStorage.setItem('isFullscreen', 'false');
					this.classList.remove('active');
				} else {
					body.classList.add('side-header');
					body.classList.add('compact-navbar');
					localStorage.setItem('isFullscreen', 'true');
					this.classList.add('active');
				}
			});
		}
	});
</script>