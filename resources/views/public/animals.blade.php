<!DOCTYPE html>
<html lang="ar" dir="rtl" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>برنامج الأضاحي - احجز نصيبك الآن</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Cairo', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased overflow-x-hidden">

{{-- ═══ NAVBAR ═══ --}}
<nav class="sticky top-0 z-40 bg-white/90 backdrop-blur-md border-b border-slate-100 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 bg-gradient-to-tr from-indigo-600 to-blue-500 rounded-xl flex items-center justify-center text-white text-lg shadow-md">🐄</div>
                <span class="text-xl font-black bg-clip-text text-transparent bg-gradient-to-l from-indigo-700 to-blue-600">برنامج الأضاحي</span>
            </div>
            <div class="flex items-center gap-3">
                <a href="#groups" class="hidden sm:inline-flex text-sm font-bold text-slate-600 hover:text-indigo-600 transition">المجموعات</a>
                <a href="#request" class="hidden sm:inline-flex text-sm font-bold text-slate-600 hover:text-indigo-600 transition">سجّل طلبك</a>
                @auth
                    <a href="{{ route('udhiya.dashboard') }}" class="inline-flex items-center px-4 py-2 text-sm font-bold text-white rounded-xl bg-indigo-600 hover:bg-indigo-700 transition shadow-sm">لوحة التحكم</a>
                @else
                    <a href="{{ route('signin') }}" class="inline-flex items-center px-4 py-2 text-sm font-bold text-indigo-700 bg-indigo-50 rounded-xl hover:bg-indigo-100 transition border border-indigo-200">تسجيل الدخول</a>
                @endauth
            </div>
        </div>
    </div>
</nav>

{{-- ═══ HERO ═══ --}}
<section class="relative overflow-hidden bg-gradient-to-br from-indigo-50 via-white to-blue-50 pt-20 pb-24">
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute top-[-15%] end-[-10%] w-[600px] h-[600px] bg-blue-100 rounded-full blur-3xl opacity-40"></div>
        <div class="absolute bottom-[-10%] start-[-5%] w-[500px] h-[500px] bg-indigo-100 rounded-full blur-3xl opacity-40"></div>
    </div>
    <div class="relative max-w-4xl mx-auto px-4 text-center">
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-indigo-100 border border-indigo-200 text-indigo-700 text-sm font-bold mb-6">
            <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
            التسجيل متاح الآن
        </div>
        <h1 class="text-4xl sm:text-5xl md:text-6xl font-black text-slate-900 leading-tight mb-6">
            احجز نصيبك في<br/>
            <span class="bg-clip-text text-transparent bg-gradient-to-l from-indigo-600 to-blue-500">مجموعات الأضاحي</span>
        </h1>
        <p class="text-lg sm:text-xl text-slate-600 font-medium max-w-2xl mx-auto mb-10 leading-relaxed">
            انضم إلى مجموعة ذبح أو سجّل طلب اشتراكك الآن. سنتواصل معك للتأكيد وترتيب التفاصيل.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="#groups" class="inline-flex items-center justify-center px-8 py-3.5 text-base font-bold text-white rounded-2xl bg-indigo-600 hover:bg-indigo-700 transition shadow-xl shadow-indigo-200 transform hover:-translate-y-0.5">
                🐄 استعرض المجموعات
            </a>
            <a href="#request" class="inline-flex items-center justify-center px-8 py-3.5 text-base font-bold text-slate-700 rounded-2xl bg-white hover:bg-slate-50 transition shadow-md border border-slate-200 transform hover:-translate-y-0.5">
                📋 سجّل طلبك الآن
            </a>
        </div>

        @if(session('toast_success'))
        <div class="mt-8 inline-flex items-center gap-3 px-5 py-3 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-800 font-bold text-sm">
            ✅ {{ session('toast_success') }}
        </div>
        @endif
        @if(session('toast_error'))
        <div class="mt-8 inline-flex items-center gap-3 px-5 py-3 bg-rose-50 border border-rose-200 rounded-2xl text-rose-800 font-bold text-sm">
            ❌ {{ session('toast_error') }}
        </div>
        @endif
    </div>
</section>

