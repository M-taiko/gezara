import os
import re

base_path = r'c:\xampp\htdocs\gzara\resources\views'
udhiya_path = os.path.join(base_path, 'udhiya')

layout_master = """<!DOCTYPE html>
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
</body>
</html>"""

layout_sidebar = """
<!-- Sidebar backdrop (mobile only) -->
<div class="fixed inset-0 bg-slate-900 bg-opacity-30 z-40 lg:hidden lg:z-auto transition-opacity duration-200" 
     :class="sidebarOpen ? 'opacity-100' : 'opacity-0 pointer-events-none'" 
     aria-hidden="true" x-cloak></div>

<!-- Sidebar -->
<div id="sidebar" class="flex flex-col absolute z-40 start-0 top-0 lg:static lg:start-auto lg:top-auto lg:translate-x-0 h-screen overflow-y-scroll lg:overflow-y-auto no-scrollbar w-64 lg:w-72 bg-white border-e border-slate-200 transition-all duration-200 ease-in-out shadow-sm" 
     :class="sidebarOpen ? 'translate-x-0' : 'translate-x-full lg:translate-x-0'" 
     @click.outside="sidebarOpen = false" 
     @keydown.escape.window="sidebarOpen = false">

    <!-- Sidebar header -->
    <div class="flex justify-between items-center pe-3 sm:pe-6 lg:pe-8 py-5 px-4 border-b border-slate-100">
        <button class="lg:hidden text-slate-500 hover:text-slate-400" @click.stop="sidebarOpen = !sidebarOpen" aria-controls="sidebar" :aria-expanded="sidebarOpen">
            <span class="sr-only">إغلاق</span>
            <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M10.7 18.7l1.4-1.4L7.8 13H20v-2H7.8l4.3-4.3-1.4-1.4L4 12z" />
            </svg>
        </button>
        <a class="flex items-center gap-2 text-xl font-bold bg-clip-text text-transparent bg-gradient-to-l from-indigo-600 to-blue-500" href="{{ route('udhiya.dashboard') }}">
            <span>برنامج الأضاحي</span>
            <span>🐄</span>
        </a>
    </div>

    <!-- Links -->
    <div class="space-y-8 flex-1 py-6">
        <div class="px-3">
            <ul class="space-y-1">
                @php
                    $links = [
                        ['route' => 'udhiya.dashboard', 'icon' => '📊', 'label' => 'الرئيسية', 'pattern' => 'udhiya.dashboard'],
                        ['route' => 'udhiya.animals.index', 'icon' => '🐄', 'label' => 'الحيوانات', 'pattern' => 'udhiya.animals.*'],
                        ['route' => 'udhiya.customers.index', 'icon' => '👥', 'label' => 'العملاء', 'pattern' => 'udhiya.customers.*'],
                        ['route' => 'udhiya.contracts.index', 'icon' => '📑', 'label' => 'الصكوك', 'pattern' => 'udhiya.contracts.*'],
                        ['route' => 'udhiya.purchases.index', 'icon' => '🛒', 'label' => 'المشتريات', 'pattern' => 'udhiya.purchases.*'],
                        ['route' => 'udhiya.suppliers.index', 'icon' => '🚚', 'label' => 'الموردين', 'pattern' => 'udhiya.suppliers.*'],
                        ['route' => 'udhiya.groups.index', 'icon' => '🔪', 'label' => 'الذبح', 'pattern' => 'udhiya.groups.*'],
                        ['route' => 'udhiya.reports.index', 'icon' => '📈', 'label' => 'التقارير', 'pattern' => 'udhiya.reports.*'],
                    ];
                @endphp
                
                @foreach($links as $link)
                <li>
                    <a href="{{ route($link['route']) }}" class="flex items-center space-x-3 space-x-reverse px-4 py-2.5 rounded-xl text-slate-700 hover:bg-slate-50 transition-colors {{ request()->routeIs($link['pattern']) ? 'bg-indigo-50/80 text-indigo-600 font-semibold shadow-sm ring-1 ring-indigo-100/50' : '' }}">
                        <span class="text-xl shrink-0 drop-shadow-sm">{{ $link['icon'] }}</span>
                        <span>{{ $link['label'] }}</span>
                    </a>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
"""

