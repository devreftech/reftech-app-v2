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
        // 1. Master Material Piping
        if (!Schema::hasTable('piping_materials')) {
            Schema::create('piping_materials', function (Blueprint $table) {
                $table->id();
                $table->string('item_code')->nullable()->unique();
                $table->enum('category', ['pipe', 'fitting', 'valve', 'support', 'consumable', 'other'])->default('pipe');
                $table->string('material_type')->nullable(); // e.g. Aluminium, SS304, CS Sch40, PPR, Galvanis
                $table->string('item_name');                 // e.g. Pipa Aluminium, Elbow 90, Ball Valve
                $table->string('size')->nullable();          // e.g. 1/2", 1", 2", 50mm, 63mm
                $table->string('connection_type')->nullable(); // e.g. Threaded, Flange, Socket Weld, Quick-Fit
                $table->string('unit')->default('Batang');   // e.g. Batang 6m, Meter, Pcs, Roll, Set
                $table->decimal('length_per_unit', 8, 2)->nullable()->default(6.00); // Standar panjang per batang dalam meter
                $table->decimal('default_waste_percent', 5, 2)->default(5.00); // Safety scrap margin (%)
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 2. Vendor Pricelist Material Piping (Mirip Part Inquiry)
        if (!Schema::hasTable('piping_material_vendor_prices')) {
            Schema::create('piping_material_vendor_prices', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_piping_material');
                $table->integer('id_supplier'); // FK ke table supplier
                $table->decimal('price_idr', 15, 2)->default(0);
                $table->decimal('price_usd', 15, 2)->nullable();
                $table->decimal('kurs_usd', 15, 2)->nullable();
                $table->date('date')->nullable(); // Tanggal update inquiry harga
                $table->string('notes')->nullable();
                $table->boolean('is_primary')->default(false);
                $table->timestamps();

                $table->foreign('id_piping_material')->references('id')->on('piping_materials')->onDelete('cascade');
                $table->index(['id_piping_material', 'id_supplier'], 'piping_mat_sup_idx');
            });
        }

        // 3. Header RAB Piping Proyek
        if (!Schema::hasTable('piping_rabs')) {
            Schema::create('piping_rabs', function (Blueprint $table) {
                $table->id();
                $table->string('no_rab')->unique();
                $table->integer('id_client')->nullable();
                $table->integer('id_pic')->nullable();
                $table->integer('id_sales')->nullable();
                $table->integer('id_admin')->nullable();
                $table->string('project_name');
                $table->string('location_plant')->nullable();
                $table->date('rab_date')->nullable();
                $table->integer('revision_number')->default(0);
                $table->unsignedBigInteger('root_id')->nullable();
                $table->boolean('is_latest')->default(true);
                $table->enum('status', ['Draft', 'Reviewed', 'Approved', 'Converted'])->default('Draft');
                $table->decimal('total_hpp', 15, 2)->default(0);
                $table->decimal('total_margin', 15, 2)->default(0);
                $table->decimal('total_selling_price', 15, 2)->default(0);
                $table->unsignedBigInteger('converted_quotation_id')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['root_id', 'is_latest']);
            });
        }

        // 4. Section / Plant Grouping dalam 1 RAB (e.g. Material Plant A, Material Plant B, Jasa Instalasi)
        if (!Schema::hasTable('piping_rab_sections')) {
            Schema::create('piping_rab_sections', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_piping_rab');
                $table->string('section_name');
                $table->integer('sort_order')->default(0);
                $table->decimal('subtotal_hpp', 15, 2)->default(0);
                $table->decimal('subtotal_selling_price', 15, 2)->default(0);
                $table->timestamps();

                $table->foreign('id_piping_rab')->references('id')->on('piping_rabs')->onDelete('cascade');
            });
        }

        // 5. Detail Items dalam Section RAB
        if (!Schema::hasTable('piping_rab_items')) {
            Schema::create('piping_rab_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_piping_rab_section');
                $table->unsignedBigInteger('id_piping_material')->nullable();
                $table->enum('item_type', ['material', 'service', 'equipment', 'other'])->default('material');
                $table->string('item_name');
                $table->string('size')->nullable();
                $table->string('spec')->nullable();
                $table->string('unit')->default('Pcs');
                $table->decimal('input_length_meter', 10, 2)->nullable(); // Panjang meter jika pipa
                $table->decimal('length_per_unit', 8, 2)->nullable();     // Standar panjang batang (6m)
                $table->decimal('waste_percent', 5, 2)->default(0);       // Waste margin %
                $table->decimal('calculated_qty', 10, 2)->default(1);     // Qty hasil hitung / input
                $table->decimal('unit_price_hpp', 15, 2)->default(0);     // Harga modal dari vendor terpilih
                $table->integer('id_supplier')->nullable();               // Supplier vendor terpilih
                $table->enum('margin_type', ['percent', 'nominal'])->default('percent');
                $table->decimal('margin_value', 15, 2)->default(0);       // % atau nominal Rp margin
                $table->decimal('unit_selling_price', 15, 2)->default(0); // Harga jual satuan
                $table->decimal('total_hpp', 15, 2)->default(0);
                $table->decimal('total_selling_price', 15, 2)->default(0);
                $table->string('notes')->nullable();
                $table->integer('sort_order')->default(0);
                $table->timestamps();

                $table->foreign('id_piping_rab_section')->references('id')->on('piping_rab_sections')->onDelete('cascade');
            });
        }

        // 6. Tambahkan kolom id_piping_rab ke unit_quotation jika belum ada
        if (Schema::hasTable('unit_quotation') && !Schema::hasColumn('unit_quotation', 'id_piping_rab')) {
            Schema::table('unit_quotation', function (Blueprint $table) {
                $table->unsignedBigInteger('id_piping_rab')->nullable()->after('root_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('unit_quotation') && Schema::hasColumn('unit_quotation', 'id_piping_rab')) {
            Schema::table('unit_quotation', function (Blueprint $table) {
                $table->dropColumn('id_piping_rab');
            });
        }

        Schema::dropIfExists('piping_rab_items');
        Schema::dropIfExists('piping_rab_sections');
        Schema::dropIfExists('piping_rabs');
        Schema::dropIfExists('piping_material_vendor_prices');
        Schema::dropIfExists('piping_materials');
    }
};
