<?php

namespace App\Http\Middleware;

use App\Services\MaintenanceService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeveloperMaintenanceMiddleware
{
    /**
     * URIs that should be accessible even when maintenance mode is active.
     *
     * @var array<int, string>
     */
    protected array $except = [
        'maintenance*',
        'api/maintenance/*',
        'developer/maintenance*',
        'login*',
        'logout*',
        'password/*',
        'assets/*',
        'build/*',
        'favicon.ico',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // 1. If requesting maintenance page directly, never block or redirect
        if ($request->is('maintenance*') || $request->routeIs('maintenance.page')) {
            return $next($request);
        }

        // 2. If maintenance mode is NOT active, proceed normally
        if (!MaintenanceService::isActive()) {
            return $next($request);
        }

        // 3. Allow whitelisted routes
        foreach ($this->except as $except) {
            if ($request->is($except)) {
                return $next($request);
            }
        }

        // 4. If user is logged in as Developer, allow complete unrestricted access
        if (Auth::check() && Auth::user()->isDeveloper()) {
            return $next($request);
        }

        // 5. If request expects JSON or is AJAX, return clean 503 response
        if ($request->expectsJson() || $request->ajax()) {
            $details = MaintenanceService::getDetails();
            return response()->json([
                'maintenance' => true,
                'message' => $details['message'] ?? 'Sistem sedang dalam proses pemeliharaan.',
                'end_time' => $details['end_time'] ?? null,
            ], 503);
        }

        // 6. Redirect all non-developers and guests to the maintenance page
        return redirect()->route('maintenance.page');
    }
}