{{-- ═══ GROUPS SECTION ═══ --}}
@if(count($groups) > 0)
<section id="groups" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl sm:text-4xl font-black text-slate-900 mb-3">مجموعات الذبح المتاحة</h2>
            <p class="text-slate-500 font-medium">اختر المجموعة المناسبة لك وسجّل طلب انضمامك</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($groups as $group)
            @php
                $emoji = match($group['category_code']) { 'BQR' => '🐄', 'GHN' => '🐑', 'JDN' => '🐐', 'JML' => '🐪', default => '🐾' };
                $pct   = $group['total'] > 0 ? round(($group['used'] / $group['total']) * 100) : 0;
                $full  = $group['remaining'] === 0;
            @endphp
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col">
                {{-- Card Header --}}
                <div class="px-5 py-4 bg-gradient-to-b from-slate-50 to-white border-b border-slate-100 flex justify-between items-start gap-2">
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="text-2xl flex-shrink-0">{{ $emoji }}</span>
                        <h3 class="font-black text-slate-800 text-base leading-snug truncate">{{ $group['name'] }}</h3>
                    </div>
                    @if($full)
                        <span class="flex-shrink-0 inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-bold bg-rose-50 text-rose-600 border border-rose-200">مكتمل</span>
                    @else
                        <span class="flex-shrink-0 inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">{{ $group['remaining'] }} متبقي</span>
                    @endif
                </div>

                <div class="p-5 flex-1 flex flex-col gap-4">
                    {{-- Info rows --}}
                    <div class="space-y-2">
                        @if($group['animal_type'])
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-slate-500 font-semibold">نوع الذبيحة</span>
                            <span class="text-slate-800 font-bold">{{ $group['animal_type'] }}</span>
                        </div>
                        @endif
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-slate-500 font-semibold">نوع التقسيم</span>
                            <span class="text-slate-800 font-bold">{{ $group['share_label'] }}</span>
                        </div>
                        @if($group['slaughter_day'])
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-slate-500 font-semibold">يوم الذبح</span>
                            <span class="text-amber-700 font-bold bg-amber-50 px-2 py-0.5 rounded-lg">📅 {{ $group['slaughter_day'] }}</span>
                        </div>
                        @endif
                        @if($group['min_price'])
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-slate-500 font-semibold">السعر من</span>
                            <span class="text-indigo-700 font-black text-base">{{ number_format($group['min_price'], 0) }} <span class="text-xs font-bold text-indigo-400">ج.م</span></span>
                        </div>
                        @endif
                    </div>

                    {{-- Progress bar --}}
                    <div>
                        <div class="flex justify-between text-xs font-bold text-slate-400 mb-1.5">
                            <span>حالة الاكتمال</span>
                            <span>{{ $group['used'] }} / {{ $group['total'] }}</span>
                        </div>
                        <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-2 rounded-full transition-all duration-500 {{ $full ? 'bg-rose-400' : ($pct > 60 ? 'bg-amber-400' : 'bg-emerald-400') }}"
                                 style="width:{{ $pct }}%"></div>
                        </div>
                    </div>
                </div>

                {{-- Action button --}}
                <div class="px-5 pb-5">
                    @if(!$full)
                    <button type="button"
                            onclick="openGroupModal({{ json_encode($group) }})"
                            class="w-full inline-flex items-center justify-center px-4 py-2.5 text-sm font-bold text-white rounded-xl bg-indigo-600 hover:bg-indigo-700 transition shadow-md shadow-indigo-100 transform hover:-translate-y-0.5">
                        سجّل اشتراكك ←
                    </button>
                    @else
                    <div class="w-full text-center py-2.5 text-sm font-bold text-slate-400">المجموعة اكتملت</div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ═══ CATEGORIES SECTION ═══ --}}
