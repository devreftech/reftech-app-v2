<?php

namespace App\Services;

use App\Models\PurchaseRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PurchaseRequestService
{
    /**
     * Find the draft (status=0) PR header for this pending, or create one.
     * Locked so concurrent submissions for the same pending don't create
     * two headers.
     */
    public function findOrCreateDraftHeader(int $idPending, ?int $idUser): PurchaseRequest
    {
        return DB::transaction(function () use ($idPending, $idUser) {
            $header = PurchaseRequest::where('id_pending', $idPending)
                ->where('status', '0')
                ->lockForUpdate()
                ->first();

            if (!$header) {
                $header = new PurchaseRequest();
                $header->no_pr = $this->generateNoPr();
                $header->id_pending = $idPending;
                $header->id_user = $idUser;
                $header->status = '0';
                $header->date = Carbon::now();
                $header->save();
            }

            return $header;
        });
    }

    public function generateNoPr(): string
    {
        $year = now()->format('Y');
        $month = now()->format('m');
        $prefix = "PR/{$year}/{$month}/";

        $last = PurchaseRequest::where('no_pr', 'like', $prefix . '%')
            ->orderByDesc('no_pr')
            ->value('no_pr');

        $lastSeq = $last ? (int) substr($last, -3) : 0;
        $nextSeq = str_pad($lastSeq + 1, 3, '0', STR_PAD_LEFT);

        return $prefix . $nextSeq;
    }
}
