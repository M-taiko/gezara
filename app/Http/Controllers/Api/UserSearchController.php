<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserSearchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Search for users by name or email
     */
    public function search(Request $request)
    {
        $query = $request->query('q', '');
        $authUser = Auth::user();

        $userQuery = User::where('id', '!=', $authUser->id)
            ->with('profile');

        // If query is provided, filter by name or email
        if (strlen($query) > 0) {
            $userQuery->where(function ($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%')
                  ->orWhere('email', 'like', '%' . $query . '%');
            });
        }

        $users = $userQuery->limit(10)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'status' => $user->status,
                    'profile' => $user->profile,
                ];
            });

        return response()->json(['users' => $users]);
    }

    /**
     * Get single user details
     */
    public function show($userId)
    {
        $authUser = Auth::user();

        if ($authUser->id === (int)$userId) {
            abort(403, 'Cannot view own profile');
        }

        $user = User::with('profile')->findOrFail($userId);

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'status' => $user->status,
                'profile' => $user->profile,
            ]
        ]);
    }
}