@if(count($categoriesData) > 0)
<section id="categories" class="py-20 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl sm:text-4xl font-black text-slate-900 mb-3">الأضاحي المتاحة</h2>
            <p class="text-slate-500 font-medium">اختر نوع الأضحية والحصة المناسبة لك</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($categoriesData as $category)
            @php
                $catEmoji = match($category['name']) { 'عجول' => '🐄', 'جمال' => '🐪', 'خرفان' => '🐑', 'جديان' => '🐐', default => '🐾' };
            @endphp
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 bg-gradient-to-b from-slate-50 to-white flex items-center gap-3">
                    <span class="text-3xl">{{ $catEmoji }}</span>
                    <h3 class="font-black text-slate-800 text-lg">{{ $category['name'] }}</h3>
                </div>
                <div class="p-5 space-y-3">
                    @foreach($category['availableShares'] as $type => $shareData)
                    <button type="button"
                            onclick="openCategoryModal({{ $category['id'] }}, '{{ $category['name'] }}', '{{ $type }}', {{ $shareData['minPrice'] }}, {{ json_encode($category['availableShares']) }})"
                            class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-indigo-50 hover:border-indigo-200 transition-colors text-sm font-bold text-slate-700 hover:text-indigo-700">
                        <span>{{ $shareData['label'] }}</span>
                        <span class="text-indigo-600 font-black">من {{ number_format($shareData['minPrice'], 0) }} ج.م</span>
                    </button>
                    @endforeach
                    <button type="button"
                            onclick="openCategoryModal({{ $category['id'] }}, '{{ $category['name'] }}', '', 0, {{ json_encode($category['availableShares']) }})"
                            class="w-full inline-flex items-center justify-center px-4 py-2.5 text-sm font-bold text-white rounded-xl bg-indigo-600 hover:bg-indigo-700 transition shadow-md">
                        اطلب الآن ←
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ═══ REQUEST SECTION (standalone form) ═══ --}}
<section id="request" class="py-20 bg-white border-t border-slate-100">
    <div class="max-w-2xl mx-auto px-4">
        <div class="text-center mb-10">
            <h2 class="text-3xl sm:text-4xl font-black text-slate-900 mb-3">سجّل طلبك</h2>
            <p class="text-slate-500 font-medium">سجّل طلبك وسنتواصل معك لتأكيد التفاصيل والسعر</p>
        </div>

        <div class="bg-white rounded-3xl border border-slate-100 shadow-xl p-8">
            <form action="{{ route('public-animals.submit-request') }}" method="POST" class="space-y-5">
                @csrf
                <input type="hidden" name="group_id" id="standAloneGroupId">
                <input type="hidden" name="category_id" id="standAloneCategoryId">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">الاسم الكامل <span class="text-rose-500">*</span></label>
                        <input type="text" name="customer_name" required
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 py-3 px-4 text-sm font-semibold text-slate-800 transition-colors"
                               placeholder="اسمك الكامل">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">رقم الهاتف <span class="text-rose-500">*</span></label>
                        <input type="tel" name="customer_phone" required
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 py-3 px-4 text-sm font-semibold text-slate-800 transition-colors"
                               placeholder="01xxxxxxxxx">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">البريد الإلكتروني <span class="text-slate-400 font-normal">(اختياري)</span></label>
                    <input type="email" name="customer_email"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 py-3 px-4 text-sm font-semibold text-slate-800 transition-colors"
                           placeholder="example@mail.com">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">نوع الحصة <span class="text-rose-500">*</span></label>
                    <select name="share_type" required
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 py-3 px-4 text-sm font-semibold text-slate-800 transition-colors">
                        <option value="">— اختر الحصة —</option>
                        <option value="full">كامل</option>
                        <option value="half">نصف</option>
                        <option value="third">ثُلث</option>
                        <option value="quarter">ربع</option>
                        <option value="five">خُمس</option>
                        <option value="six">سُدس</option>
                        <option value="seven">سُبع</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">ملاحظات <span class="text-slate-400 font-normal">(اختياري)</span></label>
                    <textarea name="notes" rows="3"
                              class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 py-3 px-4 text-sm font-semibold text-slate-800 transition-colors resize-none"
                              placeholder="أي تفاصيل أو طلبات خاصة..."></textarea>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-sm text-blue-700 font-semibold">
                    📞 سيتم التواصل معك قريباً لتأكيد الطلب والسعر النهائي
                </div>

                <button type="submit"
                        class="w-full inline-flex items-center justify-center px-6 py-3.5 text-base font-bold text-white rounded-xl bg-indigo-600 hover:bg-indigo-700 transition shadow-xl shadow-indigo-200 transform hover:-translate-y-0.5">
                    ✅ تقديم الطلب
                </button>
            </form>
        </div>
    </div>
</section>

