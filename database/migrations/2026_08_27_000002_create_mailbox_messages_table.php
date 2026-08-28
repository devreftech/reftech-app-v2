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
        if (!Schema::hasTable('mailbox_messages')) {
            Schema::create('mailbox_messages', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->string('folder')->default('inbox')->index(); // inbox, sent, draft, trash
                $table->string('status')->default('Received')->index(); // Delivered, Received, Draft, Failed
                
                // Sender info
                $table->string('sender_name')->nullable();
                $table->string('sender_email')->nullable();
                
                // Recipient info
                $table->string('recipient_name')->nullable();
                $table->string('recipient_email')->nullable();
                $table->text('cc')->nullable();
                $table->text('bcc')->nullable();
                
                // Email content
                $table->string('subject')->nullable();
                $table->text('preview')->nullable();
                $table->longText('body_html')->nullable();
                $table->longText('body_text')->nullable();
                
                // Tags & categorization
                $table->string('tag')->nullable()->default('General / Sales');
                $table->string('tag_color')->nullable()->default('primary');
                
                // Metadata & flags
                $table->boolean('is_read')->default(false)->index();
                $table->boolean('is_starred')->default(false)->index();
                $table->boolean('has_attachment')->default(false);
                $table->string('message_id')->nullable()->index(); // for IMAP/SMTP deduplication
                
                $table->timestamp('sent_at')->nullable();
                $table->timestamp('received_at')->nullable();
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('mailbox_attachments')) {
            Schema::create('mailbox_attachments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('mailbox_message_id')->index();
                $table->string('filename');
                $table->string('file_path');
                $table->string('file_size')->nullable();
                $table->string('file_ext')->nullable();
                $table->string('mime_type')->nullable();
                $table->timestamps();

                $table->foreign('mailbox_message_id')->references('id')->on('mailbox_messages')->onDelete('cascade');
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
        Schema::dropIfExists('mailbox_attachments');
        Schema::dropIfExists('mailbox_messages');
    }
};
