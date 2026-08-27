<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Widen the `type` enum so page-navigation logs (not just transactions
     * and errors) can be recorded on the activity-log page.
     */
    public function up()
    {
        DB::statement("ALTER TABLE activity_logs MODIFY COLUMN type ENUM('activity', 'error', 'page_visit') NOT NULL DEFAULT 'activity'");
    }

    public function down()
    {
        DB::table('activity_logs')->where('type', 'page_visit')->delete();
        DB::statement("ALTER TABLE activity_logs MODIFY COLUMN type ENUM('activity', 'error') NOT NULL DEFAULT 'activity'");
    }
};
