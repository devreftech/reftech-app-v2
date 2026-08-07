<?php

namespace App\Console\Commands;

use App\Models\UnitQuotation;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ExpireUnitQuotations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * php artisan unit-quotation:expire           → run normally
     * php artisan unit-quotation:expire --dry-run → preview only, no DB changes
     */
    protected $signature = 'unit-quotation:expire {--dry-run : Preview which quotations would expire, without making changes}';

    /**
     * The console command description.
     */
    protected $description = 'Auto-set unit quotations to "loss" when expired_date has passed with no status update';

    /**
     * Statuses that are already final — skip auto-expiry.
     */
    protected array $excludedStatuses = ['loss', 'cancel', 'po_received', 'revision'];

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $today  = Carbon::today();

        $this->info('=== Unit Quotation Auto-Expire' . ($dryRun ? ' [DRY RUN]' : '') . ' ===');
        $this->info('Date: ' . $today->toDateString());
        $this->line('');

        // Find all quotations that have passed their expired_date and are not in a final status
        $expiredQuotations = UnitQuotation::query()
            ->whereNotNull('expired_date')
            ->whereDate('expired_date', '<', $today)
            ->whereNotIn('status', $this->excludedStatuses)
            ->where('is_latest', 1)
            ->get();

        if ($expiredQuotations->isEmpty()) {
            $this->info('✓ No expired quotations found. All good!');
            return self::SUCCESS;
        }

        $this->warn("Found {$expiredQuotations->count()} expired quotation(s):");
        $this->line('');

        $table = [];
        foreach ($expiredQuotations as $quote) {
            $table[] = [
                $quote->id,
                $quote->no_quote,
                $quote->status,
                $quote->expired_date?->toDateString() ?? '-',
                $quote->client?->company ?? '-',
            ];
        }

        $this->table(
            ['ID', 'No. Quote', 'Current Status', 'Expired Date', 'Client'],
            $table
        );

        if ($dryRun) {
            $this->line('');
            $this->warn('[DRY RUN] No changes were made.');
            return self::SUCCESS;
        }

        $this->line('');
        $count = 0;

        foreach ($expiredQuotations as $quote) {
            $quote->update(['status' => 'loss']);

            $quote->statusHistory()->create([
                'status' => 'loss',
                'note'   => 'Auto-expired: no status update before expired date (' . $quote->expired_date?->toDateString() . ')',
            ]);

            $this->line("  → [{$quote->no_quote}] status changed to <fg=red>loss</>");
            $count++;
        }

        $this->line('');
        $this->info("✓ Done. {$count} quotation(s) moved to loss.");

        return self::SUCCESS;
    }
}
