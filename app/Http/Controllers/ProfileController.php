<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show()
    {
        $user = Auth::user();
        $profile = $user->profile;
        return view('profile', compact('user', 'profile'));
    }

    public function edit()
    {
        $user = Auth::user();
        $profile = $user->profile;
        return view('editprofile', compact('user', 'profile'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $profile = $user->profile;

        // Create profile if it doesn't exist
        if (!$profile) {
            $profile = $user->profile()->create([]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:500',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            if ($profile->avatar && file_exists(public_path($profile->avatar))) {
                unlink(public_path($profile->avatar));
            }
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = 'storage/' . $avatarPath;
        }

        // Update user info
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        // Update profile info
        $profile->update([
            'phone' => $validated['phone'] ?? null,
            'bio' => $validated['bio'] ?? null,
            'address' => $validated['address'] ?? null,
            'city' => $validated['city'] ?? null,
            'country' => $validated['country'] ?? null,
            'job_title' => $validated['job_title'] ?? null,
            'avatar' => $validated['avatar'] ?? $profile->avatar,
        ]);

        return redirect('/profile')->with('success', 'Profile updated successfully!');
    }
}