layout_header = """
<header class="sticky top-0 bg-white/80 backdrop-blur-md border-b border-slate-200 z-30 shadow-sm">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 -mb-px">
            {{-- Mobile menu button --}}
            <div class="flex">
                <button class="text-slate-500 hover:text-slate-600 lg:hidden p-2 rounded-lg hover:bg-slate-100 transition" @click.stop="sidebarOpen = !sidebarOpen" aria-controls="sidebar" :aria-expanded="sidebarOpen">
                    <span class="sr-only">فتح القائمة</span>
                    <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <rect x="4" y="5" width="16" height="2" rx="1" />
                        <rect x="4" y="11" width="16" height="2" rx="1" />
                        <rect x="4" y="17" width="16" height="2" rx="1" />
                    </svg>
                </button>
            </div>
            
            {{-- Header Right --}}
            <div class="flex items-center space-x-3 space-x-reverse">
                <div class="relative" x-data="{ open: false }">
                    <button class="inline-flex justify-center items-center group p-1.5 rounded-full hover:bg-slate-50 transition" aria-haspopup="true" @click.prevent="open = !open" :aria-expanded="open">
                        <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold shadow-sm">
                            م
                        </div>
                        <div class="hidden md:flex items-center truncate">
                            <span class="truncate ms-2 text-sm font-semibold text-slate-700 group-hover:text-slate-900 transition">المدير العام</span>
                            <svg class="w-3 h-3 shrink-0 ms-1 fill-current text-slate-400 group-hover:text-slate-600" viewBox="0 0 12 12">
                                <path d="M5.9 11.4L.5 6l1.4-1.4 4 4 4-4L11.3 6z" />
                            </svg>
                        </div>
                    </button>
                    <div class="origin-top-left z-10 absolute top-full start-0 min-w-44 bg-white border border-slate-200 py-1.5 rounded-xl shadow-lg overflow-hidden mt-1" 
                         @click.outside="open = false" 
                         @keydown.escape.window="open = false" 
                         x-show="open" 
                         x-transition:enter="transition ease-out duration-200 transform" 
                         x-transition:enter-start="opacity-0 -translate-y-2" 
                         x-transition:enter-end="opacity-100 translate-y-0" 
                         x-transition:leave="transition ease-out duration-200" 
                         x-transition:leave-start="opacity-100" 
                         x-transition:leave-end="opacity-0" x-cloak>
                        <div class="pt-2 pb-2 px-4 mb-1 border-b border-slate-100">
                            <div class="font-bold text-slate-800">المدير العام</div>
                            <div class="text-xs text-slate-500">administrator</div>
                        </div>
                        <ul>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="w-full text-start font-medium text-sm text-rose-500 hover:text-rose-600 hover:bg-rose-50 transition-colors flex items-center py-2 px-4" type="submit">
                                        تسجيل الخروج
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
"""

# Overwrite Layouts
layouts_dir = os.path.join(base_path, 'layouts')
os.makedirs(layouts_dir, exist_ok=True)
with open(os.path.join(layouts_dir, 'master.blade.php'), 'w', encoding='utf-8') as f:
    f.write(layout_master)
with open(os.path.join(layouts_dir, 'main-sidebar.blade.php'), 'w', encoding='utf-8') as f:
    f.write(layout_sidebar)
with open(os.path.join(layouts_dir, 'main-header.blade.php'), 'w', encoding='utf-8') as f:
    f.write(layout_header)

print("Layouts rewritten.")

