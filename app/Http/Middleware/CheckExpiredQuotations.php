<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Quotation;

class CheckExpiredQuotations
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && !session()->has('expired_checked')) {
            $quote = Quotation::where('id_sales', Auth::user()->id)->get();
            foreach ($quote as $item) {
                if ($item->expired_date < Carbon::today() && $item->status != 100) {
                    $item->status = '0';
                    $item->save();
                }
            }

            session(['expired_checked' => true]); // Cegah pengecekan berulang dalam sesi yang sama
        }

        return $next($request);
    }
}
