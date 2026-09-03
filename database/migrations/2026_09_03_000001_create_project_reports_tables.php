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
        Schema::create('project_reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_number')->nullable()->index();
            $table->string('job_name');
            $table->string('contract_no')->nullable();
            $table->date('report_date')->index();
            $table->string('contractor_name')->default('PT. REFTECH JAYA OPTIMA');
            $table->string('day_number')->nullable();
            $table->string('day_name')->nullable();
            $table->string('days_remaining')->nullable();
            
            $table->unsignedBigInteger('client_id')->nullable()->index();
            $table->unsignedBigInteger('created_by')->nullable()->index();

            // Cuaca
            $table->boolean('weather_cerah')->default(false);
            $table->string('weather_cerah_time')->nullable();
            $table->boolean('weather_hujan')->default(false);
            $table->string('weather_hujan_time')->nullable();
            $table->boolean('weather_mendung')->default(false);
            $table->string('weather_mendung_time')->nullable();
            $table->boolean('weather_dll')->default(false);
            $table->string('weather_dll_time')->nullable();

            // Notes / Textarea
            $table->text('planning_today')->nullable();
            $table->text('achievement_today')->nullable();
            $table->text('issues_constraints')->nullable();
            $table->text('next_plan')->nullable();

            // Signatures
            $table->string('client_sign')->nullable();
            $table->string('client_pic_name')->nullable();
            $table->string('contractor_sign')->nullable();
            $table->string('contractor_pic_name')->nullable();

            $table->string('status')->default('completed'); // draft, completed, approved
            $table->timestamps();
        });

        Schema::create('project_report_tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_project_report')->index();
            $table->string('task_name');
            $table->string('location')->nullable();
            $table->string('notes')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('id_project_report')->references('id')->on('project_reports')->onDelete('cascade');
        });

        Schema::create('project_report_materials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_project_report')->index();
            $table->string('material_name');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('id_project_report')->references('id')->on('project_reports')->onDelete('cascade');
        });

        Schema::create('project_report_equipments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_project_report')->index();
            $table->string('equipment_name');
            $table->string('qty')->nullable();
            $table->string('unit')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('id_project_report')->references('id')->on('project_reports')->onDelete('cascade');
        });

        Schema::create('project_report_manpowers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_project_report')->index();
            $table->string('position');
            $table->string('manpower_count')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('id_project_report')->references('id')->on('project_reports')->onDelete('cascade');
        });

        Schema::create('project_report_photos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_project_report')->index();
            $table->string('photo_path');
            $table->string('caption')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('id_project_report')->references('id')->on('project_reports')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_report_photos');
        Schema::dropIfExists('project_report_manpowers');
        Schema::dropIfExists('project_report_equipments');
        Schema::dropIfExists('project_report_materials');
        Schema::dropIfExists('project_report_tasks');
        Schema::dropIfExists('project_reports');
    }
};
