<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureHrAccess
{
    /**
     * Handle an incoming request.
     * Hanya role Admin, Developer / Development, Finance, Finance Manager, dan Accounting
     * yang diizinkan mengakses modul manajemen HR.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->guest(route('login'));
        }

        $user = Auth::user();
        $userRole = $user->getRawOriginal('role') ?? $user->role;

        $allowedRoles = ['Admin', 'Developer', 'developer', 'development', 'Development', 'Finance', 'Finance Manager', 'Accounting'];

        if (in_array($userRole, $allowedRoles) || in_array($user->role, $allowedRoles) || ($user && method_exists($user, 'isDeveloper') && $user->isDeveloper())) {
            return $next($request);
        }

        abort(403, 'Akses terbatas: Halaman HR Management hanya dapat diakses oleh Admin, Development, dan Finance.');
    }
}
