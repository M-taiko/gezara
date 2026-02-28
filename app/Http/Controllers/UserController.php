<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    /**
     * Display a listing of all users.
     */
    public function index()
    {
        $users = User::with('roles', 'profile')
            ->paginate(10);

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $roles = Role::all();

        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:active,banned,inactive',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'status' => $validated['status'],
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->profile()->update(['avatar' => 'storage/' . $avatarPath]);
        }

        // Assign role to user
        $user->roles()->attach($validated['role_id']);

        // Log activity
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'created',
            'model_type' => 'App\Models\User',
            'model_id' => $user->id,
            'description' => "Created new user: {$user->name}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully!');
    }

    /**
     * Display the specified user.
     */
    public function show($id)
    {
        $user = User::with('roles', 'profile')->findOrFail($id);
        $activityLogs = ActivityLog::where('user_id', $id)->recent()->paginate(10);

        return view('users.show', compact('user', 'activityLogs'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit($id)
    {
        $user = User::with('roles', 'profile')->findOrFail($id);
        $roles = Role::all();

        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:users,email,{$id}",
            'password' => 'nullable|string|min:6|confirmed',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:active,banned,inactive',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $oldData = $user->only(['name', 'email', 'status']);

        // Update user
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'status' => $validated['status'],
        ]);

        if ($validated['password'] ?? false) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $profile = $user->profile;
            if ($profile->avatar && file_exists(public_path($profile->avatar))) {
                unlink(public_path($profile->avatar));
            }
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $profile->update(['avatar' => 'storage/' . $avatarPath]);
        }

        // Update role
        $user->roles()->sync([$validated['role_id']]);

        // Log activity
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated',
            'model_type' => 'App\Models\User',
            'model_id' => $user->id,
            'description' => "Updated user: {$user->name}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'changes' => [
                'before' => $oldData,
                'after' => $user->only(['name', 'email', 'status']),
            ],
        ]);

        return redirect()->route('users.show', $id)->with('success', 'User updated successfully!');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Don't allow deleting yourself
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'You cannot delete your own account!');
        }

        // Don't allow deleting the last admin
        if ($user->isAdmin() && User::where('status', 'active')->whereHas('roles', function ($q) {
            $q->where('name', 'admin');
        })->count() <= 1) {
            return redirect()->back()->with('error', 'Cannot delete the last administrator!');
        }

        $userName = $user->name;

        // Delete related data
        $user->roles()->detach();
        if ($user->profile) {
            $user->profile()->delete();
        }

        // Log activity
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'deleted',
            'model_type' => 'App\Models\User',
            'model_id' => $user->id,
            'description' => "Deleted user: {$userName}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully!');
    }

    /**
     * Update the user's status (active, banned, inactive).
     */
    public function updateStatus(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:active,banned,inactive',
        ]);

        $oldStatus = $user->status;

        $user->update(['status' => $validated['status']]);

        // Log activity
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated',
            'model_type' => 'App\Models\User',
            'model_id' => $user->id,
            'description' => "Changed user status from {$oldStatus} to {$validated['status']}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'changes' => [
                'before' => ['status' => $oldStatus],
                'after' => ['status' => $validated['status']],
            ],
        ]);

        return redirect()->back()->with('success', 'User status updated successfully!');
    }

    /**
     * Export users to Excel.
     */
    public function export()
    {
        return Excel::download(new \App\Exports\UsersExport(), 'users_' . date('Y-m-d_H-i-s') . '.xlsx');
    }
}
