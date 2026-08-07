<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client', function (Blueprint $table) {
            $table->id();
            $table->foreignId("id_sales");
            $table->foreignId("id_issues");
            $table->foreignId("id_support")->nullable();
            $table->string('company', 50);
            $table->string('email')->nullable();
            $table->string('phone', 15);
            $table->enum('ru', ['User', 'Reseller']);
            $table->string('web')->nullable();
            $table->string('image')->nullable();
            $table->string('source', 15)->nullable();
            $table->date("created_date")->nullable();
            $table->enum('role', ['Leads', 'Customers'])->default("Leads");
            $table->string("mobile", 14)->nullable();
            $table->string("machine")->nullable();
            $table->string("note")->nullable();
            $table->string("address");
            $table->string("subAddress");
            $table->string("area", 20);
            $table->timestamps();

            $table->index('role', 'idx_role');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()  
    {
        Schema::dropIfExists('client');
    }
};
