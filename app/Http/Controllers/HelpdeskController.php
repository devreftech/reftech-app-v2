<?php

namespace App\Http\Controllers;

use App\Models\HelpdeskTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HelpdeskController extends Controller
{
    public function index()
    {
        return view('pages.helpdesk.index');
    }

    public function store(Request $request)
    {
        $ticket = new HelpdeskTicket;
        $ticket->no_ticket = $this->generateNoTicket();
        $ticket->id_user = Auth::id();
        $ticket->title = $request->title;
        $ticket->description = $request->description;
        $ticket->status = 'Open';
        $ticket->save();

        return redirect('/helpdesk')->with('message', 'Tiket telah dibuat');
    }

    public function updateStatus(Request $request, $id)
    {
        if (Auth::user()->role != 'Admin') {
            return 0;
        }

        $ticket = HelpdeskTicket::find($id);
        $ticket->status = $request->status;
        if ($request->filled('note')) {
            $ticket->resolution_note = $request->note;
        }
        $saved = $ticket->save();

        return $saved ? 1 : 0;
    }

    private function generateNoTicket(): string
    {
        $year = now()->format('Y');
        $month = now()->format('m');
        $prefix = "TKT/{$year}/{$month}/";

        $last = HelpdeskTicket::where('no_ticket', 'like', $prefix . '%')
            ->orderByDesc('no_ticket')
            ->value('no_ticket');

        $lastSeq = $last ? (int) substr($last, -3) : 0;
        $nextSeq = str_pad($lastSeq + 1, 3, '0', STR_PAD_LEFT);

        return $prefix . $nextSeq;
    }
}
