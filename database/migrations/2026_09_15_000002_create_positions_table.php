<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_department')->nullable();
            $table->string('name');
            $table->unsignedInteger('level')->default(1); // dipakai untuk approval berjenjang (level lebih tinggi = wewenang lebih besar)
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('id_department')->references('id')->on('departments')->onDelete('set null');
            $table->index(['id_department', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('positions');
    }
};
