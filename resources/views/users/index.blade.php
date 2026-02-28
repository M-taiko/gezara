@extends("layouts.master")

@section("page-header")
<div class="page-header">
    <div class="page-leftheader">
        <h4 class="page-title"><span class="page-title-emoji">👤</span> إدارة المستخدمين</h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('udhiya.dashboard') }}">الرئيسية</a></li>
            <li class="breadcrumb-item active">المستخدمون</li>
        </ol>
    </div>
    <div class="page-rightheader">
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            ➕ إضافة مستخدم
        </a>
    </div>
</div>
@endsection

@section("content")

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>👥 قائمة المستخدمين</span>
        <span class="badge badge-primary">{{ $users->total() }} مستخدم</span>
    </div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>الصورة</th>
                    <th>الاسم</th>
                    <th>البريد الإلكتروني</th>
                    <th>الدور</th>
                    <th>الحالة</th>
                    <th>تاريخ التسجيل</th>
                    <th class="text-center">إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td style="color:var(--text-muted);font-size:.82rem;">{{ $user->id }}</td>
                    <td>
                        @if($user->profile && $user->profile->avatar)
                            <img src="{{ asset($user->profile->avatar) }}" alt="avatar"
                                 style="width:38px;height:38px;border-radius:50%;object-fit:cover;border:2px solid var(--primary);">
                        @else
                            <div style="width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--accent));display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:.95rem;">
                                {{ mb_substr($user->name,0,1) }}
                            </div>
                        @endif
                    </td>
                    <td><strong>{{ $user->name }}</strong></td>
                    <td style="font-size:.85rem;color:var(--text-muted);">{{ $user->email }}</td>
                    <td>
                        @forelse($user->roles as $role)
                            <span class="badge badge-primary">{{ $role->display_name }}</span>
                        @empty
                            <span class="badge badge-secondary">بدون دور</span>
                        @endforelse
                    </td>
                    <td>
                        @if($user->status === "active")
                            <span class="badge badge-success">✅ نشط</span>
                        @elseif($user->status === "banned")
                            <span class="badge badge-danger">🚫 محظور</span>
                        @else
                            <span class="badge badge-warning">⏸️ غير نشط</span>
                        @endif
                    </td>
                    <td style="font-size:.82rem;color:var(--text-muted);">{{ $user->created_at->format("Y/m/d") }}</td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-info btn-action" title="عرض">👁️</a>
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-warning btn-action" title="تعديل">✏️</a>
                            @if($user->id !== Auth::id())
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('هل تريد حذف هذا المستخدم؟')">
                                @csrf @method("DELETE")
                                <button type="submit" class="btn btn-sm btn-danger btn-action" title="حذف">🗑️</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="empty-state">
                            <span class="empty-icon">👤</span>
                            <p>لا يوجد مستخدمون</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="card-footer">{{ $users->links() }}</div>
    @endif
</div>

@endsection
