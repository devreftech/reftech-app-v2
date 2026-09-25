<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RestrictClientVendorAccess
{
    /**
     * Whitelist URI patterns yang diizinkan untuk role Client Vendor.
     *
     * @var array<int, string>
     */
    protected array $allowedUris = [
        '/',
        'dashboard',
        'dashboard/*',
        'home',
        'home/*',
        'project-reports',
        'project-reports/*',
        'project-report',
        'project-report/*',
        'db/project-reports',
        'db/project-reports/*',
        'api/project-reports',
        'api/project-reports/*',
        'profile',
        'profile/*',
        'logout',
        'logout/*',
        'password/*',
        'assets/*',
        'build/*',
        'storage/*',
        'favicon.ico',
        'change-dashboard-view',
        'change-dashboard-view/*',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Jika belum login, serahkan ke middleware auth
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        $userRole = $user->getRawOriginal('role') ?? $user->role;

        // Hanya terapkan pembatasan jika role adalah Client Vendor
        // Kecualikan jika user adalah Developer yang sedang switch view
        if ($userRole !== 'Client Vendor' || (method_exists($user, 'isDeveloper') && $user->isDeveloper())) {
            return $next($request);
        }

        // Periksa apakah URL saat ini ada di whitelist
        foreach ($this->allowedUris as $pattern) {
            if ($request->is($pattern)) {
                return $next($request);
            }
        }

        // Jika request via AJAX / API / JSON
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Akun Client Vendor hanya diizinkan mengakses Dashboard dan Daily Project Report.',
            ], 403);
        }

        // Jika akses langsung via browser URL, redirect ke dashboard dengan pesan error
        return redirect('/')->with('error', 'Akses dibatasi. Akun Client Vendor hanya diizinkan mengakses Dashboard dan Daily Project Report.');
    }
}
