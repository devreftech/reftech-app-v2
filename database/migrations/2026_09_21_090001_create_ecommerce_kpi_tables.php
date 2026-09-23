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
        Schema::create('ecommerce_kpi_templates', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['automatic', 'hybrid', 'manual'])->default('automatic');
            $table->string('unit')->default('%'); // 'IDR', 'Qty', '%', 'Poin', 'Update', 'PO'
            $table->decimal('default_target', 18, 2)->default(100.00);
            $table->decimal('default_weight', 5, 2)->default(10.00); // dalam persen
            $table->string('calculation_handler')->nullable(); // 'revenue', 'product_upload', 'po_count', 'sw_update', 'akurasi', 'response', 'delivery', 'rating', 'customer', 'video', 'manual'
            $table->integer('sort_order')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('ecommerce_kpi_periods', function (Blueprint $table) {
            $table->id();
            $table->integer('year');
            $table->integer('month');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['open', 'review', 'published'])->default('open');
            $table->timestamp('published_at')->nullable();
            $table->unsignedBigInteger('published_by')->nullable();
            $table->timestamps();

            $table->unique(['year', 'month']);
        });

        Schema::create('ecommerce_kpi_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('period_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('evaluator_id')->nullable();
            $table->decimal('total_score', 6, 2)->default(0.00);
            $table->string('grade', 5)->nullable(); // A, B, C, D
            $table->enum('status', ['draft', 'review', 'published'])->default('draft');
            $table->text('notes')->nullable();
            $table->timestamp('evaluated_at')->nullable();
            $table->timestamps();

            $table->unique(['period_id', 'user_id']);
        });

        Schema::create('ecommerce_kpi_assignment_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assignment_id');
            $table->unsignedBigInteger('template_id')->nullable();
            $table->string('kpi_code')->nullable();
            $table->string('kpi_name');
            $table->enum('kpi_type', ['automatic', 'hybrid', 'manual'])->default('automatic');
            $table->string('unit')->default('%');
            $table->string('calculation_handler')->nullable();
            $table->decimal('target', 18, 2)->default(0.00);
            $table->decimal('actual_system', 18, 2)->nullable();
            $table->decimal('actual_final', 18, 2)->default(0.00);
            $table->decimal('achievement_rate', 8, 2)->default(0.00); // in percent (actual_final / target * 100)
            $table->decimal('weight', 5, 2)->default(0.00); // in percent
            $table->decimal('score', 6, 2)->default(0.00); // achievement_rate * weight / 100
            $table->text('evaluator_notes')->nullable();
            $table->integer('sort_order')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ecommerce_kpi_assignment_items');
        Schema::dropIfExists('ecommerce_kpi_assignments');
        Schema::dropIfExists('ecommerce_kpi_periods');
        Schema::dropIfExists('ecommerce_kpi_templates');
    }
};
