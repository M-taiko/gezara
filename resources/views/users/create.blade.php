@extends('layouts.master')

@section('page-header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8 mt-2">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
            <span class="text-indigo-600 text-4xl">➕</span> مستخدم جديد
        </h1>
        <p class="text-slate-500 font-medium text-sm mt-1">
            <a href="{{ route('admin.users.index') }}" class="text-indigo-500 hover:text-indigo-700 hover:underline">المستخدمون</a>
            / إضافة
        </p>
    </div>
    <a href="{{ route('admin.users.index') }}"
       class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 transition-all">
        ← العودة
    </a>
</div>
@endsection

@section('content')
<form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data"
      class="flex flex-col lg:flex-row-reverse gap-6 pb-16">
    @csrf

    {{-- RIGHT: Meta sidebar --}}
    <div class="w-full lg:w-72 flex-shrink-0 flex flex-col gap-5">

        {{-- Avatar --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                <h6 class="text-base font-black text-slate-800 m-0">📷 الصورة الشخصية</h6>
            </div>
            <div class="p-5 flex flex-col items-center gap-4">
                <div id="avatarWrap" class="w-24 h-24 rounded-2xl bg-indigo-50 border-2 border-indigo-100 border-dashed flex items-center justify-center overflow-hidden">
                    <img id="avatarPreview" src="#" alt="" class="w-full h-full object-cover hidden">
                    <span id="avatarPlaceholder" class="text-4xl">👤</span>
                </div>
                <label for="avatarInput"
                       class="cursor-pointer inline-flex items-center gap-2 px-4 py-2 text-xs font-bold rounded-xl bg-indigo-50 text-indigo-700 hover:bg-indigo-100 border border-indigo-100 transition-all">
                    📁 اختر صورة
                </label>
                <input type="file" name="avatar" id="avatarInput" accept="image/*" class="hidden">
                <p class="text-xs text-slate-400 text-center">JPEG, PNG, JPG — بحد أقصى 2MB</p>
                @error('avatar')
                <p class="text-rose-500 text-xs font-bold">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Role + Status --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                <h6 class="text-base font-black text-slate-800 m-0">🔑 الدور والحالة</h6>
            </div>
            <div class="p-5 space-y-4">
                <div>
                    <label class="block text-xs font-black text-slate-600 mb-1.5">الدور <span class="text-rose-500">*</span></label>
                    <select name="role_id" required
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                        <option value="">— اختر الدور —</option>
                        @php
                        $roleIcons = ['owner'=>'👑','accountant'=>'💼','seller'=>'🏪','admin'=>'🛡','manager'=>'⚙️','user'=>'👤'];
                        @endphp
                        @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                            {{ ($roleIcons[$role->name] ?? '👤') . ' ' . $role->display_name }}
                        </option>
                        @endforeach
                    </select>
                    @error('role_id')
                    <p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-black text-slate-600 mb-1.5">الحالة <span class="text-rose-500">*</span></label>
                    <select name="status" required
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                        <option value="active"   {{ old('status','active') === 'active'   ? 'selected' : '' }}>✅ نشط</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>⏸ غير نشط</option>
                        <option value="banned"   {{ old('status') === 'banned'   ? 'selected' : '' }}>🚫 محظور</option>
                    </select>
                    @error('status')
                    <p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <button type="submit"
                class="w-full inline-flex justify-center items-center gap-2 px-5 py-3 text-sm font-black rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 shadow-md shadow-indigo-200/60 transition-all">
            ✅ حفظ المستخدم
        </button>
    </div>

    {{-- LEFT: Main form --}}
    <div class="flex-1 min-w-0 flex flex-col gap-5">

        {{-- Personal info --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                <h6 class="text-base font-black text-slate-800 m-0">👤 البيانات الشخصية</h6>
            </div>
            <div class="p-5 space-y-4">
                <div>
                    <label class="block text-xs font-black text-slate-600 mb-1.5">الاسم الكامل <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           placeholder="اسم المستخدم"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                    @error('name')
                    <p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-black text-slate-600 mb-1.5">البريد الإلكتروني <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           placeholder="email@example.com"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors"
                           dir="ltr">
                    @error('email')
                    <p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Password --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                <h6 class="text-base font-black text-slate-800 m-0">🔒 كلمة المرور</h6>
            </div>
            <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-black text-slate-600 mb-1.5">كلمة المرور <span class="text-rose-500">*</span></label>
                    <input type="password" name="password" required
                           placeholder="6 أحرف على الأقل"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors"
                           dir="ltr">
                    @error('password')
                    <p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-black text-slate-600 mb-1.5">تأكيد كلمة المرور <span class="text-rose-500">*</span></label>
                    <input type="password" name="password_confirmation" required
                           placeholder="أعد كتابة كلمة المرور"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors"
                           dir="ltr">
                </div>
            </div>
        </div>

    </div>
</form>
@endsection

@push('js')
<script>
document.getElementById('avatarInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = ev => {
        const preview = document.getElementById('avatarPreview');
        preview.src = ev.target.result;
        preview.classList.remove('hidden');
        document.getElementById('avatarPlaceholder').classList.add('hidden');
    };
    reader.readAsDataURL(file);
});
</script>
@endpush
