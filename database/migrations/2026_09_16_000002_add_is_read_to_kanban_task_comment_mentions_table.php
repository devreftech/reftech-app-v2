<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('kanban_task_comment_mentions')) {
            Schema::table('kanban_task_comment_mentions', function (Blueprint $table) {
                if (!Schema::hasColumn('kanban_task_comment_mentions', 'is_read')) {
                    $table->boolean('is_read')->default(false)->after('user_id');
                }
                if (!Schema::hasColumn('kanban_task_comment_mentions', 'read_at')) {
                    $table->timestamp('read_at')->nullable()->after('is_read');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('kanban_task_comment_mentions')) {
            Schema::table('kanban_task_comment_mentions', function (Blueprint $table) {
                if (Schema::hasColumn('kanban_task_comment_mentions', 'read_at')) {
                    $table->dropColumn('read_at');
                }
                if (Schema::hasColumn('kanban_task_comment_mentions', 'is_read')) {
                    $table->dropColumn('is_read');
                }
            });
        }
    }
};
