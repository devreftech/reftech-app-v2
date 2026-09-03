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
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->boolean('is_edited')->default(false)->after('read_at');
            $table->timestamp('edited_at')->nullable()->after('is_edited');
            $table->text('original_message')->nullable()->after('edited_at');
            $table->boolean('is_deleted')->default(false)->after('original_message');
            $table->timestamp('deleted_at')->nullable()->after('is_deleted');
            $table->unsignedBigInteger('deleted_by')->nullable()->after('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropColumn([
                'is_edited',
                'edited_at',
                'original_message',
                'is_deleted',
                'deleted_at',
                'deleted_by',
            ]);
        });
    }
};
