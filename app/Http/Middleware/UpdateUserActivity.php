<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class UpdateUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $userId = Auth::id();
            // Store current timestamp for online presence check (valid for 2 days in cache)
            Cache::put('user_last_activity_' . $userId, now()->timestamp, now()->addDays(2));

            $this->logPageVisit($request, $userId);
        }

        return $next($request);
    }

    /**
     * Record every real page navigation (full GET load) so admin/developer can
     * see which pages a user actually opened, not just which transactions
     * they performed. AJAX/JSON calls (datatables, polling, API) are skipped
     * on purpose so the log stays readable and isn't flooded by background calls.
     */
    protected function logPageVisit(Request $request, $userId)
    {
        if (!$request->isMethod('get') || $request->ajax() || $request->wantsJson() || $request->pjax()) {
            return;
        }

        $route = $request->route();
        $routeName = $route ? $route->getName() : null;

        // Skip the activity-log page itself and asset-like/auth routes to avoid noise.
        if ($routeName && in_array($routeName, ['activity-log.index', 'logout'])) {
            return;
        }

        try {
            ActivityLog::create([
                'user_id'     => $userId,
                'type'        => 'page_visit',
                'action'      => 'visited',
                'description' => 'Membuka halaman: ' . $this->resolvePageTitle($request, $routeName),
                'properties'  => [
                    'route' => $routeName,
                    'url'   => $request->fullUrl(),
                ],
                'ip_address'  => $request->ip(),
                'user_agent'  => $request->userAgent(),
            ]);
        } catch (\Throwable $e) {
            \Log::error('Failed recording page visit log: ' . $e->getMessage());
        }
    }

    protected function resolvePageTitle(Request $request, ?string $routeName): string
    {
        if ($routeName) {
            return ucwords(str_replace(['.', '-', '_'], ' ', $routeName));
        }

        return $request->path();
    }
}
