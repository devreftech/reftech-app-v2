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
        if (!Schema::hasTable('user_mail_settings')) {
            Schema::create('user_mail_settings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->unique();
                $table->string('mail_driver')->default('smtp');
                
                // SMTP Config
                $table->string('smtp_host')->nullable()->default('smtp.gmail.com');
                $table->integer('smtp_port')->nullable()->default(587);
                $table->string('smtp_encryption')->nullable()->default('tls');
                $table->string('smtp_username')->nullable();
                $table->text('smtp_password')->nullable(); // Encrypted
                
                // IMAP Config (for incoming emails)
                $table->string('imap_host')->nullable()->default('imap.gmail.com');
                $table->integer('imap_port')->nullable()->default(993);
                $table->string('imap_encryption')->nullable()->default('ssl');
                $table->string('imap_username')->nullable();
                $table->text('imap_password')->nullable(); // Encrypted

                // Sender display & Signature
                $table->string('from_name')->nullable();
                $table->string('from_address')->nullable();
                $table->string('signature_layout')->nullable()->default('sig_corporate');
                $table->string('signature_color')->nullable()->default('#696cff');
                $table->text('signature_html')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamp('last_synced_at')->nullable();
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_mail_settings');
    }
};