replacements = [
    # Basic Layout wrappers
    (r'<div class="row g-3(?: mb-[0-9]+)?">', r'<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6">'),
    (r'<div class="row.*?">', r'<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-6">'),
    (r'<div class="col-6 col-md-3">', r'<div>'),
    (r'<div class="(?:col-12 )?col-md-([0-9]+).*?">', r'<div class="col-span-1 md:col-span-\1">'),
    (r'<div class="(?:col-12 )?col-lg-([0-9]+).*?">', r'<div class="col-span-1 lg:col-span-\1">'),
    (r'<div class="col-12">', r'<div class="col-span-1 lg:col-span-12">'),
    
    # Cards
    (r'<div class="card(?: mb-[0-9]+)?(?: h-100)?">', r'<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-6 flex flex-col h-full hover:shadow-md transition-shadow duration-300">'),
    (r'<div class="card-header.*?">', r'<div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 text-slate-800 font-bold">'),
    (r'<div class="card-body(.*?)">', r'<div class="p-6 flex-1\1">'),
    (r'<div class="card-footer(.*?)">', r'<div class="px-6 py-4 border-t border-slate-100 bg-slate-50\1">'),
    
    # Tables
    (r'<div class="table-responsive">', r'<div class="overflow-x-auto ring-1 ring-slate-100 sm:rounded-lg">'),
    (r'<table class="table(?: mb-0)?(?: table-[a-z]+)*">', r'<table class="min-w-full text-end text-sm text-slate-500">'),
    (r'<thead>(<tr>.*)</thead>', r'<thead class="text-xs text-slate-600 uppercase bg-slate-100/80 font-bold border-b border-slate-200">\1</thead>'),
    (r'<th>', r'<th class="px-6 py-4 font-bold tracking-wider">'),
    (r'<tr>(<td.*)</tr>', r'<tr class="border-b border-slate-100 hover:bg-slate-50/50 transition-colors">\1</tr>'),
    (r'<td>', r'<td class="px-6 py-4 whitespace-nowrap">'),
    
    # Forms & Inputs
    (r'<div class="form-group(?: mb-[0-9]+)?">', r'<div class="mb-4">'),
    (r'<label(.*?)>', r'<label\1 class="block text-sm font-semibold text-slate-700 mb-2">'),
    (r'<input(.*?)class="form-control(.*?)"(.*?)>', r'<input\1class="block w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors py-2.5 px-3 border\2"\3>'),
    (r'<select(.*?)class="form-control(.*?)"(.*?)>', r'<select\1class="block w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors py-2.5 px-3 border bg-white\2"\3>'),
    (r'<textarea(.*?)class="form-control(.*?)"(.*?)>', r'<textarea\1class="block w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors py-2.5 px-3 border\2"\3>'),
    
    # Buttons
    (r'btn-primary', r'bg-indigo-600 text-white hover:bg-indigo-700 ring-indigo-600 focus:ring-offset-2 focus:ring-2 border border-transparent shadow-sm'),
    (r'btn-success', r'bg-emerald-600 text-white hover:bg-emerald-700 ring-emerald-600 focus:ring-offset-2 focus:ring-2 border border-transparent shadow-sm'),
    (r'btn-danger', r'bg-rose-600 text-white hover:bg-rose-700 ring-rose-600 focus:ring-offset-2 focus:ring-2 border border-transparent shadow-sm'),
    (r'btn-warning', r'bg-orange-500 text-white hover:bg-orange-600 ring-orange-500 focus:ring-offset-2 focus:ring-2 border border-transparent shadow-sm'),
    (r'btn-info', r'bg-sky-500 text-white hover:bg-sky-600 ring-sky-500 focus:ring-offset-2 focus:ring-2 border border-transparent shadow-sm'),
    (r'btn-secondary', r'bg-slate-100 text-slate-700 hover:bg-slate-200 ring-slate-200 focus:ring-offset-2 focus:ring-2 border border-transparent'),
    (r'class="btn btn-sm(.*?)(")', r'class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-semibold rounded-lg transition-all\1\2'),
    (r'class="btn(.*?)(")', r'class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold rounded-xl transition-all\1\2'),
    
    # Badges
    (r'badge-success', r'bg-emerald-100 text-emerald-800 ring-1 ring-inset ring-emerald-600/20'),
    (r'badge-warning', r'bg-orange-100 text-orange-800 ring-1 ring-inset ring-orange-600/20'),
    (r'badge-danger', r'bg-rose-100 text-rose-800 ring-1 ring-inset ring-rose-600/20'),
    (r'badge-primary', r'bg-indigo-100 text-indigo-800 ring-1 ring-inset ring-indigo-600/20'),
    (r'badge-secondary', r'bg-slate-100 text-slate-800 ring-1 ring-inset ring-slate-600/20'),
    (r'class="badge(.*?)(")', r'class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium\1\2'),
    
    # Modals (bootstrap to tailwind structure)
    (r'<div class="modal fade" id="(.*?)" tabindex="-1".*?>', r'<div class="fixed inset-0 z-50 overflow-y-auto" id="\1" style="display: none;" x-data="{ open: false }" @open-\1.window="open = true" x-show="open" aria-labelledby="modal-title" role="dialog" aria-modal="true">'),
    (r'<div class="modal-dialog.*?">', r'<div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0"><div class="fixed inset-0 bg-slate-500 bg-opacity-75 transition-opacity" @click="open = false" aria-hidden="true"></div><span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span><div class="inline-block align-bottom bg-white rounded-2xl text-start overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-100">'),
    (r'<div class="modal-content">', r'<div>'),
    (r'<div class="modal-header">', r'<div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex justify-between items-center text-lg font-bold">'),
    (r'<h[0-9] class="modal-title.*?">(.*?)</h[0-9]>', r'<h3 class="text-lg leading-6 font-bold text-slate-800" id="modal-title">\1</h3>'),
    (r'<button type="button" class="close" data-dismiss="modal".*?>.*?</button>', r'<button type="button" class="text-slate-400 hover:text-slate-500 rounded-lg p-1 transition" @click="open = false; $dispatch(\'close-modal\')"><svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg></button>'),
    (r'<div class="modal-footer(.*?)">', r'<div class="px-6 py-4 bg-slate-50 border-t border-slate-100 sm:flex sm:flex-row-reverse sm:gap-3\1">'),
    
    # Progress bars
    (r'<div class="progress(?: mb-[0-9]+)?">', r'<div class="w-full bg-slate-200 rounded-full h-2.5 overflow-hidden">'),
    (r'<div class="progress-bar(.*?)style="width:(.*?)%"></div>', r'<div class="bg-indigo-600 h-2.5 rounded-full\1" style="width:\2%"></div>'),
    
    # Specific Dashboard Stats fix
    (r'<div class="stat-card (.*?)\s+h-100">', r'<div class="stat-card \1 bg-white rounded-2xl shadow-sm border-b-4 h-full p-5 hover:-translate-y-1 transition-transform duration-300">'),
    (r'<div class="page-header.*?">', r'<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">'),
    (r'<div class="page-leftheader">', r'<div>'),
    (r'<div class="page-rightheader.*?">', r'<div class="flex h-full items-center">'),
    (r'<h4 class="page-title.*?">(.*?)</h4>', r'<h1 class="text-2xl font-black text-slate-800 tracking-tight flex items-center gap-2">\1</h1>'),
    (r'<ol class="breadcrumb.*?">(.*?)</ol>', r'<div class="mt-1 flex items-center text-sm text-slate-500">\1</div>'),
    (r'<li class="breadcrumb-item active">(.*?)</li>', r'<span>\1</span>'),
    
    # Utilities
    (r'text-muted', r'text-slate-500'),
    (r'text-primary', r'text-indigo-600'),
    (r'text-success', r'text-emerald-600'),
    (r'text-danger', r'text-rose-600'),
    (r'd-flex', r'flex'),
    (r'justify-content-between', r'justify-between'),
    (r'align-items-center', r'items-center'),
    (r'mb-([0-9]+)', lambda m: f"mb-{int(m.group(1))*2}"),  # mb-2 -> mb-4 in tailwind etc is roughly comparable
    (r'mt-([0-9]+)', lambda m: f"mt-{int(m.group(1))*2}"),
    (r'p-([0-9]+)', lambda m: f"p-{int(m.group(1))*2}"),
    (r'p-0', r'p-0'),
]

