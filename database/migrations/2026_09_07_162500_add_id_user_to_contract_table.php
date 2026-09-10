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
        Schema::table('contract', function (Blueprint $table) {
            if (!Schema::hasColumn('contract', 'id_user')) {
                $table->unsignedBigInteger('id_user')->nullable()->after('id_client');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contract', function (Blueprint $table) {
            if (Schema::hasColumn('contract', 'id_user')) {
                $table->dropColumn('id_user');
            }
        });
    }
};
