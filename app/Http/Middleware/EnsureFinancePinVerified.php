<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureFinancePinVerified
{
    /**
     * Handle an incoming request.
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

        // Verify that the user has a permitted role to access Kas & Bank / Petty Cash
        $allowedRoles = ['Finance Manager', 'Finance', 'Accounting', 'Admin', 'Developer'];
        if (!in_array($userRole, $allowedRoles) && !in_array($user->role, $allowedRoles) && !$user->isDeveloper()) {
            abort(403, 'Anda tidak memiliki otorisasi untuk mengakses modul keuangan ini.');
        }

        // Check if the current session already passed PIN verification
        if ($request->session()->get('finance_pin_verified') === true) {
            return $next($request);
        }

        // If AJAX / JSON API request
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status'   => 'pin_required',
                'message'  => 'PIN Keamanan Finance diperlukan untuk mengakses fitur ini.',
                'redirect' => route('finance.security.verify_form'),
            ], 403);
        }

        // Store intended URL to redirect back after entering PIN
        $request->session()->put('finance_pin_intended', $request->fullUrl());

        $previous = url()->previous();
        // If coming from another page within this application, bounce back and pop up the PIN modal seamlessly
        if ($previous && $previous !== $request->fullUrl() && !str_contains($previous, '/finance/security') && !str_contains($previous, '/login')) {
            $request->session()->flash('open_finance_pin_modal', true);
            return redirect($previous);
        }

        return redirect()->route('finance.security.verify_form');
    }
}
