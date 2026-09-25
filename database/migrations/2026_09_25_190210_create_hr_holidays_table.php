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
        Schema::create('hr_holidays', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('holiday_date')->unique();
            $table->enum('type', ['National', 'Joint_Leave', 'Company'])->default('National');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert default Indonesian National Holidays 2026 / Common Presets
        $defaultHolidays = [
            ['name' => 'Tahun Baru Masehi 2026', 'holiday_date' => '2026-01-01', 'type' => 'National', 'description' => 'Libur Nasional Tahun Baru'],
            ['name' => 'Isra Mi\'raj Nabi Muhammad SAW', 'holiday_date' => '2026-01-16', 'type' => 'National', 'description' => 'Hari Besar Keagamaan'],
            ['name' => 'Tahun Baru Imlek 2577 Kongzili', 'holiday_date' => '2026-02-17', 'type' => 'National', 'description' => 'Tahun Baru Imlek'],
            ['name' => 'Hari Suci Nyepi Tahun Baru Saka 1948', 'holiday_date' => '2026-03-21', 'type' => 'National', 'description' => 'Hari Suci Nyepi'],
            ['name' => 'Hari Raya Idul Fitri 1447 H (Hari 1)', 'holiday_date' => '2026-03-20', 'type' => 'National', 'description' => 'Hari Raya Idul Fitri'],
            ['name' => 'Hari Raya Idul Fitri 1447 H (Hari 2)', 'holiday_date' => '2026-03-21', 'type' => 'National', 'description' => 'Hari Raya Idul Fitri'],
            ['name' => 'Cuti Bersama Idul Fitri', 'holiday_date' => '2026-03-23', 'type' => 'Joint_Leave', 'description' => 'Cuti Bersama Hari Raya Idul Fitri'],
            ['name' => 'Wafat Yesus Kristus', 'holiday_date' => '2026-04-03', 'type' => 'National', 'description' => 'Hari Wafat Yesus Kristus'],
            ['name' => 'Hari Buruh Internasional', 'holiday_date' => '2026-05-01', 'type' => 'National', 'description' => 'May Day'],
            ['name' => 'Kenaikan Yesus Kristus', 'holiday_date' => '2026-05-14', 'type' => 'National', 'description' => 'Hari Kenaikan Yesus Kristus'],
            ['name' => 'Hari Raya Waisak 2570 BE', 'holiday_date' => '2026-05-31', 'type' => 'National', 'description' => 'Hari Raya Waisak'],
            ['name' => 'Hari Lahir Pancasila', 'holiday_date' => '2026-06-01', 'type' => 'National', 'description' => 'Hari Lahir Pancasila'],
            ['name' => 'Hari Raya Idul Adha 1447 H', 'holiday_date' => '2026-05-27', 'type' => 'National', 'description' => 'Hari Raya Kurban'],
            ['name' => 'Tahun Baru Islam 1448 Hijriah', 'holiday_date' => '2026-06-16', 'type' => 'National', 'description' => '1 Muharram'],
            ['name' => 'Hari Kemerdekaan Republik Indonesia ke-81', 'holiday_date' => '2026-08-17', 'type' => 'National', 'description' => 'HUT RI ke-81'],
            ['name' => 'Maulid Nabi Muhammad SAW', 'holiday_date' => '2026-08-25', 'type' => 'National', 'description' => 'Maulid Nabi'],
            ['name' => 'Hari Raya Natal', 'holiday_date' => '2026-12-25', 'type' => 'National', 'description' => 'Hari Raya Natal'],
            ['name' => 'Cuti Bersama Hari Raya Natal', 'holiday_date' => '2026-12-26', 'type' => 'Joint_Leave', 'description' => 'Cuti Bersama Natal'],
        ];

        foreach ($defaultHolidays as $item) {
            \Illuminate\Support\Facades\DB::table('hr_holidays')->updateOrInsert(
                ['holiday_date' => $item['holiday_date']],
                [
                    'name' => $item['name'],
                    'type' => $item['type'],
                    'description' => $item['description'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hr_holidays');
    }
};
