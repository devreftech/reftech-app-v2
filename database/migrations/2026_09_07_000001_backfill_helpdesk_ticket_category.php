<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Perbaiki data lama: tiket error otomatis (no_ticket "ERR/...") sempat
     * tersimpan dengan category "user_report" karena kolom category belum ada
     * di $fillable model HelpdeskTicket, sehingga mass-assignment membuangnya
     * dan kolom jatuh ke default "user_report". Akibatnya semua temuan error
     * system ikut muncul di tabel Tiket User.
     */
    public function up()
    {
        DB::table('helpdesk_tickets')
            ->where('no_ticket', 'like', 'ERR/%')
            ->where('category', '!=', 'system_error')
            ->update(['category' => 'system_error']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('helpdesk_tickets')
            ->where('no_ticket', 'like', 'ERR/%')
            ->update(['category' => 'user_report']);
    }
};