stat_colors = [
    (r'stat-card purple', r'bg-white rounded-2xl p-5 shadow-sm border border-slate-100 border-b-4 border-b-purple-500 hover:shadow-md transition-all'),
    (r'stat-card green', r'bg-white rounded-2xl p-5 shadow-sm border border-slate-100 border-b-4 border-b-emerald-500 hover:shadow-md transition-all'),
    (r'stat-card orange', r'bg-white rounded-2xl p-5 shadow-sm border border-slate-100 border-b-4 border-b-orange-500 hover:shadow-md transition-all'),
    (r'stat-card pink', r'bg-white rounded-2xl p-5 shadow-sm border border-slate-100 border-b-4 border-b-pink-500 hover:shadow-md transition-all'),
    (r'stat-card blue', r'bg-white rounded-2xl p-5 shadow-sm border border-slate-100 border-b-4 border-b-blue-500 hover:shadow-md transition-all'),
    (r'stat-card teal', r'bg-white rounded-2xl p-5 shadow-sm border border-slate-100 border-b-4 border-b-teal-500 hover:shadow-md transition-all'),
]

for root, dirs, files in os.walk(udhiya_path):
    for filename in files:
        if filename.endswith('.blade.php'):
            filepath = os.path.join(root, filename)
            with open(filepath, 'r', encoding='utf-8') as f:
                content = f.read()
            
            original_content = content
            
            for old, new in replacements:
                if callable(new):
                    content = re.sub(old, new, content)
                else:
                    content = re.sub(old, new, content)
            
            for old, new in stat_colors:
                content = re.sub(old, new, content)
                
            content = re.sub(r'<span class="stat-icon">(.*?)</span>', r'<div class="w-12 h-12 rounded-xl bg-slate-50 flex items-center justify-center text-2xl mb-4 shadow-sm">\1</div>', content)
            content = re.sub(r'<div class="stat-value">(.*?)</div>', r'<div class="text-3xl font-black text-slate-800 mb-1 tracking-tight">\1</div>', content)
            content = re.sub(r'<div class="stat-label">(.*?)</div>', r'<div class="text-sm font-semibold text-slate-500 uppercase">\1</div>', content)
            
            # Clean up double empty state icons
            content = re.sub(r'<td colspan="[0-9]+" class="empty-state">.*?</td>', r'<td colspan="10" class="text-center py-12"><div class="flex flex-col items-center justify-center text-slate-400"><span class="text-4xl mb-3">📭</span><p class="text-lg font-medium">لا توجد بيانات</p></div></td>', content)

            if content != original_content:
                with open(filepath, 'w', encoding='utf-8') as f:
                    f.write(content)
                print(f"Updated {filepath}")

print("Done converting udhiya views!")
