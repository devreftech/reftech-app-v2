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
        Schema::table('helpdesk_tickets', function (Blueprint $table) {
            if (!Schema::hasColumn('helpdesk_tickets', 'category')) {
                $table->enum('category', ['user_report', 'system_error'])->default('user_report')->after('id_user')->index();
            }
            if (!Schema::hasColumn('helpdesk_tickets', 'error_file')) {
                $table->string('error_file')->nullable()->after('description');
            }
            if (!Schema::hasColumn('helpdesk_tickets', 'error_line')) {
                $table->integer('error_line')->nullable()->after('error_file');
            }
            if (!Schema::hasColumn('helpdesk_tickets', 'error_exception')) {
                $table->string('error_exception')->nullable()->after('error_line');
            }
            if (!Schema::hasColumn('helpdesk_tickets', 'url_accessed')) {
                $table->text('url_accessed')->nullable()->after('error_exception');
            }
            if (!Schema::hasColumn('helpdesk_tickets', 'http_method')) {
                $table->string('http_method', 10)->nullable()->after('url_accessed');
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
        Schema::table('helpdesk_tickets', function (Blueprint $table) {
            $table->dropColumn(['category', 'error_file', 'error_line', 'error_exception', 'url_accessed', 'http_method']);
        });
    }
};
