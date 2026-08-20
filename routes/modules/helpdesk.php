<?php

use App\Http\Controllers\HelpdeskController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\NotulenController;
use App\Models\HelpdeskTicket;
use App\Models\Notulen;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    // Library
    Route::resource('/library', LibraryController::class);
    Route::get('/library/index/marktool', [LibraryController::class, 'index_marktool'])->name('marktool.index');
    Route::get('/library/index/brosur', [LibraryController::class, 'index_brosur'])->name('brosur.index');
    Route::get('/library/index/partlist', [LibraryController::class, 'index_partlist'])->name('partlist.index');
    Route::get('/library/index/manbook', [LibraryController::class, 'index_manbook'])->name('manbook.index');
    Route::post('/library/store/marktool', [LibraryController::class, 'store_marktool'])->name('store_marktool.library');
    Route::post('/library/store/brosur', [LibraryController::class, 'store_brosur'])->name('store_brosur.library');
    Route::post('/library/store/partlist', [LibraryController::class, 'store_partlist'])->name('store_partlist.library');
    Route::post('/library/store/manbook', [LibraryController::class, 'store_manbook'])->name('store_manbook.library');

    // Notulen
    Route::resource('/notulen', NotulenController::class);
    Route::get('/db/notulen/mention', function () {
        $notulen = Notulen::join('mention_notulen as m', 'm.id_notulen', '=', 'notulen.id')->join('users as u', 'm.id_mention', '=', 'u.id')->where('id_notuler', Auth::id())->get(['notulen.*', 'u.name', 'm.level']);
        return response()->json(['data' => $notulen]);
    });
    Route::get('/db/notulen/mention/admin', function () {
        $notulen = Notulen::join('mention_notulen as m', 'm.id_notulen', '=', 'notulen.id')->join('users as u', 'm.id_mention', '=', 'u.id')->get(['notulen.*', 'u.name', 'm.level', 'm.id as mId']);
        return response()->json(['data' => $notulen]);
    });
    Route::get('/db/notulen', function () {
        $notulen = Notulen::join('mention_notulen as m', 'm.id_notulen', '=', 'notulen.id')->join('users as u', 'm.id_mention', '=', 'u.id')->where('m.id_mention', Auth::id())->get(['notulen.*', 'u.name', 'm.level']);
        return response()->json(['data' => $notulen]);
    });

    // Helpdesk
    Route::get('/helpdesk', [HelpdeskController::class, 'index'])->name('helpdesk.index');
    Route::post('/helpdesk', [HelpdeskController::class, 'store'])->name('helpdesk.store');
    Route::patch('/helpdesk/status/{id}', [HelpdeskController::class, 'updateStatus'])->name('helpdesk.update-status');
    Route::get('/db/helpdesk', function () {
        $tickets = HelpdeskTicket::where('id_user', Auth::id())->latest()->get();
        $tickets->each(function ($ticket) {
            $ticket->created_at = $ticket->created_at?->clone()->timezone('Asia/Jakarta');
            $ticket->updated_at = $ticket->updated_at?->clone()->timezone('Asia/Jakarta');
        });
        return response()->json(['data' => $tickets]);
    });
    Route::get('/db/helpdesk/admin', function () {
        $category = request()->query('category', 'user_report');
        $query = HelpdeskTicket::leftJoin('users as u', 'u.id', '=', 'helpdesk_tickets.id_user');

        if ($category === 'system_error') {
            $query->where('helpdesk_tickets.category', 'system_error');
        } else {
            $query->where(function ($q) {
                $q->where('helpdesk_tickets.category', 'user_report')
                  ->orWhereNull('helpdesk_tickets.category');
            });
        }

        $tickets = $query->latest('helpdesk_tickets.created_at')
            ->get(['helpdesk_tickets.*', 'u.name', 'u.image as user_image']);
        $tickets->each(function ($ticket) {
            $ticket->created_at = $ticket->created_at?->clone()->timezone('Asia/Jakarta');
            $ticket->updated_at = $ticket->updated_at?->clone()->timezone('Asia/Jakarta');
        });

        return response()->json(['data' => $tickets]);
    });

    // Activity Log & System Audit
    Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');
});
