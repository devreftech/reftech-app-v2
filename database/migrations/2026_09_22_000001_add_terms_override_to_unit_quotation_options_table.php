<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// 1 Opsi bisa punya Term & Condition sendiri (beda dari Opsi lain di quotation
// yang sama) — override_terms=false (default) berarti opsi ini tetap pakai
// Note/T&C global dari unit_quotation seperti sebelumnya.
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('unit_quotation_options')) {
            return;
        }

        Schema::table('unit_quotation_options', function (Blueprint $table) {
            if (!Schema::hasColumn('unit_quotation_options', 'override_terms')) {
                $table->boolean('override_terms')->default(false)->after('fee');
            }
            if (!Schema::hasColumn('unit_quotation_options', 'note')) {
                $table->text('note')->nullable()->after('override_terms');
            }
            if (!Schema::hasColumn('unit_quotation_options', 'validity')) {
                $table->string('validity')->nullable()->after('note');
            }
            if (!Schema::hasColumn('unit_quotation_options', 'pricing')) {
                $table->string('pricing')->nullable()->after('validity');
            }
            if (!Schema::hasColumn('unit_quotation_options', 'payment')) {
                $table->string('payment')->nullable()->after('pricing');
            }
            if (!Schema::hasColumn('unit_quotation_options', 'warranty')) {
                $table->string('warranty')->nullable()->after('payment');
            }
            if (!Schema::hasColumn('unit_quotation_options', 'delivery_process')) {
                $table->text('delivery_process')->nullable()->after('warranty');
            }
            if (!Schema::hasColumn('unit_quotation_options', 'rental_terms')) {
                $table->text('rental_terms')->nullable()->after('delivery_process');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('unit_quotation_options')) {
            return;
        }

        Schema::table('unit_quotation_options', function (Blueprint $table) {
            foreach (['override_terms', 'note', 'validity', 'pricing', 'payment', 'warranty', 'delivery_process', 'rental_terms'] as $col) {
                if (Schema::hasColumn('unit_quotation_options', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
