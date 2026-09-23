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
        Schema::table('stock_opname', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_opname', 'status')) {
                $table->string('status', 50)->default('in_progress')->after('periode');
            }
            if (!Schema::hasColumn('stock_opname', 'is_locked')) {
                $table->tinyInteger('is_locked')->default(0)->after('status');
            }
            if (!Schema::hasColumn('stock_opname', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('is_locked');
            }
            if (!Schema::hasColumn('stock_opname', 'id_user_completed')) {
                $table->unsignedBigInteger('id_user_completed')->nullable()->after('completed_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_opname', function (Blueprint $table) {
            $cols = ['status', 'is_locked', 'completed_at', 'id_user_completed'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('stock_opname', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
