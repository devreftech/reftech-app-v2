<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Bank;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('bank', 'entity')) {
            Schema::table('bank', function (Blueprint $table) {
                $table->string('entity', 50)->default('Reftech')->after('atas_nama')->nullable();
            });
        }

        // Set entity for existing accounts
        Bank::where('description', 'like', '%Kojisha%')
            ->orWhere('atas_nama', 'like', '%Kojisha%')
            ->orWhere('atas_nama', 'like', '%Regita%')
            ->orWhere('no_rek', '5223876543')
            ->orWhere('no_rek', '1560239137')
            ->update(['entity' => 'Kojisha']);

        Bank::whereNull('entity')
            ->orWhere('entity', '')
            ->update(['entity' => 'Reftech']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('bank', 'entity')) {
            Schema::table('bank', function (Blueprint $table) {
                $table->dropColumn('entity');
            });
        }
    }
};
