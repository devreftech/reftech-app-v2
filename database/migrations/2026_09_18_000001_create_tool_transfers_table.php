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
        Schema::create('tool_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('transfer_number')->unique();
            $table->unsignedBigInteger('id_fixed_asset');
            $table->unsignedBigInteger('id_from_user');
            $table->unsignedBigInteger('id_to_user');
            $table->enum('status', ['Pending', 'Approved', 'Rejected', 'Cancelled'])->default('Pending');
            $table->text('catatan_pengirim')->nullable();
            $table->text('catatan_penerima')->nullable();
            $table->string('foto_kondisi')->nullable();
            $table->dateTime('requested_at');
            $table->dateTime('responded_at')->nullable();
            $table->timestamps();

            $table->foreign('id_fixed_asset')->references('id')->on('fixed_asset')->onDelete('cascade');
            $table->foreign('id_from_user')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_to_user')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tool_transfers');
    }
};
