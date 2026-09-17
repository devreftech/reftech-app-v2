<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('kanban_task_service_reports')) {
            Schema::create('kanban_task_service_reports', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('kanban_task_id');
                $table->unsignedBigInteger('service_report_id')->nullable();
                $table->text('note')->nullable();
                $table->timestamps();

                $table->foreign('kanban_task_id')->references('id')->on('kanban_tasks')->cascadeOnDelete();
                $table->index('service_report_id');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('kanban_task_service_reports');
    }
};
