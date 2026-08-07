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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->integer('nip');
            $table->string('name');
            $table->string('email')->unique();
            // $table->date('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phone');
            $table->string('sign')->nullable();
            $table->string('image');
            $table->date('birthday');
            $table->string('address');
            $table->string('code');
            $table->enum('active',['0','1']);
            $table->enum('role',['Sales', 'Technician', 'Admin', 'Logistic', 'Accounting', 'Supervisor', 'Coordinator', 'Support']);
            $table->date('date_in');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
