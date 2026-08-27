<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kanban_tasks', function (Blueprint $table) {
            // Link balik ke quotation asal kartu ini, buat tombol "Post to Kanban" di
            // halaman detail quotation — beda dari pending_po_id yang cuma keisi kalau
            // quotation-nya udah punya PO/SO, sedangkan post-to-kanban bisa dipakai
            // dari status manapun.
            $table->unsignedBigInteger('id_unit_quotation')->nullable()->after('pending_po_id');
        });
    }

    public function down(): void
    {
        Schema::table('kanban_tasks', function (Blueprint $table) {
            $table->dropColumn('id_unit_quotation');
        });
    }
};
