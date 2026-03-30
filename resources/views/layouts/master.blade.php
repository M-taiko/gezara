<!DOCTYPE html>
<html lang="ar" dir="rtl" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>برنامج الأضاحي 🐄 - الإدارة</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; }
        [x-cloak] { display: none !important; }
        /* Clean scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
    @stack('css')
</head>
<body class="bg-[#F8FAFC] text-slate-800 antialiased" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen overflow-hidden">
        {{-- Sidebar --}}
        @include('layouts.main-sidebar')
        
        {{-- Main Wrapper --}}
        <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden">
            {{-- Header --}}
            @include('layouts.main-header')
            
            {{-- Content --}}
            <main class="w-full">
                <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">
                    @yield('page-header')
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
    @stack('js')

    {{-- Toast Notifications --}}
    @if(session('toast_success') || session('toast_error') || session('toast_warning') || session('toast_info') || $errors->any())
    <div id="toast-container" style="position:fixed;bottom:24px;left:24px;z-index:99999;min-width:320px;max-width:420px;">
        @if(session('toast_success'))
        <div style="background:#16a34a;color:#fff;padding:14px 18px;border-radius:12px;margin-top:8px;box-shadow:0 8px 24px rgba(0,0,0,.18);display:flex;align-items:center;gap:10px;font-family:Cairo,sans-serif;font-weight:700;">
            ✅ {{ session('toast_success') }}
        </div>
        @endif
        @if(session('toast_error'))
        <div style="background:#dc2626;color:#fff;padding:14px 18px;border-radius:12px;margin-top:8px;box-shadow:0 8px 24px rgba(0,0,0,.18);display:flex;align-items:center;gap:10px;font-family:Cairo,sans-serif;font-weight:700;">
            ❌ {{ session('toast_error') }}
        </div>
        @endif
        @if(session('toast_warning'))
        <div style="background:#d97706;color:#fff;padding:14px 18px;border-radius:12px;margin-top:8px;box-shadow:0 8px 24px rgba(0,0,0,.18);display:flex;align-items:center;gap:10px;font-family:Cairo,sans-serif;font-weight:700;">
            ⚠️ {{ session('toast_warning') }}
        </div>
        @endif
        @if(session('toast_info'))
        <div style="background:#0891b2;color:#fff;padding:14px 18px;border-radius:12px;margin-top:8px;box-shadow:0 8px 24px rgba(0,0,0,.18);display:flex;align-items:center;gap:10px;font-family:Cairo,sans-serif;font-weight:700;">
            ℹ️ {{ session('toast_info') }}
        </div>
        @endif
        @if($errors->any())
        <div style="background:#dc2626;color:#fff;padding:14px 18px;border-radius:12px;margin-top:8px;box-shadow:0 8px 24px rgba(0,0,0,.18);font-family:Cairo,sans-serif;font-weight:700;">
            ❌ {{ $errors->first() }}
        </div>
        @endif
    </div>
    <script>
    setTimeout(function() {
        var c = document.getElementById('toast-container');
        if (c) { c.style.transition = 'opacity 0.5s'; c.style.opacity = '0'; setTimeout(function(){ if(c) c.remove(); }, 500); }
    }, 6000);
    </script>
    @endif
</body>
</html>