<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE users MODIFY role ENUM('Sales','Technician','Admin','Logistic','Accounting','Supervisor','Coordinator','Support','ServiceM','Client','Finance Manager','Developer','Project Manager') NOT NULL DEFAULT 'Sales'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE users MODIFY role ENUM('Sales','Technician','Admin','Logistic','Accounting','Supervisor','Coordinator','Support','ServiceM','Client','Finance Manager','Developer') NOT NULL DEFAULT 'Sales'");
    }
};
