<?php

namespace App\Console\Commands;

use App\Services\ToolAuditPeriodGenerator;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateToolAuditPeriod extends Command
{
    protected $signature = 'tools:generate-audit-period {--date= : Simulasikan tanggal tertentu (YYYY-MM-DD), buat testing di luar window asli}';

    protected $description = 'Generate tool_audit_period + draft tool_audit/tool_audit_item kalau tanggal (asli atau simulasi) masuk window audit (10 hari terakhir Juni/Desember)';

    public function handle(ToolAuditPeriodGenerator $generator)
    {
        $date = $this->option('date') ? Carbon::parse($this->option('date')) : null;
        $period = $generator->generateIfNeeded($date);

        if (!$period) {
            $this->info('Tanggal tersebut di luar window audit tools. Tidak ada yang digenerate.');
            return Command::SUCCESS;
        }

        $this->info("Periode audit tahun {$period->tahun} semester {$period->semester} siap (id: {$period->id}).");
        return Command::SUCCESS;
    }
}
