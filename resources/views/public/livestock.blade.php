<!DOCTYPE html>
<html lang="ar" dir="rtl" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>مشروع الأضاحي - الحصص المتاحة</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; }
        .glass-nav {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
        }
        .mesh-gradient {
            background-color: #f8fafc;
            background-image: 
                radial-gradient(at 0% 0%, hsla(253,16%,7%,1) 0, transparent 50%), 
                radial-gradient(at 50% 0%, hsla(225,39%,30%,1) 0, transparent 50%), 
                radial-gradient(at 100% 0%, hsla(339,49%,30%,1) 0, transparent 50%);
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased overflow-x-hidden selection:bg-indigo-500 selection:text-white">

    <!-- Decorative Background Splashes -->
    <div class="absolute top-[-10%] start-[-10%] w-[500px] h-[500px] rounded-full bg-gradient-to-br from-indigo-400/20 to-purple-400/20 blur-3xl -z-10 pointer-events-none"></div>
    <div class="absolute top-[20%] end-[-10%] w-[600px] h-[600px] rounded-full bg-gradient-to-tl from-sky-400/10 to-blue-400/10 blur-3xl -z-10 pointer-events-none"></div>

    <!-- NAVBAR -->
    <nav class="glass-nav fixed top-0 w-full z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-600 to-violet-600 flex items-center justify-center text-white text-2xl shadow-lg shadow-indigo-200 transform group-hover:rotate-12 transition duration-300">
                        🐄
                    </div>
                    <span class="text-2xl font-black bg-clip-text text-transparent bg-gradient-to-r from-slate-800 to-slate-600">مشروع <span class="text-indigo-600">الأضاحي</span></span>
                </a>
                <div class="flex items-center gap-4">
                    @auth
                    <a href="{{ route('udhiya.dashboard') }}" class="inline-flex items-center justify-center px-6 py-2.5 text-sm font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 rounded-xl transition duration-300">
                        لوحة التحكم <span class="ms-2">→</span>
                    </a>
                    @else
                    <a href="{{ route('signin') }}" class="inline-flex items-center justify-center px-6 py-2.5 text-sm font-bold text-white bg-slate-900 hover:bg-slate-800 rounded-xl transition shadow-lg shadow-slate-200 transform hover:-translate-y-0.5 duration-300">
                        تسجيل الدخول
                    </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section class="relative pt-32 pb-20 lg:pt-40 lg:pb-28 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm font-bold mb-8 animate-fade-in-up">
                <span class="relative flex h-2.5 w-2.5">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                </span>
                موسم الأضاحي متاح الآن
            </div>
            <h1 class="text-5xl md:text-6xl lg:text-7xl font-black text-slate-900 tracking-tight mb-6 leading-[1.1] max-w-4xl mx-auto">
                اختر أضحيتك واستكمل <br class="hidden sm:block"> <span class="bg-clip-text text-transparent bg-gradient-to-l from-indigo-600 to-violet-500">مراسيم الأجر</span> بكل اطمئنان
            </h1>
            <p class="text-lg md:text-xl text-slate-500 font-medium max-w-2xl mx-auto mb-10 leading-relaxed">
                تصفح الأضاحي المتوفرة وحصص الاشتراك المتاحة، وقم بتأكيد حجزك بكل سهولة قبل نفاد الكمية.
            </p>
        </div>
    </section>

    <!-- STATS -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-10 relative z-20 mb-16">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
            {{-- Stat 1 --}}
            <div class="bg-white rounded-3xl p-6 shadow-xl shadow-slate-200/40 border border-slate-100 flex flex-col items-center justify-center transform transition duration-500 hover:-translate-y-2">
                <div class="w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-3xl mb-4">🐄</div>
                <div class="text-3xl font-black text-slate-800 mb-1">{{ $stats['total'] }}</div>
                <div class="text-sm font-bold text-slate-500">إجمالي الأضاحي</div>
            </div>
            {{-- Stat 2 --}}
            <div class="bg-white rounded-3xl p-6 shadow-xl shadow-slate-200/40 border border-slate-100 flex flex-col items-center justify-center transform transition duration-500 hover:-translate-y-2">
                <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-3xl mb-4">✅</div>
                <div class="text-3xl font-black text-slate-800 mb-1">{{ $stats['available'] }}</div>
                <div class="text-sm font-bold text-slate-500">متاح للاشتراك</div>
            </div>
            {{-- Stat 3 --}}
            <div class="bg-white rounded-3xl p-6 shadow-xl shadow-slate-200/40 border border-slate-100 flex flex-col items-center justify-center transform transition duration-500 hover:-translate-y-2">
                <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center text-3xl mb-4">🎫</div>
                <div class="text-3xl font-black text-slate-800 mb-1">{{ $stats['total_spots'] }}</div>
                <div class="text-sm font-bold text-slate-500">حصة متبقية</div>
            </div>
            {{-- Stat 4 --}}
            <div class="bg-white rounded-3xl p-6 shadow-xl shadow-slate-200/40 border border-slate-100 flex flex-col items-center justify-center transform transition duration-500 hover:-translate-y-2">
                <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center text-3xl mb-4">🔪</div>
                <div class="text-3xl font-black text-slate-800 mb-1">{{ $stats['slaughtered'] }}</div>
                <div class="text-sm font-bold text-slate-500">تم ذبحها</div>
            </div>
        </div>
    </div>

    <!-- MAIN APP WRAPPER -->
    <div x-data="{ 
            filter: 'all', 
            search: '',
            get animalsList() {
                let els = document.querySelectorAll('.animal-card-wrapper');
                let count = 0;
                els.forEach(el => {
                    let cat = el.dataset.category;
                    let status = el.dataset.status;
                    let code = el.dataset.code.toLowerCase();
                    
                    let showCategory = (this.filter === 'all') || 
                                       (this.filter === 'available' && (status === 'available' || status === 'partially_allocated')) ||
                                       (this.filter === 'fully_allocated' && status === 'fully_allocated') ||
                                       (cat === this.filter);
                                       
                    let showSearch = this.search === '' || code.includes(this.search.toLowerCase());
                    
                    if (showCategory && showSearch) {
                        el.style.display = '';
                        count++;
                    } else {
                        el.style.display = 'none';
                    }
                });
                return count;
            }
         }" 
         class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-24">

        <div class="flex flex-col items-center text-center pb-10">
            <h2 class="text-3xl md:text-4xl font-black text-slate-900 mb-4">قائمة الأضاحي المتوفرة</h2>
            <p class="text-slate-500 font-medium text-lg max-w-xl">اختر الأضحية المناسبة وتواصل مع الإدارة عبر المعرف الخاص بها لإتمام الحجز.</p>
        </div>

        <!-- Filter & Search Bar -->
        <div class="bg-white p-4 rounded-3xl shadow-lg shadow-slate-200/50 border border-slate-100 flex flex-col lg:flex-row items-center justify-between gap-4 mb-12 sticky top-24 z-40">
            <div class="flex flex-wrap items-center gap-2 justify-center lg:justify-start w-full lg:w-auto">
                <button @click="filter = 'all'" :class="filter === 'all' ? 'bg-slate-900 text-white shadow-md' : 'bg-slate-50 text-slate-600 hover:bg-slate-100'" class="px-5 py-2.5 rounded-full text-sm font-bold transition-all duration-300">
                    الكل <span class="bg-white/20 px-2 py-0.5 rounded-full ms-1 text-xs">{{ $animals->count() }}</span>
                </button>
                @foreach($categories as $cat)
                @php $catCount = $animals->filter(fn($a) => $a->product?->mainCategory?->id === $cat->id)->count(); @endphp
                <button @click="filter = '{{ $cat->code }}'" :class="filter === '{{ $cat->code }}' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-200' : 'bg-slate-50 text-slate-600 hover:bg-slate-100'" class="px-5 py-2.5 rounded-full text-sm font-bold transition-all duration-300 flex items-center gap-2">
                    <span>{{ match($cat->code) { 'BQR' => '🐄', 'GHN' => '🐑', 'JDN' => '🐐', 'JML' => '🐪', default => '🐾' } }}</span>
                    {{ $cat->name }} <span class="bg-black/10 px-2 py-0.5 rounded-full text-xs">{{ $catCount }}</span>
                </button>
                @endforeach
                <div class="w-px h-8 bg-slate-200 mx-2 hidden sm:block"></div>
                <button @click="filter = 'available'" :class="filter === 'available' ? 'bg-emerald-500 text-white shadow-md shadow-emerald-200' : 'bg-slate-50 text-slate-600 hover:bg-slate-100'" class="px-5 py-2.5 rounded-full text-sm font-bold transition-all duration-300">
                    ✅ متاح
                </button>
                <button @click="filter = 'fully_allocated'" :class="filter === 'fully_allocated' ? 'bg-amber-500 text-white shadow-md shadow-amber-200' : 'bg-slate-50 text-slate-600 hover:bg-slate-100'" class="px-5 py-2.5 rounded-full text-sm font-bold transition-all duration-300">
                    🔒 مكتمل
                </button>
            </div>
            <div class="w-full lg:w-72 relative">
                <div class="absolute inset-y-0 start-0 flex items-center ps-4 pointer-events-none text-slate-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input x-model="search" type="text" class="w-full bg-slate-50 border-0 rounded-full py-3 ps-12 pe-4 text-sm font-medium text-slate-700 focus:ring-2 focus:ring-indigo-500 transition shadow-inner" placeholder="ابحث برقم المعرف...">
            </div>
        </div>

        @if($animals->isEmpty())
        <div class="bg-white rounded-3xl p-16 text-center border border-slate-100 shadow-sm">
            <div class="text-7xl mb-6">🐄</div>
            <h3 class="text-2xl font-bold text-slate-800 mb-2">لا توجد أضاحي متوفرة</h3>
            <p class="text-slate-500 font-medium">نعتذر، لم يتم إضافة أي أضاحي بعد في النظام.</p>
        </div>
        @else
        <!-- Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 sm:gap-8" x-effect="animalsList">
            
            @foreach($animals as $animal)
            @php
                $catCode  = $animal->product?->mainCategory?->code ?? 'BQR';
                $catName  = $animal->product?->mainCategory?->name ?? 'حيوان';
                $emoji    = match($catCode) { 'BQR' => '🐄', 'GHN' => '🐑', 'JDN' => '🐐', 'JML' => '🐪', default => '🐾' };
                $colorTheme = match($catCode) { 
                    'BQR' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'ring' => 'ring-blue-500/20'],
                    'GHN' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'ring' => 'ring-emerald-500/20'],
                    'JDN' => ['bg' => 'bg-orange-50', 'text' => 'text-orange-700', 'ring' => 'ring-orange-500/20'],
                    'JML' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-800', 'ring' => 'ring-amber-500/20'],
                    default => ['bg' => 'bg-slate-50', 'text' => 'text-slate-700', 'ring' => 'ring-slate-500/20']
                };
                
                $price    = $animal->price_full ?? $animal->cost ?? 0;
                $setting  = $animal->shareSetting;

                $maxShares = $setting ? $setting->total_shares : 1;
                $usedShares = $setting ? $setting->sold_shares : ($animal->status === 'fully_allocated' ? 1 : 0);
                $remainingShares = $setting ? $setting->remaining_shares : ($animal->status === 'available' ? 1 : 0);
                $pct = $maxShares > 0 ? round(($usedShares / $maxShares) * 100) : 0;

                $statusDetails = match($animal->status) {
                    'available'           => ['label' => 'متاح', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-800', 'bar' => 'bg-emerald-500'],
                    'partially_allocated' => ['label' => 'متاح جزئياً', 'bg' => 'bg-orange-100', 'text' => 'text-orange-800', 'bar' => 'bg-gradient-to-r from-orange-400 to-amber-500'],
                    'fully_allocated'     => ['label' => 'مكتمل', 'bg' => 'bg-rose-100', 'text' => 'text-rose-800', 'bar' => 'bg-rose-500'],
                    'slaughtered'         => ['label' => 'تم الذبح', 'bg' => 'bg-slate-200', 'text' => 'text-slate-600', 'bar' => 'bg-slate-400'],
                    default               => ['label' => $animal->status, 'bg' => 'bg-slate-100', 'text' => 'text-slate-800', 'bar' => 'bg-slate-500']
                };

                $pricePerShare = $maxShares > 0 ? round($price / $maxShares) : $price;
                $isSlaughtered = $animal->status === 'slaughtered';
            @endphp
            
            <div class="animal-card-wrapper" data-category="{{ $catCode }}" data-status="{{ $animal->status }}" data-code="{{ $animal->code }}">
                <div class="relative bg-white rounded-3xl p-2 shadow-lg shadow-slate-200/40 border border-slate-100 transition-all duration-300 hover:shadow-2xl hover:shadow-indigo-200/40 hover:-translate-y-2 group flex flex-col h-full {{ $isSlaughtered ? 'opacity-70 grayscale-[30%]' : '' }}">
                    
                    {{-- Header Emoji area --}}
                    <div class="h-44 {{ $colorTheme['bg'] }} rounded-2xl flex items-center justify-center text-7xl relative overflow-hidden group-hover:scale-[1.02] transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-b from-transparent to-white/60"></div>
                        <span class="relative z-10 transform group-hover:scale-110 group-hover:rotate-3 transition duration-500">{{ $emoji }}</span>
                        <div class="absolute top-3 start-3">
                            <span class="px-3 py-1 font-bold text-xs rounded-full {{ $statusDetails['bg'] }} {{ $statusDetails['text'] }} shadow-sm">
                                {{ $statusDetails['label'] }}
                            </span>
                        </div>
                        <div class="absolute top-3 end-3">
                            <span class="px-2 py-1 font-bold text-xs bg-white text-slate-600 rounded-lg shadow-sm border border-slate-100">
                                #{{ $animal->code }}
                            </span>
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="p-5 flex-1 flex flex-col">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-lg font-black text-slate-800">{{ $catName }}</h3>
                            @if($animal->weight)
                            <span class="inline-flex items-center gap-1 text-sm font-bold text-slate-500 bg-slate-50 px-2 py-1 rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path></svg>
                                {{ number_format($animal->weight, 0) }} كجم
                            </span>
                            @endif
                        </div>

                        <div class="mb-4">
                            <div class="text-2xl font-black text-indigo-600 mb-1 flex items-end gap-1">
                                <span>{{ number_format($price, 0) }}</span> 
                                <span class="text-sm font-bold text-slate-400 mb-1">ج.م</span>
                            </div>
                            @if($animal->is_grouped)
                            <div class="text-sm font-bold text-slate-500">
                                السهم: <span class="text-slate-700">{{ number_format($pricePerShare, 0) }} ج.م</span>
                            </div>
                            @endif
                        </div>

                        {{-- Progress Area --}}
                        <div class="mt-auto">
                            @if($animal->is_grouped && $setting)
                            <div class="mb-3">
                                <div class="flex justify-between text-xs font-bold mb-2">
                                    <span class="text-slate-500">الحصص ({{ $usedShares }}/{{ $maxShares }})</span>
                                    @if($remainingShares > 0)
                                        <span class="text-emerald-600">متبقي {{ $remainingShares }}</span>
                                    @else
                                        <span class="text-rose-500">مكتمل</span>
                                    @endif
                                </div>
                                <div class="h-2.5 w-full bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full {{ $statusDetails['bar'] }} rounded-full transition-all duration-1000 ease-out" style="width: {{ $pct }}%"></div>
                                </div>
                                <div class="flex flex-wrap gap-1 mt-3">
                                    @for($i = 1; $i <= $maxShares; $i++)
                                    <div class="h-1.5 flex-1 rounded-full {{ $i <= $usedShares ? $statusDetails['bar'] : 'bg-slate-200' }}"></div>
                                    @endfor
                                </div>
                            </div>
                            @else
                                <div class="py-3 px-4 bg-slate-50 rounded-xl text-center text-sm font-bold text-slate-600 mt-2 mb-2 border border-slate-100">
                                    أضحية كاملة (شخص واحد)
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- No search results display (controlled via alpine) -->
        <div x-show="animalsList === 0 && search !== ''" x-cloak class="bg-white rounded-3xl p-12 text-center border border-slate-100 shadow-sm mt-8">
            <h3 class="text-xl font-bold text-slate-800">لا يوجد أضاحي متطابقة للبحث</h3>
            <button @click="search = ''; filter = 'all'" class="mt-4 px-6 py-2 text-indigo-600 bg-indigo-50 rounded-xl font-bold hover:bg-indigo-100 transition">إلغاء البحث</button>
        </div>
        @endif
    </div>

    <!-- FOOTER -->
    <footer class="bg-slate-900 border-t border-slate-800 py-12 relative z-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col items-center">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-slate-800 rounded-xl flex items-center justify-center text-white text-xl">
                    🐄
                </div>
                <span class="text-2xl font-black text-white">مشروع الأضاحي</span>
            </div>
            <p class="text-slate-400 font-medium text-sm text-center max-w-sm">
                نظام سحابي متطور لتسهيل إدارة وبيع الأضاحي حصصاً أو كاملة بكل شفافية واحترافية.
            </p>
            <div class="w-full max-w-md h-px bg-slate-800 my-8"></div>
            <p class="text-slate-500 text-sm font-bold">
                جميع الحقوق محفوظة &copy; {{ date('Y') }}
            </p>
        </div>
    </footer>

</body>
</html>
