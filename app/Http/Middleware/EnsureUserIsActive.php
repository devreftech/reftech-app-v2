<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsActive
{
    /**
     * Logout paksa user yang sedang login (termasuk yang masuk lewat
     * cookie "remember me") bila akunnya sudah dinonaktifkan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && (string) $user->active !== '1') {
            $message = 'Akun Anda sudah dinonaktifkan. Silakan hubungi Admin.';

            // Navigasi halaman (buka/refresh/submit form) vs request latar belakang
            // (polling notifikasi, DataTables, fetch). Browser lama tanpa header
            // Sec-Fetch-Mode: jatuh kembali ke expectsJson().
            $isNavigation = $request->headers->has('Sec-Fetch-Mode')
                ? $request->header('Sec-Fetch-Mode') === 'navigate'
                : ! $request->expectsJson();

            // Request latar belakang: cukup ditolak, sesi TIDAK disentuh,
            // supaya logout + pesan terjadi di navigasi berikutnya dan pesannya
            // pasti tampil ke user.
            if (! $isNavigation) {
                return response()->json(['message' => $message], 401);
            }

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors(['email' => $message]);
        }

        return $next($request);
    }
}
