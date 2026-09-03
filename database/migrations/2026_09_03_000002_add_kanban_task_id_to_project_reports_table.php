<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('project_reports', function (Blueprint $table) {
            if (!Schema::hasColumn('project_reports', 'kanban_task_id')) {
                $table->unsignedBigInteger('kanban_task_id')->nullable()->after('client_id');
                $table->foreign('kanban_task_id')->references('id')->on('kanban_tasks')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_reports', function (Blueprint $table) {
            if (Schema::hasColumn('project_reports', 'kanban_task_id')) {
                $table->dropForeign(['kanban_task_id']);
                $table->dropColumn('kanban_task_id');
            }
        });
    }
};
