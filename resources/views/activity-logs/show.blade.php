@extends('layouts.master')

@section('page-header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8 mt-2">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
            <span class="text-slate-600 text-4xl">🔍</span> تفاصيل النشاط
        </h1>
        <p class="text-slate-500 font-medium text-sm mt-1">
            <a href="{{ route('admin.activity-logs.index') }}" class="text-indigo-500 hover:text-indigo-700 hover:underline">سجل النشاط</a>
            / #{{ $log->id }}
        </p>
    </div>
    <div class="flex items-center gap-3">
        @if($log->id > 1)
        <a href="{{ route('admin.activity-logs.show', $log->id - 1) }}"
           class="w-9 h-9 rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 inline-flex items-center justify-center transition-colors">→</a>
        @endif
        <a href="{{ route('admin.activity-logs.show', $log->id + 1) }}"
           class="w-9 h-9 rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 inline-flex items-center justify-center transition-colors">←</a>
        <a href="{{ route('admin.activity-logs.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 transition-all">
            ← العودة
        </a>
    </div>
</div>
@endsection

@section('content')
@php
// ─── Action config ────────────────────────────────────
$actionCfg = [
    'created' => ['bg-emerald-50 text-emerald-700 border-emerald-200', '➕', 'إنشاء جديد'],
    'updated' => ['bg-amber-50  text-amber-700  border-amber-200',     '✏️', 'تعديل'],
    'deleted' => ['bg-rose-50   text-rose-700   border-rose-200',      '🗑', 'حذف'],
    'viewed'  => ['bg-slate-50  text-slate-600  border-slate-200',     '👁', 'عرض'],
    'login'   => ['bg-indigo-50 text-indigo-700 border-indigo-200',    '🔐', 'تسجيل دخول'],
    'logout'  => ['bg-purple-50 text-purple-700 border-purple-200',    '🚪', 'تسجيل خروج'],
];
[$aCls, $aEmoji, $aLbl] = $actionCfg[$log->action] ?? ['bg-slate-50 text-slate-600 border-slate-200', '•', $log->action];

// ─── Model type map ────────────────────────────────────
$modelMap = [
    'App\\Models\\User'          => ['👤', 'مستخدم'],
    'App\\Models\\Role'          => ['🔑', 'دور صلاحية'],
    'App\\Models\\Animal'        => ['🐄', 'حيوان'],
    'App\\Models\\Contract'      => ['📑', 'صك'],
    'App\\Models\\ContractItem'  => ['📝', 'بند صك'],
    'App\\Models\\Purchase'      => ['🛒', 'مشتريات'],
    'App\\Models\\PurchaseItem'  => ['📦', 'بند مشتريات'],
    'App\\Models\\Payment'       => ['💰', 'دفعة'],
    'App\\Models\\Customer'      => ['👥', 'عميل'],
    'App\\Models\\Supplier'      => ['🚚', 'مورد'],
    'App\\Models\\Expense'       => ['💸', 'مصروف'],
    'App\\Models\\SlaughterGroup'=> ['🔪', 'مجموعة ذبح'],
    'App\\Models\\MeatInventory' => ['🧊', 'مخزن لحوم'],
    'App\\Models\\MeatSale'      => ['🥩', 'بيع لحوم'],
    'App\\Models\\Warehouse'     => ['🏪', 'موقع/مخزن'],
    'App\\Models\\Product'       => ['🏷', 'صنف'],
];
[$mEmoji, $mLabel] = $modelMap[$log->model_type] ?? ['📄', class_basename($log->model_type ?? 'غير محدد')];

// ─── Field name translations ───────────────────────────
$fieldNames = [
    'name'            => 'الاسم',
    'email'           => 'البريد الإلكتروني',
    'status'          => 'الحالة',
    'password'        => 'كلمة المرور',
    'role'            => 'الدور',
    'display_name'    => 'الاسم للعرض',
    'description'     => 'الوصف',
    'code'            => 'الكود',
    'cost'            => 'التكلفة',
    'price'           => 'السعر',
    'weight'          => 'الوزن',
    'total_amount'    => 'الإجمالي',
    'paid_amount'     => 'المدفوع',
    'remaining_amount'=> 'المتبقي',
    'notes'           => 'ملاحظات',
    'date'            => 'التاريخ',
    'amount'          => 'المبلغ',
    'category'        => 'التصنيف',
    'customer_id'     => 'العميل',
    'animal_id'       => 'الحيوان',
    'warehouse_id'    => 'الموقع',
    'slaughter_day'   => 'يوم الذبح',
    'phone'           => 'الهاتف',
    'address'         => 'العنوان',
    'is_grouped'      => 'مجموعة',
    'share_type'      => 'نوع التقسيم',
    'total_shares'    => 'إجمالي الأنصبة',
    'sold_shares'     => 'الأنصبة المباعة',
    'weight_kg'       => 'الوزن (كجم)',
    'sold_weight_kg'  => 'المباع (كجم)',
    'slaughtered_at'  => 'تاريخ الذبح',
    'delivered_at'    => 'تاريخ التسليم',
];

$statusLabels = [
    'active'              => '✅ نشط',
    'inactive'            => '⏸ غير نشط',
    'banned'              => '🚫 محظور',
    'available'           => '✅ متاح',
    'partially_allocated' => '⏳ مخصص جزئياً',
    'fully_allocated'     => '🔒 مخصص كلياً',
    'slaughtered'         => '⚡ مذبوح',
    'completed'           => '✅ مكتمل',
    'cancelled'           => '❌ ملغي',
    '1'                   => 'نعم',
    '0'                   => 'لا',
];

// Check if description is just an HTTP log (from middleware)
$isHttpLog = preg_match('/^(GET|POST|PUT|PATCH|DELETE)\s+/', $log->description);

// Parse HTTP log to Arabic
$httpDescription = null;
if ($isHttpLog) {
    $parts = explode(' ', $log->description, 2);
    $method = $parts[0] ?? '';
    $path   = $parts[1] ?? '';
    $methodLabels = ['GET'=>'عرض صفحة','POST'=>'إرسال بيانات','PUT'=>'تعديل','PATCH'=>'تعديل','DELETE'=>'حذف'];
    $httpDescription = ($methodLabels[$method] ?? $method) . ': ' . $path;
}
@endphp

<div class="flex flex-col lg:flex-row gap-5 max-w-5xl">

    {{-- ═══ RIGHT: Summary card ═══ --}}
    <div class="w-full lg:w-72 flex-shrink-0 flex flex-col gap-5">

        {{-- Who did it --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                <h6 class="text-base font-black text-slate-800 m-0">👤 من قام بالنشاط</h6>
            </div>
            <div class="p-5 flex flex-col gap-4">
                @if($log->user)
                <div class="flex items-center gap-3">
                    @if($log->user->profile?->avatar)
                        <img src="{{ asset($log->user->profile->avatar) }}" alt=""
                             class="w-12 h-12 rounded-2xl object-cover border border-slate-200">
                    @else
                        <div class="w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-xl font-black border border-indigo-200">
                            {{ mb_substr($log->user->name, 0, 1) }}
                        </div>
                    @endif
                    <div>
                        <div class="font-black text-slate-800">{{ $log->user->name }}</div>
                        <div class="text-xs text-slate-400">{{ $log->user->email }}</div>
                    </div>
                </div>
                <a href="{{ route('admin.activity-logs.user', $log->user->id) }}"
                   class="w-full inline-flex justify-center items-center gap-2 px-4 py-2 text-xs font-bold rounded-xl bg-indigo-50 text-indigo-700 hover:bg-indigo-100 border border-indigo-100 transition-all">
                    📋 كل نشاطات هذا المستخدم
                </a>
                @else
                <span class="text-sm text-slate-400 italic">مستخدم محذوف</span>
                @endif
            </div>
        </div>

        {{-- Meta info --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                <h6 class="text-base font-black text-slate-800 m-0">ℹ️ معلومات إضافية</h6>
            </div>
            <div class="p-5 space-y-3 text-sm">
                <div class="flex justify-between items-start gap-3">
                    <span class="text-slate-400 font-semibold shrink-0">التاريخ</span>
                    <span class="font-bold text-slate-700 text-left">{{ $log->created_at->format('d/m/Y') }}</span>
                </div>
                <div class="flex justify-between items-start gap-3">
                    <span class="text-slate-400 font-semibold shrink-0">الوقت</span>
                    <span class="font-bold text-slate-700">{{ $log->created_at->format('H:i:s') }}</span>
                </div>
                <div class="flex justify-between items-start gap-3">
                    <span class="text-slate-400 font-semibold shrink-0">منذ</span>
                    <span class="text-slate-500 text-xs">{{ $log->created_at->diffForHumans() }}</span>
                </div>
                @if($log->ip_address)
                <div class="flex justify-between items-start gap-3">
                    <span class="text-slate-400 font-semibold shrink-0">IP</span>
                    <code class="text-xs font-mono text-slate-600">{{ $log->ip_address }}</code>
                </div>
                @endif
                @if($log->model_id)
                <div class="flex justify-between items-start gap-3">
                    <span class="text-slate-400 font-semibold shrink-0">رقم السجل</span>
                    <code class="text-xs font-mono text-slate-600">{{ $mEmoji }} #{{ $log->model_id }}</code>
                </div>
                @endif
            </div>
        </div>

    </div>

    {{-- ═══ LEFT: Main content ═══ --}}
    <div class="flex-1 min-w-0 flex flex-col gap-5">

        {{-- What happened --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <h6 class="text-base font-black text-slate-800 m-0">🎯 ماذا فعل المستخدم</h6>
                <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-black border {{ $aCls }}">
                    {{ $aEmoji }} {{ $aLbl }}
                </span>
            </div>
            <div class="p-6">
                {{-- Description --}}
                <div class="flex items-start gap-3 p-4 bg-slate-50 rounded-2xl border border-slate-100 mb-4">
                    <span class="text-2xl mt-0.5">{{ $aEmoji }}</span>
                    <div>
                        <p class="text-base font-black text-slate-800 m-0">
                            @if($isHttpLog)
                                {{ $httpDescription }}
                            @else
                                {{ $log->description }}
                            @endif
                        </p>
                        @if($log->model_type)
                        <p class="text-xs text-slate-400 font-semibold mt-1 m-0">
                            {{ $mEmoji }} {{ $mLabel }}
                            @if($log->model_id)
                                — رقم #{{ $log->model_id }}
                            @endif
                        </p>
                        @endif
                    </div>
                </div>

                {{-- User agent --}}
                @if($log->user_agent)
                <div class="text-xs text-slate-400 font-semibold truncate">
                    🖥 {{ $log->user_agent }}
                </div>
                @endif
            </div>
        </div>

        {{-- Changes diff --}}
        @if($log->changes && isset($log->changes['before'], $log->changes['after']))
        @php
            $before  = $log->changes['before'];
            $after   = $log->changes['after'];
            $allKeys = array_unique(array_merge(array_keys($before), array_keys($after)));
            $changed = array_filter($allKeys, fn($k) => ($before[$k] ?? null) !== ($after[$k] ?? null));
        @endphp
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-amber-50/50 flex items-center justify-between">
                <h6 class="text-base font-black text-slate-800 m-0">🔄 التغييرات التفصيلية</h6>
                <span class="text-xs font-bold text-amber-700 bg-amber-50 border border-amber-100 px-2.5 py-1 rounded-lg">
                    {{ count($changed) }} تغيير
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-right">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs font-bold">
                            <th class="px-5 py-3 w-1/4">الحقل</th>
                            <th class="px-5 py-3 w-5/12">
                                <span class="inline-flex items-center gap-1 text-rose-600">⬅️ قبل التعديل</span>
                            </th>
                            <th class="px-5 py-3 w-5/12">
                                <span class="inline-flex items-center gap-1 text-emerald-600">➡️ بعد التعديل</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($changed as $key)
                        @php
                            $oldVal = $before[$key] ?? null;
                            $newVal = $after[$key] ?? null;
                            $label  = $fieldNames[$key] ?? $key;
                            // Translate known status values
                            $oldDisplay = $statusLabels[(string)$oldVal] ?? (is_array($oldVal) ? json_encode($oldVal, JSON_UNESCAPED_UNICODE) : ($oldVal ?? '—'));
                            $newDisplay = $statusLabels[(string)$newVal] ?? (is_array($newVal) ? json_encode($newVal, JSON_UNESCAPED_UNICODE) : ($newVal ?? '—'));
                            // Mask password
                            if ($key === 'password') { $oldDisplay = '••••••••'; $newDisplay = '••••••••'; }
                        @endphp
                        <tr class="hover:bg-slate-50/40">
                            <td class="px-5 py-3">
                                <span class="text-sm font-black text-slate-700">{{ $label }}</span>
                                <code class="block text-[10px] text-slate-400 font-mono">{{ $key }}</code>
                            </td>
                            <td class="px-5 py-3">
                                <span class="inline-block text-sm font-semibold text-rose-700 bg-rose-50 rounded-lg px-2.5 py-1 max-w-xs truncate">
                                    {{ $oldDisplay }}
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                <span class="inline-block text-sm font-semibold text-emerald-700 bg-emerald-50 rounded-lg px-2.5 py-1 max-w-xs truncate">
                                    {{ $newDisplay }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @elseif($log->changes)
        {{-- Raw changes (no before/after structure) --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-amber-50/50">
                <h6 class="text-base font-black text-slate-800 m-0">🔄 البيانات المسجّلة</h6>
            </div>
            <div class="p-6">
                <pre class="bg-slate-50 border border-slate-100 rounded-2xl p-4 text-xs font-mono text-slate-700 overflow-x-auto whitespace-pre-wrap">@json($log->changes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)</pre>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection
