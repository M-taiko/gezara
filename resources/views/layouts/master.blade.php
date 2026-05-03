<!DOCTYPE html>
<html lang="ar" dir="rtl" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <style>
        /* Select2 RTL + Tailwind integration */
        .select2-container { width: 100% !important; }
        .select2-container--default .select2-selection--single {
            height: 46px; border-radius: 0.75rem; border-color: #e2e8f0;
            background-color: #f8fafc; display: flex; align-items: center; padding: 0 16px;
            font-family: 'Cairo', sans-serif; font-size: 0.875rem; font-weight: 600; color: #1e293b;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            padding: 0; line-height: normal; color: #1e293b; font-weight: 600;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 44px; left: 8px; right: auto;
        }
        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,0.15); background: #fff;
        }
        .select2-dropdown { border-radius: 1rem; border-color: #e2e8f0; box-shadow: 0 10px 30px rgba(0,0,0,.12); font-family: 'Cairo', sans-serif; }
        .select2-container--default .select2-results__option--highlighted { background-color: #eef2ff; color: #4338ca; }
        .select2-search--dropdown .select2-search__field { border-radius: 0.5rem; border-color: #e2e8f0; font-family: 'Cairo', sans-serif; padding: 6px 10px; }
        .select2-container--default .select2-selection--single .select2-selection__placeholder { color: #94a3b8; }
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
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    @stack('js')

    {{-- ══ Global Attachment Lightbox ══ --}}
    <div id="gz-lightbox" class="hidden fixed inset-0 z-[9999] flex items-center justify-center bg-black/80 backdrop-blur-sm p-4"
         onclick="if(event.target===this)gzLightboxClose()">
        <div class="relative bg-white rounded-2xl shadow-2xl flex flex-col overflow-hidden"
             style="max-width:92vw;max-height:90vh;min-width:320px;">
            {{-- Header --}}
            <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100 flex-shrink-0">
                <span id="gz-lb-title" class="text-sm font-bold text-slate-700 truncate max-w-xs"></span>
                <div class="flex items-center gap-2 mr-3">
                    <a id="gz-lb-download" href="#" download target="_blank"
                       class="text-xs font-bold px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white transition-colors">
                        ⬇ تحميل
                    </a>
                    <button onclick="gzLightboxClose()"
                            class="w-7 h-7 rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-700 hover:text-white transition-colors text-lg font-bold leading-none flex items-center justify-center">
                        ✕
                    </button>
                </div>
            </div>
            {{-- Body --}}
            <div id="gz-lb-body" class="flex-1 overflow-auto flex items-center justify-center bg-slate-50 p-2"
                 style="min-height:200px;">
                {{-- filled by JS --}}
            </div>
        </div>
    </div>
    <script>
    function gzLightboxOpen(url, filename) {
        const ext   = filename.split('.').pop().toLowerCase();
        const body  = document.getElementById('gz-lb-body');
        const title = document.getElementById('gz-lb-title');
        const dl    = document.getElementById('gz-lb-download');

        title.textContent  = filename;
        dl.href            = url;
        dl.setAttribute('download', filename);
        body.innerHTML     = '';

        if (['jpg','jpeg','png','gif','webp','svg'].includes(ext)) {
            const img = document.createElement('img');
            img.src   = url;
            img.alt   = filename;
            img.style.cssText = 'max-width:100%;max-height:75vh;border-radius:8px;object-fit:contain;';
            body.appendChild(img);
        } else if (ext === 'pdf') {
            const iframe = document.createElement('iframe');
            iframe.src   = url + '#toolbar=1&navpanes=0';
            iframe.style.cssText = 'width:80vw;height:75vh;border:none;border-radius:8px;';
            body.appendChild(iframe);
        } else {
            body.innerHTML = `
                <div class="text-center p-8">
                    <div class="text-5xl mb-4">📄</div>
                    <p class="text-sm font-semibold text-slate-600 mb-4">${filename}</p>
                    <a href="${url}" target="_blank" download="${filename}"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 text-white text-sm font-bold hover:bg-indigo-700 transition-colors">
                        ⬇ تحميل الملف
                    </a>
                </div>`;
        }

        document.getElementById('gz-lightbox').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function gzLightboxClose() {
        document.getElementById('gz-lightbox').classList.add('hidden');
        document.getElementById('gz-lb-body').innerHTML = '';
        document.body.style.overflow = '';
    }
    document.addEventListener('keydown', e => { if (e.key === 'Escape') gzLightboxClose(); });
    </script>

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