{{-- ═══ FOOTER ═══ --}}
<footer class="bg-slate-900 py-10 border-t border-slate-800">
    <div class="max-w-7xl mx-auto px-4 text-center">
        <div class="flex items-center justify-center gap-2.5 mb-4">
            <span class="text-2xl">🐄</span>
            <span class="text-xl font-bold text-white">برنامج الأضاحي</span>
        </div>
        <p class="text-slate-400 font-medium text-sm">&copy; {{ date('Y') }} جميع الحقوق محفوظة</p>
    </div>
</footer>

{{-- ═══ GROUP MODAL ═══ --}}
<div id="groupModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4"
     onclick="if(event.target===this) closeGroupModal()">
    <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full overflow-hidden" onclick="event.stopPropagation()">
        <div class="px-6 py-5 border-b border-slate-100 bg-gradient-to-b from-indigo-50 to-white flex justify-between items-center">
            <div>
                <h3 id="modalGroupName" class="text-lg font-black text-slate-800"></h3>
                <p id="modalGroupInfo" class="text-sm text-slate-500 font-semibold mt-0.5"></p>
            </div>
            <button onclick="closeGroupModal()" class="w-8 h-8 rounded-xl bg-slate-100 text-slate-500 hover:bg-slate-200 flex items-center justify-center font-bold text-lg transition-colors">✕</button>
        </div>

        <form action="{{ route('public-animals.submit-request') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="group_id" id="modalGroupId">
            <input type="hidden" name="share_type" id="modalShareType">
            <input type="hidden" name="share_price" id="modalSharePrice" value="0">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">الاسم <span class="text-rose-500">*</span></label>
                    <input type="text" name="customer_name" required
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors"
                           placeholder="اسمك الكامل">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">الهاتف <span class="text-rose-500">*</span></label>
                    <input type="tel" name="customer_phone" required
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors"
                           placeholder="01xxxxxxxxx">
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1.5">البريد <span class="text-slate-400 font-normal text-xs">(اختياري)</span></label>
                <input type="email" name="customer_email"
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1.5">ملاحظات <span class="text-slate-400 font-normal text-xs">(اختياري)</span></label>
                <textarea name="notes" rows="2"
                          class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors resize-none"
                          placeholder="أي ملاحظات..."></textarea>
            </div>

            {{-- Price display --}}
            <div id="priceRow" class="hidden bg-indigo-50 border border-indigo-100 rounded-xl px-4 py-3 flex items-center justify-between">
                <span class="text-sm font-bold text-indigo-700">السعر المبدئي</span>
                <span id="priceDisplay" class="text-lg font-black text-indigo-800"></span>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-xl px-4 py-3 text-sm text-blue-700 font-semibold">
                📞 سيتم التواصل معك لتأكيد الطلب والسعر النهائي
            </div>

            <div class="flex gap-3 pt-1">
                <button type="submit" class="flex-1 inline-flex items-center justify-center px-5 py-3 text-sm font-bold text-white rounded-xl bg-indigo-600 hover:bg-indigo-700 transition shadow-md">
                    ✅ تقديم الطلب
                </button>
                <button type="button" onclick="closeGroupModal()" class="flex-1 inline-flex items-center justify-center px-5 py-3 text-sm font-bold text-slate-700 rounded-xl bg-slate-100 hover:bg-slate-200 transition">
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ═══ CATEGORY MODAL ═══ --}}
<div id="categoryModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4"
     onclick="if(event.target===this) closeCategoryModal()">
    <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full overflow-hidden" onclick="event.stopPropagation()">
        <div class="px-6 py-5 border-b border-slate-100 bg-gradient-to-b from-slate-50 to-white flex justify-between items-center">
            <h3 id="catModalTitle" class="text-lg font-black text-slate-800"></h3>
            <button onclick="closeCategoryModal()" class="w-8 h-8 rounded-xl bg-slate-100 text-slate-500 hover:bg-slate-200 flex items-center justify-center font-bold text-lg transition-colors">✕</button>
        </div>

        <form action="{{ route('public-animals.submit-request') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="category_id" id="catModalCategoryId">

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1.5">نوع الحصة <span class="text-rose-500">*</span></label>
                <select name="share_type" id="catModalShareType" required
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                    <option value="">— اختر —</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">الاسم <span class="text-rose-500">*</span></label>
                    <input type="text" name="customer_name" required
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">الهاتف <span class="text-rose-500">*</span></label>
                    <input type="tel" name="customer_phone" required
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1.5">البريد <span class="text-slate-400 font-normal text-xs">(اختياري)</span></label>
                <input type="email" name="customer_email"
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1.5">ملاحظات <span class="text-slate-400 font-normal text-xs">(اختياري)</span></label>
                <textarea name="notes" rows="2"
                          class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors resize-none"></textarea>
            </div>

            <div id="catPriceRow" class="hidden bg-indigo-50 border border-indigo-100 rounded-xl px-4 py-3 flex items-center justify-between">
                <span class="text-sm font-bold text-indigo-700">السعر المبدئي</span>
                <span id="catPriceDisplay" class="text-lg font-black text-indigo-800"></span>
                <input type="hidden" name="share_price" id="catSharePrice" value="0">
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-xl px-4 py-3 text-sm text-blue-700 font-semibold">
                📞 سيتم التواصل معك لتأكيد الطلب والسعر النهائي
            </div>

            <div class="flex gap-3 pt-1">
                <button type="submit" class="flex-1 inline-flex items-center justify-center px-5 py-3 text-sm font-bold text-white rounded-xl bg-indigo-600 hover:bg-indigo-700 transition shadow-md">
                    ✅ تقديم الطلب
                </button>
                <button type="button" onclick="closeCategoryModal()" class="flex-1 inline-flex items-center justify-center px-5 py-3 text-sm font-bold text-slate-700 rounded-xl bg-slate-100 hover:bg-slate-200 transition">
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const SHARE_LABELS = {
    full: 'كامل', half: 'نصف', third: 'ثُلث', quarter: 'ربع',
    five: 'خُمس', six: 'سُدس', seven: 'سُبع'
};

