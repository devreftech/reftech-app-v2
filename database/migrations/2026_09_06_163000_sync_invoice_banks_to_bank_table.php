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
        // Ensure columns exist first
        Schema::table('bank', function (Blueprint $table) {
            if (!Schema::hasColumn('bank', 'initial_balance')) {
                $table->decimal('initial_balance', 18, 2)->default(0)->after('bank');
            }
            if (!Schema::hasColumn('bank', 'atas_nama')) {
                $table->string('atas_nama')->nullable()->after('no_rek');
            }
            if (!Schema::hasColumn('bank', 'branch')) {
                $table->string('branch')->nullable()->after('atas_nama');
            }
            if (!Schema::hasColumn('bank', 'is_active')) {
                $table->boolean('is_active')->default(1)->after('saldo');
            }
            if (!Schema::hasColumn('bank', 'description')) {
                $table->text('description')->nullable()->after('is_active');
            }
        });

        // 1. Update ID 1 (Mandiri)
        $b1 = Bank::find(1);
        if ($b1) {
            $b1->update([
                'bank' => 'Mandiri',
                'atas_nama' => 'PT. Refrigerasi Teknik Indonesia',
                'description' => 'Rekening Operasional Mandiri',
                'is_active' => true,
            ]);
        }

        // 2. Update ID 2 (BCA Reftech PPN)
        $b2 = Bank::find(2);
        if ($b2) {
            $b2->update([
                'bank' => 'BCA',
                'no_rek' => '008-6289-789',
                'atas_nama' => 'PT. REFTECH JAYA OPTIMA',
                'description' => 'Rekening PPN Reftech (Swift: CENAIDJA)',
                'is_active' => true,
            ]);
        }

        // 3. Update ID 3 (BRI)
        $b3 = Bank::find(3);
        if ($b3) {
            $b3->update([
                'bank' => 'BRI',
                'atas_nama' => 'PT. Refrigerasi Teknik Indonesia',
                'description' => 'Rekening Operasional BRI',
                'is_active' => true,
            ]);
        }

        // 4. BCA Reftech Non-PPN (ARIEP RACHMAN - 166-2242-271)
        Bank::firstOrCreate(
            ['no_rek' => '166-2242-271'],
            [
                'bank' => 'BCA',
                'atas_nama' => 'ARIEP RACHMAN',
                'branch' => 'Bandung',
                'initial_balance' => 0,
                'saldo' => 0,
                'is_active' => true,
                'description' => 'Rekening Non-PPN Reftech'
            ]
        );

        // 5. BCA Kojisha PPN (KOJISHA INNOTIV INDONESIA PT - 5223876543)
        Bank::firstOrCreate(
            ['no_rek' => '5223876543'],
            [
                'bank' => 'BCA',
                'atas_nama' => 'KOJISHA INNOTIV INDONESIA PT',
                'branch' => 'Bandung',
                'initial_balance' => 0,
                'saldo' => 0,
                'is_active' => true,
                'description' => 'Rekening PPN Kojisha'
            ]
        );

        // 6. BCA Kojisha Non-PPN (REGITA DWI MELINDA - 1560239137)
        Bank::firstOrCreate(
            ['no_rek' => '1560239137'],
            [
                'bank' => 'BCA',
                'atas_nama' => 'REGITA DWI MELINDA',
                'branch' => 'Bandung',
                'initial_balance' => 0,
                'saldo' => 0,
                'is_active' => true,
                'description' => 'Rekening Non-PPN Kojisha'
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse needed
    }
};
