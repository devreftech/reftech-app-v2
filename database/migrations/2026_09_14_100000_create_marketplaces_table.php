<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('marketplaces')) {
            Schema::create('marketplaces', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('entity')->nullable();
                $table->unsignedBigInteger('id_default_bank')->nullable();
                $table->decimal('fee_percent', 5, 2)->nullable();
                $table->boolean('is_active')->default(1);
                $table->timestamps();

                $table->foreign('id_default_bank')->references('id')->on('bank')->nullOnDelete();
            });
        }

        // Seed marketplace channels already in use across historical escrow payments
        // (payment.escrow_channel values: "Kojisha Filter", "Parts Compressor") plus
        // "Airend Center" which is offered in the payment form but unused so far.
        // Additive only — never touches existing payment rows.
        $existing = DB::table('marketplaces')->pluck('name')->all();
        $seed = [
            ['name' => 'Airend Center', 'entity' => 'Reftech'],
            ['name' => 'Parts Compressor', 'entity' => 'Reftech'],
            ['name' => 'Kojisha Filter', 'entity' => 'Kojisha'],
        ];
        foreach ($seed as $row) {
            if (!in_array($row['name'], $existing, true)) {
                DB::table('marketplaces')->insert([
                    'name' => $row['name'],
                    'entity' => $row['entity'],
                    'is_active' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketplaces');
    }
};
