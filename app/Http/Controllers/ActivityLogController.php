<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(Auth::user()?->isDeveloper(), 403);

        // Default to "today" so the datatable never has to scan the whole
        // activity_logs history at once — user picks another date to look back.
        $date = $request->filled('date')
            ? Carbon::parse($request->date, 'Asia/Jakarta')
            : Carbon::now('Asia/Jakarta');

        $query = ActivityLog::with('user')
            ->whereBetween('created_at', [
                $date->clone()->startOfDay(),
                $date->clone()->endOfDay(),
            ])
            ->latest();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        $logs = $query->paginate(25)->withQueryString();
        $users = User::orderBy('name')->get(['id', 'name', 'role']);

        return view('pages.activity_log.index', [
            'logs' => $logs,
            'users' => $users,
            'date' => $date,
        ]);
    }
}
