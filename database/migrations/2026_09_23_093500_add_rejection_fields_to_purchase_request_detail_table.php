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
        Schema::table('purchase_request_detail', function (Blueprint $table) {
            $table->boolean('is_rejected')->default(false);
            $table->text('rejected_reason')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->unsignedBigInteger('rejected_by')->nullable();

            $table->foreign('rejected_by')->references('id')->on('users')->onDelete('set null');
            $table->index('is_rejected');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_request_detail', function (Blueprint $table) {
            $table->dropForeign(['rejected_by']);
            $table->dropIndex(['is_rejected']);
            $table->dropColumn(['is_rejected', 'rejected_reason', 'rejected_at', 'rejected_by']);
        });
    }
};
