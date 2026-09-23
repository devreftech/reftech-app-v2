<?php

use App\Models\OnlineLead;
use App\Models\OnlineLeadFollowUp;
use App\Models\OnlineLeadFollowUpItem;
use Illuminate\Database\Migrations\Migration;

// Sebelum fitur "Follow Up & Kebutuhan" ada, kebutuhan awal cuma disimpan di
// kolom product_interest/notes pada online_leads, gak tercatat di timeline.
// Migration ini backfill lead-lead lama (yang product_interest-nya terisi &
// belum punya follow-up sama sekali) jadi follow-up #1, biar timeline-nya
// utuh dari kebutuhan pertama.
return new class extends Migration
{
    public function up(): void
    {
        OnlineLead::whereNotNull('product_interest')
            ->where('product_interest', '!=', '')
            ->doesntHave('followUps')
            ->chunkById(200, function ($leads) {
                foreach ($leads as $lead) {
                    $itemName = trim($lead->product_interest ?? '');
                    if ($itemName === '') {
                        continue;
                    }

                    $followUp = OnlineLeadFollowUp::create([
                        'id_lead' => $lead->id,
                        'id_user' => $lead->id_sales,
                        'date' => $lead->date,
                        'note' => $lead->notes ?: 'Kebutuhan awal saat chat pertama masuk.',
                    ]);

                    OnlineLeadFollowUpItem::create([
                        'id_follow_up' => $followUp->id,
                        'item_name' => $itemName,
                        'status' => 'pending',
                    ]);
                }
            });
    }

    public function down(): void
    {
        // Data backfill — sengaja gak di-reverse otomatis (bisa kehapus follow-up
        // manual yang sempat ditambahkan user setelah backfill jalan).
    }
};
