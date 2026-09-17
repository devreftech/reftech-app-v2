<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE `product_in` MODIFY `date_payment` DATE NULL DEFAULT NULL');
    }

    public function down()
    {
        DB::statement('ALTER TABLE `product_in` MODIFY `date_payment` DATE NOT NULL DEFAULT CURRENT_DATE()');
    }
};
