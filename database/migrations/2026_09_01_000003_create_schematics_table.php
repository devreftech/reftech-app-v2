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
        Schema::create('schematics', function (Blueprint $table) {
            $table->id();
            $table->string('schematic_number', 50)->unique();
            $table->string('title');
            $table->unsignedBigInteger('client_id')->nullable()->index();
            $table->string('project_name')->nullable();
            $table->string('diagram_type', 100)->default('Refrigeration System');
            $table->longText('canvas_data')->nullable();
            $table->longText('preview_image')->nullable();
            $table->text('description')->nullable();
            $table->string('status', 50)->default('Draft');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('client')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schematics');
    }
};
