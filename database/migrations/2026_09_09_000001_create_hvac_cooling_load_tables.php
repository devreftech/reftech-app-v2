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
        // 1. Master Materials (Wall, Roof, Partition)
        if (!Schema::hasTable('hvac_materials')) {
            Schema::create('hvac_materials', function (Blueprint $table) {
                $table->id();
                $table->enum('category', ['wall', 'roof', 'ceiling', 'partition', 'floor'])->default('wall');
                $table->string('material_name', 150);
                $table->text('description')->nullable();
                $table->decimal('u_value', 8, 4); // W/m²·K
                $table->decimal('default_cltd', 6, 2)->nullable(); // °C
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 2. Master Glass Types
        if (!Schema::hasTable('hvac_glass_types')) {
            Schema::create('hvac_glass_types', function (Blueprint $table) {
                $table->id();
                $table->string('glass_name', 150);
                $table->decimal('u_value', 8, 4); // W/m²·K
                $table->decimal('shgc', 4, 3);    // Solar Heat Gain Coefficient (0 - 1)
                $table->decimal('shading_coefficient', 4, 3)->nullable();
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 3. Master Activity Loads (Occupants)
        if (!Schema::hasTable('hvac_activity_loads')) {
            Schema::create('hvac_activity_loads', function (Blueprint $table) {
                $table->id();
                $table->string('activity_name', 100);
                $table->decimal('sensible_watt', 8, 2);
                $table->decimal('latent_watt', 8, 2);
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // 4. Master AC Catalog
        if (!Schema::hasTable('hvac_ac_catalog')) {
            Schema::create('hvac_ac_catalog', function (Blueprint $table) {
                $table->id();
                $table->string('brand', 100);
                $table->string('model_name', 150);
                $table->enum('ac_type', ['split_wall', 'cassette', 'floor_standing', 'ceiling_ducted', 'vrv_vrf', 'package', 'chiller'])->default('split_wall');
                $table->decimal('nominal_pk', 4, 2);
                $table->decimal('cooling_capacity_btuh', 10, 2);
                $table->decimal('cooling_capacity_kw', 8, 3);
                $table->decimal('power_input_watt', 8, 2)->nullable();
                $table->string('refrigerant', 20)->default('R32');
                $table->string('energy_rating', 50)->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 5. Master City Design Temps (Outdoor references)
        if (!Schema::hasTable('hvac_city_design_temps')) {
            Schema::create('hvac_city_design_temps', function (Blueprint $table) {
                $table->id();
                $table->string('city_name', 100);
                $table->decimal('outdoor_db_temp', 5, 2)->default(33.00); // Dry Bulb °C
                $table->decimal('outdoor_rh_percent', 5, 2)->default(75.00); // RH %
                $table->decimal('outdoor_wb_temp', 5, 2)->nullable();
                $table->timestamps();
            });
        }

        // 6. Projects Header
        if (!Schema::hasTable('hvac_projects')) {
            Schema::create('hvac_projects', function (Blueprint $table) {
                $table->id();
                $table->string('project_code', 50)->unique();
                $table->string('project_name', 200);
                $table->integer('id_client')->nullable(); // existing client table
                $table->string('customer_name', 200)->nullable();
                $table->integer('id_sales')->nullable(); // users table
                $table->integer('id_engineer')->nullable(); // users table
                $table->string('location', 255)->nullable();
                $table->decimal('design_outdoor_temp', 5, 2)->default(33.00);
                $table->decimal('design_outdoor_rh', 5, 2)->default(75.00);
                $table->enum('status', ['draft', 'calculated', 'approved', 'quoted'])->default('draft');
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index('id_client');
                $table->index('id_sales');
                $table->index('status');
            });
        }

        // 7. Rooms
        if (!Schema::hasTable('hvac_rooms')) {
            Schema::create('hvac_rooms', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_hvac_project');
                $table->string('room_name', 150);
                $table->enum('calculation_mode', ['quick', 'detailed'])->default('detailed');
                $table->string('room_type', 100)->nullable();

                // Dimensions
                $table->decimal('length', 8, 2);
                $table->decimal('width', 8, 2);
                $table->decimal('height', 8, 2);
                $table->decimal('floor_area', 10, 2)->default(0);
                $table->decimal('room_volume', 10, 2)->default(0);

                // Design Conditions
                $table->decimal('indoor_temp', 5, 2)->default(24.00);
                $table->decimal('indoor_rh', 5, 2)->default(50.00);
                $table->decimal('outdoor_temp', 5, 2)->default(33.00);
                $table->decimal('outdoor_rh', 5, 2)->default(75.00);

                // Quick Calculation Parameters
                $table->integer('quick_occupants_count')->default(0);
                $table->enum('quick_room_condition', ['standard', 'light_insulated', 'glass_heavy', 'high_heat'])->default('standard');
                $table->enum('quick_sun_exposure', ['low', 'medium', 'high'])->default('medium');

                $table->decimal('safety_factor_percent', 5, 2)->default(10.00);
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('id_hvac_project')->references('id')->on('hvac_projects')->onDelete('cascade');
            });
        }

        // 8. Room Walls
        if (!Schema::hasTable('hvac_room_walls')) {
            Schema::create('hvac_room_walls', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_hvac_room');
                $table->string('wall_name', 100);
                $table->enum('orientation', ['N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW', 'Internal'])->default('N');
                $table->boolean('is_external')->default(true);
                $table->decimal('length', 8, 2)->default(0);
                $table->decimal('height', 8, 2)->default(0);
                $table->decimal('gross_area', 10, 2)->default(0);
                $table->decimal('window_deduction_area', 10, 2)->default(0);
                $table->decimal('net_area', 10, 2)->default(0);
                $table->unsignedBigInteger('id_material')->nullable();
                $table->decimal('u_value', 8, 4)->default(2.80);
                $table->decimal('temp_difference', 6, 2)->default(9.00);
                $table->decimal('calculated_sensible_load_w', 10, 2)->default(0);
                $table->timestamps();

                $table->foreign('id_hvac_room')->references('id')->on('hvac_rooms')->onDelete('cascade');
            });
        }

        // 9. Room Roofs
        if (!Schema::hasTable('hvac_room_roofs')) {
            Schema::create('hvac_room_roofs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_hvac_room');
                $table->string('roof_name', 100);
                $table->decimal('area', 10, 2)->default(0);
                $table->unsignedBigInteger('id_material')->nullable();
                $table->decimal('u_value', 8, 4)->default(1.50);
                $table->boolean('is_exposed_to_sun')->default(true);
                $table->decimal('temp_difference', 6, 2)->default(15.00);
                $table->decimal('calculated_sensible_load_w', 10, 2)->default(0);
                $table->timestamps();

                $table->foreign('id_hvac_room')->references('id')->on('hvac_rooms')->onDelete('cascade');
            });
        }

        // 10. Room Windows / Glass
        if (!Schema::hasTable('hvac_room_windows')) {
            Schema::create('hvac_room_windows', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_hvac_room');
                $table->string('window_name', 100);
                $table->decimal('width', 8, 2)->default(0);
                $table->decimal('height', 8, 2)->default(0);
                $table->integer('quantity')->default(1);
                $table->decimal('total_area', 10, 2)->default(0);
                $table->enum('orientation', ['N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW'])->default('N');
                $table->unsignedBigInteger('id_glass_type')->nullable();
                $table->decimal('u_value', 8, 4)->default(5.80);
                $table->decimal('shgc', 4, 3)->default(0.820);
                $table->decimal('internal_shading_factor', 4, 2)->default(1.00);
                $table->decimal('solar_irradiance_w_m2', 8, 2)->default(300.00);
                $table->decimal('calculated_conduction_load_w', 10, 2)->default(0);
                $table->decimal('calculated_solar_load_w', 10, 2)->default(0);
                $table->decimal('calculated_total_load_w', 10, 2)->default(0);
                $table->timestamps();

                $table->foreign('id_hvac_room')->references('id')->on('hvac_rooms')->onDelete('cascade');
            });
        }

        // 11. Room Occupants
        if (!Schema::hasTable('hvac_room_occupants')) {
            Schema::create('hvac_room_occupants', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_hvac_room');
                $table->unsignedBigInteger('id_activity')->nullable();
                $table->string('activity_name', 100);
                $table->integer('quantity')->default(1);
                $table->decimal('sensible_watt_per_person', 8, 2)->default(75.00);
                $table->decimal('latent_watt_per_person', 8, 2)->default(55.00);
                $table->decimal('calculated_sensible_load_w', 10, 2)->default(0);
                $table->decimal('calculated_latent_load_w', 10, 2)->default(0);
                $table->timestamps();

                $table->foreign('id_hvac_room')->references('id')->on('hvac_rooms')->onDelete('cascade');
            });
        }

        // 12. Room Lightings
        if (!Schema::hasTable('hvac_room_lightings')) {
            Schema::create('hvac_room_lightings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_hvac_room');
                $table->string('lighting_name', 100);
                $table->integer('quantity')->default(1);
                $table->decimal('watt_per_unit', 8, 2)->default(20.00);
                $table->decimal('ballast_factor', 4, 2)->default(1.00);
                $table->decimal('usage_factor', 4, 2)->default(1.00);
                $table->decimal('calculated_sensible_load_w', 10, 2)->default(0);
                $table->timestamps();

                $table->foreign('id_hvac_room')->references('id')->on('hvac_rooms')->onDelete('cascade');
            });
        }

        // 13. Room Equipments
        if (!Schema::hasTable('hvac_room_equipments')) {
            Schema::create('hvac_room_equipments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_hvac_room');
                $table->string('equipment_name', 100);
                $table->integer('quantity')->default(1);
                $table->decimal('watt_per_unit', 8, 2)->default(150.00);
                $table->decimal('usage_factor', 4, 2)->default(0.80);
                $table->decimal('sensible_fraction', 4, 2)->default(1.00);
                $table->decimal('latent_fraction', 4, 2)->default(0.00);
                $table->decimal('calculated_sensible_load_w', 10, 2)->default(0);
                $table->decimal('calculated_latent_load_w', 10, 2)->default(0);
                $table->timestamps();

                $table->foreign('id_hvac_room')->references('id')->on('hvac_rooms')->onDelete('cascade');
            });
        }

        // 14. Room Ventilations
        if (!Schema::hasTable('hvac_room_ventilations')) {
            Schema::create('hvac_room_ventilations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_hvac_room');
                $table->enum('method', ['per_person', 'ach', 'direct_airflow'])->default('per_person');
                $table->decimal('input_value', 8, 2)->default(10.00); // e.g. 10 L/s/person
                $table->decimal('airflow_ls', 8, 2)->default(0);
                $table->decimal('airflow_cfm', 8, 2)->default(0);
                $table->decimal('calculated_sensible_load_w', 10, 2)->default(0);
                $table->decimal('calculated_latent_load_w', 10, 2)->default(0);
                $table->timestamps();

                $table->foreign('id_hvac_room')->references('id')->on('hvac_rooms')->onDelete('cascade');
            });
        }

        // 15. Room Infiltrations
        if (!Schema::hasTable('hvac_room_infiltrations')) {
            Schema::create('hvac_room_infiltrations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_hvac_room');
                $table->enum('method', ['ach', 'cfm'])->default('ach');
                $table->decimal('ach_value', 5, 2)->default(0.50);
                $table->decimal('airflow_ls', 8, 2)->default(0);
                $table->decimal('calculated_sensible_load_w', 10, 2)->default(0);
                $table->decimal('calculated_latent_load_w', 10, 2)->default(0);
                $table->timestamps();

                $table->foreign('id_hvac_room')->references('id')->on('hvac_rooms')->onDelete('cascade');
            });
        }

        // 16. Calculation Results
        if (!Schema::hasTable('hvac_calculation_results')) {
            Schema::create('hvac_calculation_results', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_hvac_room')->unique();

                // Breakdown Sensible (Watt)
                $table->decimal('wall_sensible_w', 10, 2)->default(0);
                $table->decimal('roof_sensible_w', 10, 2)->default(0);
                $table->decimal('glass_conduction_sensible_w', 10, 2)->default(0);
                $table->decimal('glass_solar_sensible_w', 10, 2)->default(0);
                $table->decimal('occupant_sensible_w', 10, 2)->default(0);
                $table->decimal('lighting_sensible_w', 10, 2)->default(0);
                $table->decimal('equipment_sensible_w', 10, 2)->default(0);
                $table->decimal('ventilation_sensible_w', 10, 2)->default(0);
                $table->decimal('infiltration_sensible_w', 10, 2)->default(0);
                $table->decimal('total_sensible_load_w', 10, 2)->default(0);

                // Breakdown Latent (Watt)
                $table->decimal('occupant_latent_w', 10, 2)->default(0);
                $table->decimal('equipment_latent_w', 10, 2)->default(0);
                $table->decimal('ventilation_latent_w', 10, 2)->default(0);
                $table->decimal('infiltration_latent_w', 10, 2)->default(0);
                $table->decimal('total_latent_load_w', 10, 2)->default(0);

                // Totals
                $table->decimal('subtotal_load_w', 10, 2)->default(0);
                $table->decimal('safety_factor_percent', 5, 2)->default(10.00);
                $table->decimal('design_load_w', 10, 2)->default(0);
                $table->decimal('sensible_heat_ratio', 4, 3)->default(1.000); // SHR

                // Conversions
                $table->decimal('design_load_btuh', 12, 2)->default(0);
                $table->decimal('design_load_kw', 10, 2)->default(0);
                $table->decimal('design_load_tr', 8, 2)->default(0);
                $table->decimal('design_load_pk', 8, 2)->default(0);

                // Recommendations
                $table->unsignedBigInteger('id_recommended_ac')->nullable();
                $table->string('recommended_ac_model', 150)->nullable();
                $table->decimal('recommended_ac_capacity_btuh', 10, 2)->nullable();
                $table->decimal('recommended_ac_pk', 4, 2)->nullable();
                $table->integer('recommended_unit_qty')->default(1);
                $table->text('recommendation_notes')->nullable();

                $table->timestamps();

                $table->foreign('id_hvac_room')->references('id')->on('hvac_rooms')->onDelete('cascade');
                $table->foreign('id_recommended_ac')->references('id')->on('hvac_ac_catalog')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hvac_calculation_results');
        Schema::dropIfExists('hvac_room_infiltrations');
        Schema::dropIfExists('hvac_room_ventilations');
        Schema::dropIfExists('hvac_room_equipments');
        Schema::dropIfExists('hvac_room_lightings');
        Schema::dropIfExists('hvac_room_occupants');
        Schema::dropIfExists('hvac_room_windows');
        Schema::dropIfExists('hvac_room_roofs');
        Schema::dropIfExists('hvac_room_walls');
        Schema::dropIfExists('hvac_rooms');
        Schema::dropIfExists('hvac_projects');
        Schema::dropIfExists('hvac_city_design_temps');
        Schema::dropIfExists('hvac_ac_catalog');
        Schema::dropIfExists('hvac_activity_loads');
        Schema::dropIfExists('hvac_glass_types');
        Schema::dropIfExists('hvac_materials');
    }
};
