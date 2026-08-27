<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // idx_activities_id_client & idx_client_id_sales sudah ada di DB (dibuat
        // otomatis oleh foreignId()), jadi cuma tambah yang benar-benar belum ada.
        Schema::table('activities', function (Blueprint $table) {
            if (Schema::hasColumn('activities', 'date')) {
                $table->index('date', 'idx_activities_date');
            }
        });

        Schema::table('comment', function (Blueprint $table) {
            if (Schema::hasColumn('comment', 'id_status')) {
                $table->index('id_status', 'idx_comment_id_status');
            }
            if (Schema::hasColumn('comment', 'id_user')) {
                $table->index('id_user', 'idx_comment_id_user');
            }
        });

        Schema::table('client', function (Blueprint $table) {
            if (Schema::hasColumn('client', 'created_at')) {
                $table->index('created_at', 'idx_client_created_at');
            }
        });

        Schema::table('quotation', function (Blueprint $table) {
            if (Schema::hasColumn('quotation', 'po_date')) {
                $table->index('po_date', 'idx_quotation_po_date');
            }
            if (Schema::hasColumn('quotation', 'estimated_date')) {
                $table->index('estimated_date', 'idx_quotation_estimated_date');
            }
        });

        Schema::table('unit_quotation', function (Blueprint $table) {
            if (Schema::hasColumn('unit_quotation', 'po_received')) {
                $table->index('po_received', 'idx_unit_quotation_po_received');
            }
        });
    }

    public function down()
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropIndex('idx_activities_date');
        });

        Schema::table('comment', function (Blueprint $table) {
            $table->dropIndex('idx_comment_id_status');
            $table->dropIndex('idx_comment_id_user');
        });

        Schema::table('client', function (Blueprint $table) {
            $table->dropIndex('idx_client_created_at');
        });

        Schema::table('quotation', function (Blueprint $table) {
            $table->dropIndex('idx_quotation_po_date');
            $table->dropIndex('idx_quotation_estimated_date');
        });

        Schema::table('unit_quotation', function (Blueprint $table) {
            $table->dropIndex('idx_unit_quotation_po_received');
        });
    }
};
