<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE client MODIFY COLUMN area VARCHAR(50) NOT NULL');
    }

    public function down()
    {
        DB::statement('ALTER TABLE client MODIFY COLUMN area VARCHAR(20) NOT NULL');
    }
};
