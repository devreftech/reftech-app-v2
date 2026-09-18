<?php

namespace App\Http\Controllers;

use App\Models\HelpdeskTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HelpdeskController extends Controller
{
    public function index()
    {
        $isAdmin = in_array(Auth::user()->role, ['Admin', 'Developer', 'Super Admin']);

        if ($isAdmin) {
            $userTicketsQuery = HelpdeskTicket::where('no_ticket', 'not like', 'ERR/%');
            $errorTicketsQuery = HelpdeskTicket::where('no_ticket', 'like', 'ERR/%');

            $stats = [
                'total_user' => (clone $userTicketsQuery)->count(),
                'open_user' => (clone $userTicketsQuery)->where('status', 'Open')->count(),
                'progress_user' => (clone $userTicketsQuery)->where('status', 'In Progress')->count(),
                'resolved_user' => (clone $userTicketsQuery)->where('status', 'Resolved')->count(),
                'total_errors' => (clone $errorTicketsQuery)->count(),
                'open_errors' => (clone $errorTicketsQuery)->where('status', 'Open')->count(),
                'resolved_errors' => (clone $errorTicketsQuery)->where('status', 'Resolved')->count(),
            ];
        } else {
            $userTicketsQuery = HelpdeskTicket::where('id_user', Auth::id())
                ->where('no_ticket', 'not like', 'ERR/%');

            $stats = [
                'total_user' => (clone $userTicketsQuery)->count(),
                'open_user' => (clone $userTicketsQuery)->where('status', 'Open')->count(),
                'progress_user' => (clone $userTicketsQuery)->where('status', 'In Progress')->count(),
                'resolved_user' => (clone $userTicketsQuery)->where('status', 'Resolved')->count(),
                'total_errors' => 0,
                'open_errors' => 0,
                'resolved_errors' => 0,
            ];
        }

        return view('pages.helpdesk.index', compact('stats', 'isAdmin'));
    }

    public function urgentCheck()
    {
        if (!Auth::check()) {
            return response()->json(['has_urgent' => false]);
        }

        $user = Auth::user();
        if (!in_array($user->role, ['Admin', 'Developer', 'Super Admin'])) {
            return response()->json(['has_urgent' => false]);
        }

        // Cari tiket kendala dari user yang berstatus 'Open' (bukan error sistem otomatis ERR/...)
        $ticket = HelpdeskTicket::leftJoin('users as u', 'u.id', '=', 'helpdesk_tickets.id_user')
            ->where('helpdesk_tickets.no_ticket', 'not like', 'ERR/%')
            ->where('helpdesk_tickets.status', 'Open')
            ->orderBy('helpdesk_tickets.id', 'desc')
            ->select([
                'helpdesk_tickets.*',
                'u.name as requester_name',
                'u.image as requester_image',
                'u.role as requester_role',
            ])
            ->first();

        if (!$ticket) {
            return response()->json(['has_urgent' => false]);
        }

        $createdAtTz = $ticket->created_at ? $ticket->created_at->clone()->timezone('Asia/Jakarta') : null;
        $timeAgo = $createdAtTz ? $createdAtTz->diffForHumans() : 'Baru saja';
        $timeFormatted = $createdAtTz ? $createdAtTz->format('H:i, d M Y') : '-';

        return response()->json([
            'has_urgent' => true,
            'ticket' => [
                'id'              => (string) $ticket->id,
                'no_ticket'       => $ticket->no_ticket,
                'title'           => $ticket->title,
                'category'        => $ticket->category ?? 'user_report',
                'url_accessed'    => $ticket->url_accessed,
                'description'     => \Illuminate\Support\Str::limit((string) ($ticket->description ?? ''), 250),
                'requester_name'  => $ticket->requester_name ?? 'User',
                'requester_role'  => $ticket->requester_role ?? 'Staff',
                'requester_image' => $ticket->requester_image ? asset($ticket->requester_image) : null,
                'created_at'      => $timeAgo,
                'created_at_time' => $timeFormatted,
                'action_url'      => url('/helpdesk'),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $ticket = new HelpdeskTicket;
        $ticket->no_ticket = $this->generateNoTicket();
        $ticket->id_user = Auth::id();
        $ticket->category = 'user_report';
        $ticket->title = $request->title;
        $ticket->url_accessed = $request->url_accessed ?: $request->link ?: $request->url;
        $ticket->description = $request->description;
        $ticket->status = 'Open';
        $ticket->save();

        return redirect('/helpdesk')->with('message', 'Tiket telah dibuat');
    }

    public function updateStatus(Request $request, $id)
    {
        if (!in_array(Auth::user()->role, ['Admin', 'Developer', 'Super Admin'])) {
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

        $last = HelpdeskTicket::where('no_ticket', 'like', $prefix.'%')
            ->orderByDesc('no_ticket')
            ->value('no_ticket');

        $lastSeq = $last ? (int) substr($last, -3) : 0;
        $nextSeq = str_pad($lastSeq + 1, 3, '0', STR_PAD_LEFT);

        return $prefix.$nextSeq;
    }
}
