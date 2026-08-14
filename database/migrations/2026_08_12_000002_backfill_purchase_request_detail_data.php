<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Consolidate the old "1 row per item" purchase_request rows into
     * 1 header row per id_pending + N purchase_request_detail rows.
     */
    public function up(): void
    {
        $rows = DB::table('purchase_request')->orderBy('id')->get();

        $groups = $rows->groupBy('id_pending');

        foreach ($groups as $idPending => $groupRows) {
            $header = $groupRows->sortBy('id')->first();

            $now = now();

            foreach ($groupRows as $row) {
                DB::table('purchase_request_detail')->insert([
                    'id_purchase_request' => $header->id,
                    'id_equivalent' => $row->id_equivalent,
                    'qty' => $row->qty,
                    'note' => $row->note,
                    'price' => $row->price,
                    'amount' => $row->amount,
                    'purchase_type' => $row->purchase_type,
                    'cargo' => $row->cargo,
                    'no_resi' => $row->no_resi,
                    'purchase_date' => $row->purchase_date,
                    'qty_received' => $row->qty_received,
                    'gr_status' => $row->gr_status,
                    'gr_note' => $row->gr_note,
                    'no_do' => $row->no_do,
                    'gr_date' => $row->gr_date,
                    'warehouse' => null,
                    'created_at' => $row->created_at ?? $now,
                    'updated_at' => $now,
                ]);
            }

            $idsToDelete = $groupRows->where('id', '!=', $header->id)->pluck('id');
            if ($idsToDelete->isNotEmpty()) {
                DB::table('purchase_request')->whereIn('id', $idsToDelete)->delete();
            }
        }
    }

    /**
     * Not reversible: the backfill deletes the original per-item rows.
     * Restore from a DB backup if you need to roll back.
     */
    public function down(): void
    {
        //
    }
};
