<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_request', function (Blueprint $table) {
            $table->timestamp('rejected_at')->nullable()->after('status');
            $table->text('rejected_reason')->nullable()->after('rejected_at');
            $table->unsignedBigInteger('rejected_by')->nullable()->after('rejected_reason');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_request', function (Blueprint $table) {
            $table->dropColumn(['rejected_at', 'rejected_reason', 'rejected_by']);
        });
    }
};
