<?php

namespace App\Console\Commands;

use App\Models\Employee;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackfillEmployees extends Command
{
    /**
     * php artisan hr:backfill-employees            → run backfill
     * php artisan hr:backfill-employees --dry-run   → preview only, no DB changes
     *
     * Creates one `employees` row per eligible `users` row. Safe to re-run:
     * uses firstOrCreate keyed on user_id, so existing employees (including
     * ones HR has already edited manually) are never touched or overwritten.
     *
     * id_department / id_position are always left NULL here — there is no
     * reliable source data to map them automatically (see report at the end).
     */
    protected $signature = 'hr:backfill-employees {--dry-run : Preview counts and the manual-review list without writing}';

    protected $description = 'Backfill employees table from existing users (and detail_users for reference only)';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        $this->info('=== HR Backfill: employees' . ($dryRun ? ' [DRY RUN]' : '') . ' ===');
        $this->line('');

        $users = DB::table('users')
            ->where('role', '!=', 'Client')
            ->orderBy('id')
            ->get();

        $this->line("Total users (role != Client): {$users->count()}");

        $created = 0;
        $alreadyExists = 0;
        $needsReview = [];

        $process = function () use ($users, $dryRun, &$created, &$alreadyExists, &$needsReview) {
            foreach ($users as $user) {
                if (Employee::where('user_id', $user->id)->exists()) {
                    $alreadyExists++;
                    continue;
                }

                $isActive = (string) $user->active === '1';

                // Never invent a resign date from updated_at — it can be bumped by
                // unrelated edits (password reset, profile edit, etc.) long after the
                // user actually stopped working. Mark uncertain instead of guessing.
                $employmentStatus = $isActive ? 'Tetap' : 'Perlu Verifikasi';

                $attributes = [
                    'id_department' => null,
                    'id_position' => null,
                    'nik' => $user->nip !== null ? (string) $user->nip : null,
                    'join_date' => $user->date_in,
                    'birthday' => $user->birthday,
                    'address' => $user->address,
                    'phone' => $user->phone,
                    'employment_status' => $employmentStatus,
                    'contract_start_date' => null,
                    'contract_end_date' => null,
                    'resign_date' => null,
                ];

                $lastPosition = DB::table('detail_users')
                    ->where('id_users', $user->id)
                    ->orderByDesc('date')
                    ->orderByDesc('id')
                    ->value('position');

                $reasons = ['department: kosong', 'position: kosong'];
                if (!$isActive) {
                    $reasons[] = 'status: Perlu Verifikasi (users.active=0, tanggal resign tidak diketahui)';
                }

                $needsReview[] = [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'last_position_text' => $lastPosition ?: '-',
                    'reasons' => implode(' | ', $reasons),
                ];

                if ($dryRun) {
                    $created++;
                    continue;
                }

                // firstOrCreate (not create): the unique index on user_id is the real
                // guard against duplicates, this just makes it explicit in code too.
                Employee::firstOrCreate(['user_id' => $user->id], $attributes);
                $created++;
            }
        };

        if ($dryRun) {
            $process();
        } else {
            DB::transaction($process);
        }

        $this->line('');
        $this->info('Skipped (role = Client): ' . ($users->isEmpty() ? 0 : DB::table('users')->where('role', 'Client')->count()));
        $this->info('Already had an employee record (untouched): ' . $alreadyExists);
        $this->info(($dryRun ? 'Would be created' : 'Created') . ': ' . $created);

        if (!empty($needsReview)) {
            $this->line('');
            $this->warn('To-do HR — perlu verifikasi/isi manual setelah backfill:');
            $this->table(
                ['User ID', 'Nama', 'Email', 'Posisi lama (referensi, belum ter-link)', 'Perlu diisi/verifikasi'],
                array_map(fn ($row) => [
                    $row['user_id'],
                    $row['name'],
                    $row['email'],
                    $row['last_position_text'],
                    $row['reasons'],
                ], $needsReview)
            );
        }

        $this->line('');
        if ($dryRun) {
            $this->warn('[DRY RUN] No changes were written.');
        } else {
            $this->info('✓ Done.');
        }

        return self::SUCCESS;
    }
}
