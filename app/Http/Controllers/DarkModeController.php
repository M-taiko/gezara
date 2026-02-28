<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DarkModeController extends Controller
{
    /**
     * Toggle dark mode for the user.
     */
    public function toggle(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $mode = $request->input('mode');

        if (!in_array($mode, ['light', 'dark'])) {
            return response()->json(['success' => false, 'message' => 'Invalid mode'], 400);
        }

        $user->update(['dark_mode' => $mode]);

        return response()->json(['success' => true, 'message' => 'Dark mode toggled successfully!']);
    }
}
