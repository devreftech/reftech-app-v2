<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Biaya sekarang bisa dicatat langsung dari kartu Kanban (task manual atau
        // quotation yang belum jadi PO) lewat id_kanban_task, tanpa harus punya
        // id_pending. Waktu kartu-nya akhirnya jadi PO, biaya lama ikut ke-rollup
        // di Project Monitoring.
        Schema::table('project_expenses', function (Blueprint $table) {
            $table->unsignedBigInteger('id_kanban_task')->nullable()->after('id_pending');
            $table->index('id_kanban_task');
        });

        // id_pending tidak lagi wajib — expense bisa cuma nempel ke kartu Kanban.
        DB::statement('ALTER TABLE project_expenses MODIFY id_pending BIGINT(20) UNSIGNED NULL');
    }

    public function down(): void
    {
        Schema::table('project_expenses', function (Blueprint $table) {
            $table->dropIndex(['id_kanban_task']);
            $table->dropColumn('id_kanban_task');
        });

        DB::statement('ALTER TABLE project_expenses MODIFY id_pending BIGINT(20) UNSIGNED NOT NULL');
    }
};
