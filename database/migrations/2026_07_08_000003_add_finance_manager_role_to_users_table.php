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
        DB::statement("ALTER TABLE users MODIFY role ENUM('Sales','Technician','Admin','Logistic','Accounting','Supervisor','Coordinator','Support','ServiceM','Client','Finance Manager') NOT NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE users MODIFY role ENUM('Sales','Technician','Admin','Logistic','Accounting','Supervisor','Coordinator','Support','ServiceM','Client') NOT NULL");
    }
};
