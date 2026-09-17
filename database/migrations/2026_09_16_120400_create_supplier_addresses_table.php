<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create('supplier_addresses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_supplier')->index();
            $table->string('name')->nullable(); // e.g. 'Kantor Pusat', 'Gudang Utama', 'Pabrik', etc.
            $table->text('address');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->foreign('id_supplier')->references('id')->on('supplier')->onDelete('cascade');
        });

        // Migrasi alamat lama dari tabel supplier ke tabel supplier_addresses
        try {
            $suppliers = DB::table('supplier')
                ->whereNotNull('address')
                ->where('address', '!=', '')
                ->where('address', '!=', '-')
                ->select('id', 'address')
                ->get();

            $now = now();
            $inserts = [];
            foreach ($suppliers as $s) {
                $trimmed = trim($s->address);
                if (!empty($trimmed)) {
                    $inserts[] = [
                        'id_supplier' => $s->id,
                        'name' => 'Alamat Utama',
                        'address' => $trimmed,
                        'is_primary' => true,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            if (!empty($inserts)) {
                foreach (array_chunk($inserts, 200) as $chunk) {
                    DB::table('supplier_addresses')->insert($chunk);
                }
            }
        } catch (\Throwable $e) {
            // Ignore if error during data migration
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('supplier_addresses');
    }
};
