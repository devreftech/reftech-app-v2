<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('detail_purchase_order')) {
            DB::statement('ALTER TABLE detail_purchase_order MODIFY price DECIMAL(15, 2) NOT NULL DEFAULT 0.00');
            DB::statement('ALTER TABLE detail_purchase_order MODIFY amount DECIMAL(15, 2) NOT NULL DEFAULT 0.00');
        }

        if (Schema::hasTable('purchase_order')) {
            DB::statement('ALTER TABLE purchase_order MODIFY subtotal DECIMAL(15, 2) NOT NULL DEFAULT 0.00');
            DB::statement('ALTER TABLE purchase_order MODIFY diskon DECIMAL(15, 2) NOT NULL DEFAULT 0.00');
            DB::statement('ALTER TABLE purchase_order MODIFY total DECIMAL(15, 2) NOT NULL DEFAULT 0.00');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('detail_purchase_order')) {
            DB::statement('ALTER TABLE detail_purchase_order MODIFY price INT(11) NOT NULL DEFAULT 0');
            DB::statement('ALTER TABLE detail_purchase_order MODIFY amount INT(11) NOT NULL DEFAULT 0');
        }

        if (Schema::hasTable('purchase_order')) {
            DB::statement('ALTER TABLE purchase_order MODIFY subtotal INT(11) NOT NULL DEFAULT 0');
            DB::statement('ALTER TABLE purchase_order MODIFY diskon INT(11) NOT NULL DEFAULT 0');
            DB::statement('ALTER TABLE purchase_order MODIFY total INT(11) NOT NULL DEFAULT 0');
        }
    }
};
