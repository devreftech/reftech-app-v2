<?php

namespace App\Http\Controllers;

use App\Models\FixedAsset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ToolFinanceController extends Controller
{
    public function index(Request $request)
    {
        if (!in_array(Auth::user()->role, ['Finance Manager', 'Finance', 'Admin'])) {
            abort(403, 'Hanya Finance / Finance Manager / Admin yang bisa mengakses halaman ini.');
        }

        $status = $request->get('status', 'belum');

        $query = FixedAsset::where('type', 'Tools')->with(['toolsMaster', 'pic', 'aktiva']);

        if ($status == 'belum') {
            $query->whereNull('id_aktiva');
        } else {
            $query->whereNotNull('id_aktiva');
        }

        $tools = $query->orderByDesc('id')->get();

        $countBelum = FixedAsset::where('type', 'Tools')->whereNull('id_aktiva')->count();
        $countSudah = FixedAsset::where('type', 'Tools')->whereNotNull('id_aktiva')->count();

        return view('pages.finance.tool-finance.index', compact('tools', 'status', 'countBelum', 'countSudah'));
    }
}
