<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of all activity logs.
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->recent();

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by model type
        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->dateBetween($request->start_date, $request->end_date);
        }

        $logs = $query->paginate(20);
        $users = User::select('id', 'name')->whereNotIn('email', User::HIDDEN_EMAILS)->orderBy('name')->get();

        return view('activity-logs.index', compact('logs', 'users'));
    }

    /**
     * Display the specified activity log.
     */
    public function show($id)
    {
        $log = ActivityLog::with('user.profile')->findOrFail($id);

        return view('activity-logs.show', compact('log'));
    }

    /**
     * Display activity logs for a specific user.
     */
    public function userActivity($userId)
    {
        $user = User::findOrFail($userId);
        $logs = ActivityLog::forUser($userId)->recent()->paginate(20);

        return view('activity-logs.user', compact('user', 'logs'));
    }

    /**
     * Export activity logs to Excel.
     */
    public function export(Request $request)
    {
        return Excel::download(new \App\Exports\ActivityLogsExport(), 'activity-logs_' . date('Y-m-d_H-i-s') . '.xlsx');
    }
}