/* ── Group Modal ── */
function openGroupModal(group) {
    document.getElementById('modalGroupId').value   = group.id;
    document.getElementById('modalShareType').value = group.share_type;
    document.getElementById('modalGroupName').textContent = group.name;

    let info = group.share_label;
    if (group.slaughter_day) info += ' · يوم الذبح: ' + group.slaughter_day;
    document.getElementById('modalGroupInfo').textContent = info;

    const priceRow = document.getElementById('priceRow');
    if (group.min_price && group.min_price > 0) {
        document.getElementById('modalSharePrice').value = group.min_price;
        document.getElementById('priceDisplay').textContent =
            Number(group.min_price).toLocaleString('ar-EG') + ' ج.م';
        priceRow.classList.remove('hidden');
        priceRow.classList.add('flex');
    } else {
        priceRow.classList.add('hidden');
        priceRow.classList.remove('flex');
    }

    const modal = document.getElementById('groupModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeGroupModal() {
    const modal = document.getElementById('groupModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = '';
}

/* ── Category Modal ── */
let _catShares = {};

function openCategoryModal(categoryId, categoryName, shareType, sharePrice, shares) {
    _catShares = shares || {};

    document.getElementById('catModalCategoryId').value = categoryId;
    document.getElementById('catModalTitle').textContent = categoryName;

    // Build share_type options from available shares
    const sel = document.getElementById('catModalShareType');
    sel.innerHTML = '<option value="">— اختر الحصة —</option>';
    Object.entries(_catShares).forEach(([type, data]) => {
        const opt = new Option(data.label + ' — من ' + Number(data.minPrice).toLocaleString('ar-EG') + ' ج.م', type);
        sel.appendChild(opt);
    });
    if (shareType) sel.value = shareType;

    _updateCatPrice();

    const modal = document.getElementById('categoryModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeCategoryModal() {
    const modal = document.getElementById('categoryModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = '';
}

function _updateCatPrice() {
    const type = document.getElementById('catModalShareType').value;
    const priceRow = document.getElementById('catPriceRow');
    if (type && _catShares[type] && _catShares[type].minPrice > 0) {
        const price = _catShares[type].minPrice;
        document.getElementById('catPriceDisplay').textContent = Number(price).toLocaleString('ar-EG') + ' ج.م';
        document.getElementById('catSharePrice').value = price;
        priceRow.classList.remove('hidden');
        priceRow.classList.add('flex');
    } else {
        priceRow.classList.add('hidden');
        priceRow.classList.remove('flex');
    }
}

document.getElementById('catModalShareType')?.addEventListener('change', _updateCatPrice);

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') { closeGroupModal(); closeCategoryModal(); }
});
</script>

</body>
</html>
