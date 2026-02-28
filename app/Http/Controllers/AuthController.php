<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Profile;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showSignup()
    {
        return view('signup');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'status' => 'active',
        ]);

        // Create empty profile for the user
        Profile::create([
            'user_id' => $user->id,
        ]);

        // Log activity
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'created',
            'model_type' => 'App\Models\User',
            'model_id' => $user->id,
            'description' => 'User registered',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        Auth::login($user);

        return redirect()->route('udhiya.dashboard')->with('success', 'Account created successfully!');
    }

    public function showSignin()
    {
        return view('signin');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            // Check if user account is banned
            if ($user->isBanned()) {
                Auth::logout();
                return back()
                    ->withInput($request->only('email'))
                    ->withErrors(['email' => 'Your account has been banned. Please contact administrator.']);
            }

            // Check if user account is inactive
            if ($user->status === 'inactive') {
                Auth::logout();
                return back()
                    ->withInput($request->only('email'))
                    ->withErrors(['email' => 'Your account is inactive. Please contact administrator.']);
            }

            $request->session()->regenerate();

            // Refresh user with profile loaded
            $user->load('profile');

            // Log activity
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'login',
                'model_type' => 'App\Models\User',
                'model_id' => $user->id,
                'description' => 'User logged in',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return redirect()->route('udhiya.dashboard')->with('success', 'Logged in successfully!');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'Invalid credentials']);
    }

    public function logout(Request $request)
    {
        $userId = Auth::id();

        // Log activity before logout
        if ($userId) {
            ActivityLog::create([
                'user_id' => $userId,
                'action' => 'logout',
                'model_type' => 'App\Models\User',
                'model_id' => $userId,
                'description' => 'User logged out',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/signin')->with('success', 'Logged out successfully!');
    }
}
