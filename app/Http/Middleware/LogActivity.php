<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ActivityLog;

class LogActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only log for authenticated users
        if (Auth::check()) {
            // Determine the action based on HTTP method
            $action = $this->getActionFromMethod($request->method(), $request->path());

            // Skip logging for certain routes
            if (!$this->shouldSkipLogging($request)) {
                ActivityLog::create([
                    'user_id' => Auth::id(),
                    'action' => $action,
                    'description' => "{$request->method()} {$request->path()}",
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }
        }

        return $response;
    }

    /**
     * Determine the action type based on HTTP method.
     */
    private function getActionFromMethod(string $method, string $path): string
    {
        return match ($method) {
            'GET' => 'viewed',
            'POST' => 'created',
            'PUT', 'PATCH' => 'updated',
            'DELETE' => 'deleted',
            default => 'accessed',
        };
    }

    /**
     * Check if this route should be skipped from logging.
     */
    private function shouldSkipLogging(Request $request): bool
    {
        // Skip logging for certain paths to avoid noise
        $skipPaths = [
            'admin/activity-logs', // Don't log viewing activity logs
            '/logout', // Logout is logged separately
        ];

        $path = $request->path();

        foreach ($skipPaths as $skipPath) {
            if (str_contains($path, $skipPath)) {
                return true;
            }
        }

        return false;
    }
}
