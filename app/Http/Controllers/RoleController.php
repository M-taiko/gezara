<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    /**
     * Display a listing of all roles.
     */
    public function index()
    {
        $roles = Role::withCount('users')->paginate(10);

        return view('roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        return view('roles.create');
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles|max:255',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        $role = Role::create($validated);

        // Log activity
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'created',
            'model_type' => 'App\Models\Role',
            'model_id' => $role->id,
            'description' => "Created new role: {$role->display_name}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.roles.index')->with('toast_success', 'تم إنشاء الدور بنجاح.');
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit($id)
    {
        $role = Role::with('users')->findOrFail($id);

        return view('roles.edit', compact('role'));
    }

    /**
     * Update the specified role in storage.
     */
    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        // Prevent modifying default roles
        if (in_array($role->name, ['admin', 'manager', 'user'])) {
            return redirect()->back()->with('error', 'Cannot modify default roles!');
        }

        $validated = $request->validate([
            'name' => "required|string|unique:roles,name,{$id}|max:255",
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        $oldData = $role->only(['name', 'display_name', 'description']);

        $role->update($validated);

        // Log activity
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated',
            'model_type' => 'App\Models\Role',
            'model_id' => $role->id,
            'description' => "Updated role: {$role->display_name}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'changes' => [
                'before' => $oldData,
                'after' => $role->only(['name', 'display_name', 'description']),
            ],
        ]);

        return redirect()->route('admin.roles.index')->with('toast_success', 'تم تحديث الدور بنجاح.');
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        // Prevent deleting default roles
        if (in_array($role->name, ['admin', 'manager', 'user'])) {
            return redirect()->back()->with('error', 'Cannot delete default roles!');
        }

        // Prevent deleting roles that have users
        if ($role->users()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete a role that has users assigned!');
        }

        $roleName = $role->display_name;

        // Log activity before deletion
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'deleted',
            'model_type' => 'App\Models\Role',
            'model_id' => $role->id,
            'description' => "Deleted role: {$roleName}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $role->delete();

        return redirect()->route('admin.roles.index')->with('toast_success', 'تم حذف الدور.');
    }

    /**
     * Assign a role to a user.
     */
    public function assignRole(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        $role = Role::findOrFail($validated['role_id']);

        // Check if user already has this role
        if ($user->roles()->where('role_id', $role->id)->exists()) {
            return redirect()->back()->with('warning', 'User already has this role!');
        }

        // Detach all other roles and attach the new one
        $user->roles()->sync([$role->id]);

        // Log activity
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated',
            'model_type' => 'App\Models\User',
            'model_id' => $user->id,
            'description' => "Assigned role '{$role->display_name}' to user {$user->name}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->back()->with('success', 'Role assigned successfully!');
    }
}
