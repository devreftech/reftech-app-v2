<?php

namespace App\Console\Commands;

use App\Models\Marketplace;
use App\Models\Payment;
use Illuminate\Console\Command;

class BackfillMarketplaceEscrow extends Command
{
    /**
     * php artisan marketplace:backfill-escrow            → run backfill
     * php artisan marketplace:backfill-escrow --dry-run   → preview only, no DB changes
     *
     * Only ever touches the new `id_marketplace` column. `method` and
     * `escrow_channel` on the payment table are never modified — every
     * existing read path that relies on them keeps working unchanged.
     */
    protected $signature = 'marketplace:backfill-escrow {--dry-run : Preview matched/unmatched counts without writing}';

    protected $description = 'Link existing Escrow payments to their Marketplace master record via escrow_channel, where known';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        $this->info('=== Marketplace Escrow Backfill' . ($dryRun ? ' [DRY RUN]' : '') . ' ===');

        $marketplacesByName = Marketplace::pluck('id', 'name');

        $escrowPayments = Payment::where('method', 'Escrow')
            ->whereNull('id_marketplace')
            ->get();

        $this->line("Total Escrow payments without id_marketplace yet: {$escrowPayments->count()}");

        $matched = 0;
        $unmatched = 0;
        $skippedErrors = 0;

        foreach ($escrowPayments as $payment) {
            $channel = trim((string) $payment->escrow_channel);

            if ($channel === '' || !isset($marketplacesByName[$channel])) {
                // No channel on record, or channel text doesn't match any known
                // marketplace — leave id_marketplace NULL. Never guess, never fail.
                $unmatched++;
                continue;
            }

            $matched++;

            if ($dryRun) {
                continue;
            }

            try {
                $payment->id_marketplace = $marketplacesByName[$channel];
                // disbursement_status stays at its default 'held' — we have no
                // settlement history yet to say otherwise.
                $payment->save();
            } catch (\Throwable $e) {
                $skippedErrors++;
                $this->warn("  ! Skipped payment #{$payment->id}: {$e->getMessage()}");
            }
        }

        $this->line('');
        $this->info("Matched (has known escrow_channel): {$matched}");
        $this->info("Unmatched (legacy, left as NULL for manual review): {$unmatched}");
        if ($skippedErrors > 0) {
            $this->warn("Skipped due to error: {$skippedErrors}");
        }

        if ($dryRun) {
            $this->line('');
            $this->warn('[DRY RUN] No changes were written.');
        } else {
            $this->line('');
            $this->info('✓ Done.');
        }

        return self::SUCCESS;
    }
}
