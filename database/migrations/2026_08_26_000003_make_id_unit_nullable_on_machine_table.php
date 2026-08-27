<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Machine "Dummy" hasil quick-add teknisi di Service Report sengaja gak
        // di-link ke katalog Unit (id_unit null) — sebelumnya kolom ini NOT NULL,
        // jadi harus dilonggarkan biar fitur itu bisa nyimpen. Raw SQL dipakai
        // karena doctrine/dbal (dibutuhin Schema::table()->change()) belum ke-install.
        DB::statement('ALTER TABLE `machine` MODIFY `id_unit` BIGINT UNSIGNED NULL');
    }

    public function down()
    {
        DB::statement('ALTER TABLE `machine` MODIFY `id_unit` BIGINT UNSIGNED NOT NULL');
    }
};
