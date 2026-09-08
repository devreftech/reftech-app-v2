<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HvacMasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Materials
        $materials = [
            // Walls
            ['category' => 'wall', 'material_name' => 'Dinding Bata Merah Plester Luar-Dalam (15 cm)', 'description' => 'Standar dinding bata merah plester aci', 'u_value' => 2.80, 'default_cltd' => 9.00],
            ['category' => 'wall', 'material_name' => 'Dinding Bata Ringan (Hebel) 10 cm', 'description' => 'Bata ringan AAC 10 cm dengan acian thin bed', 'u_value' => 1.25, 'default_cltd' => 7.00],
            ['category' => 'wall', 'material_name' => 'Dinding Beton Bertulang 15 cm', 'description' => 'Beton bertulang tanpa insulasi', 'u_value' => 3.20, 'default_cltd' => 11.00],
            ['category' => 'wall', 'material_name' => 'Sandwich Panel PU 50 mm (Cleanroom/Cold)', 'description' => 'Polyurethane sandwich panel dinding insulasi tinggi', 'u_value' => 0.45, 'default_cltd' => 3.00],
            ['category' => 'partition', 'material_name' => 'Partisi Gypsum 2 Sisi Rangka Hollow', 'description' => 'Gypsum board 9mm dua sisi tanpa insulasi', 'u_value' => 1.80, 'default_cltd' => 4.00],
            ['category' => 'partition', 'material_name' => 'Partisi Gypsum + Rockwool Insulation', 'description' => 'Gypsum board dengan isian rockwool 50mm', 'u_value' => 0.65, 'default_cltd' => 2.50],
            
            // Roofs & Ceilings
            ['category' => 'roof', 'material_name' => 'Atap Spandek / Seng Tanpa Insulasi', 'description' => 'Atap metal sheet langsung ke ruangan', 'u_value' => 6.00, 'default_cltd' => 28.00],
            ['category' => 'roof', 'material_name' => 'Atap Spandek + Glasswool / Bubble Foil 50mm', 'description' => 'Atap metal dengan peredam panas standar pabrik', 'u_value' => 0.75, 'default_cltd' => 12.00],
            ['category' => 'roof', 'material_name' => 'Atap Genteng Keramik / Beton + Plafon', 'description' => 'Konstruksi atap rumah/gedung konvensional', 'u_value' => 1.40, 'default_cltd' => 14.00],
            ['category' => 'ceiling', 'material_name' => 'Plafon Gypsum Board 9 mm Ruang Antara Atap', 'description' => 'Plafon dengan rongga udara di bawah atap', 'u_value' => 1.50, 'default_cltd' => 6.00],
            ['category' => 'ceiling', 'material_name' => 'Dak Beton Bertulang 12 cm (Exposed)', 'description' => 'Lantai dak beton terbuka ke udara luar', 'u_value' => 3.10, 'default_cltd' => 22.00],
        ];

        foreach ($materials as $m) {
            DB::table('hvac_materials')->updateOrInsert(
                ['material_name' => $m['material_name']],
                array_merge($m, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        // 2. Glass Types
        $glasses = [
            ['glass_name' => 'Single Clear Glass 6 mm (Kaca Bening)', 'u_value' => 5.80, 'shgc' => 0.820, 'shading_coefficient' => 0.94, 'description' => 'Kaca polos standar rumah dan ruko'],
            ['glass_name' => 'Single Tinted / Rayban Grey 6 mm', 'u_value' => 5.70, 'shgc' => 0.580, 'shading_coefficient' => 0.67, 'description' => 'Kaca riben abu-abu penahan panas'],
            ['glass_name' => 'Single Reflective / One-Way 6 mm', 'u_value' => 5.20, 'shgc' => 0.420, 'shading_coefficient' => 0.48, 'description' => 'Kaca cermin / stopsol penolak radiasi matahari'],
            ['glass_name' => 'Single Low-E 6 mm', 'u_value' => 3.60, 'shgc' => 0.400, 'shading_coefficient' => 0.46, 'description' => 'Kaca Low-Emissivity efisiensi energi'],
            ['glass_name' => 'Double Glazed Clear (6 + 12A + 6 mm)', 'u_value' => 2.70, 'shgc' => 0.700, 'shading_coefficient' => 0.80, 'description' => 'Kaca ganda kedap suara dan panas'],
            ['glass_name' => 'Double Glazed Low-E (6 + 12A + 6 mm)', 'u_value' => 1.80, 'shgc' => 0.350, 'shading_coefficient' => 0.40, 'description' => 'Kaca ganda Low-E performa tinggi untuk gedung modern'],
        ];

        foreach ($glasses as $g) {
            DB::table('hvac_glass_types')->updateOrInsert(
                ['glass_name' => $g['glass_name']],
                array_merge($g, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        // 3. Activity Loads (Penghuni)
        $activities = [
            ['activity_name' => 'Duduk Santai / Teater / Bioskop', 'sensible_watt' => 65.00, 'latent_watt' => 35.00, 'description' => 'Aktivitas sangat ringan, laju metabolisme rendah'],
            ['activity_name' => 'Kerja Kantor / Bank / Ruang Rapat', 'sensible_watt' => 75.00, 'latent_watt' => 55.00, 'description' => 'Standar perkantoran, mengetik, membaca, diskusi'],
            ['activity_name' => 'Berdiri / Berjalan Santai (Retail / Mall)', 'sensible_watt' => 80.00, 'latent_watt' => 80.00, 'description' => 'Pertokoan, supermarket, perpustakaan aktif'],
            ['activity_name' => 'Restoran / Kafe / Ballroom', 'sensible_watt' => 90.00, 'latent_watt' => 95.00, 'description' => 'Makan minum, bergerak sedang, percakapan aktif'],
            ['activity_name' => 'Kerja Ringan Pabrik / Bengkel Elektronik', 'sensible_watt' => 110.00, 'latent_watt' => 135.00, 'description' => 'Perakitan ringan, banyak bergerak berdiri'],
            ['activity_name' => 'Kerja Berat / Gudang / Pabrik Mesin', 'sensible_watt' => 170.00, 'latent_watt' => 255.00, 'description' => 'Aktivitas fisik tinggi, keringat berlebih'],
            ['activity_name' => 'Pusat Kebugaran / Gym / Olahraga', 'sensible_watt' => 210.00, 'latent_watt' => 315.00, 'description' => 'Latihan intensif, beban laten sangat dominan'],
        ];

        foreach ($activities as $a) {
            DB::table('hvac_activity_loads')->updateOrInsert(
                ['activity_name' => $a['activity_name']],
                array_merge($a, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        // 4. AC Catalog
        $acUnits = [
            ['brand' => 'Daikin', 'model_name' => 'FTKQ15 (Inverter Split Wall)', 'ac_type' => 'split_wall', 'nominal_pk' => 0.5, 'cooling_capacity_btuh' => 5100, 'cooling_capacity_kw' => 1.49, 'power_input_watt' => 370, 'refrigerant' => 'R32', 'energy_rating' => '5 Star'],
            ['brand' => 'Daikin', 'model_name' => 'FTKQ20 (Inverter Split Wall)', 'ac_type' => 'split_wall', 'nominal_pk' => 0.75, 'cooling_capacity_btuh' => 6800, 'cooling_capacity_kw' => 1.99, 'power_input_watt' => 510, 'refrigerant' => 'R32', 'energy_rating' => '5 Star'],
            ['brand' => 'Daikin', 'model_name' => 'FTKQ25 (Inverter Split Wall)', 'ac_type' => 'split_wall', 'nominal_pk' => 1.0, 'cooling_capacity_btuh' => 9000, 'cooling_capacity_kw' => 2.64, 'power_input_watt' => 680, 'refrigerant' => 'R32', 'energy_rating' => '5 Star'],
            ['brand' => 'Daikin', 'model_name' => 'FTKQ35 (Inverter Split Wall)', 'ac_type' => 'split_wall', 'nominal_pk' => 1.5, 'cooling_capacity_btuh' => 11900, 'cooling_capacity_kw' => 3.49, 'power_input_watt' => 960, 'refrigerant' => 'R32', 'energy_rating' => '5 Star'],
            ['brand' => 'Daikin', 'model_name' => 'FTKQ50 (Inverter Split Wall)', 'ac_type' => 'split_wall', 'nominal_pk' => 2.0, 'cooling_capacity_btuh' => 17700, 'cooling_capacity_kw' => 5.19, 'power_input_watt' => 1450, 'refrigerant' => 'R32', 'energy_rating' => '5 Star'],
            ['brand' => 'Daikin', 'model_name' => 'FTKQ60 (Inverter Split Wall)', 'ac_type' => 'split_wall', 'nominal_pk' => 2.5, 'cooling_capacity_btuh' => 20500, 'cooling_capacity_kw' => 6.01, 'power_input_watt' => 1750, 'refrigerant' => 'R32', 'energy_rating' => '5 Star'],
            ['brand' => 'Daikin', 'model_name' => 'FCFC50 (Inverter Cassette 4-Way)', 'ac_type' => 'cassette', 'nominal_pk' => 2.0, 'cooling_capacity_btuh' => 18000, 'cooling_capacity_kw' => 5.27, 'power_input_watt' => 1520, 'refrigerant' => 'R32', 'energy_rating' => 'Inverter Commercial'],
            ['brand' => 'Daikin', 'model_name' => 'FCFC71 (Inverter Cassette 4-Way)', 'ac_type' => 'cassette', 'nominal_pk' => 3.0, 'cooling_capacity_btuh' => 24200, 'cooling_capacity_kw' => 7.09, 'power_input_watt' => 2180, 'refrigerant' => 'R32', 'energy_rating' => 'Inverter Commercial'],
            ['brand' => 'Daikin', 'model_name' => 'FCFC100 (Inverter Cassette 4-Way)', 'ac_type' => 'cassette', 'nominal_pk' => 4.0, 'cooling_capacity_btuh' => 34100, 'cooling_capacity_kw' => 9.99, 'power_input_watt' => 3120, 'refrigerant' => 'R32', 'energy_rating' => 'Inverter Commercial'],
            ['brand' => 'Daikin', 'model_name' => 'FCFC125 (Inverter Cassette 4-Way)', 'ac_type' => 'cassette', 'nominal_pk' => 5.0, 'cooling_capacity_btuh' => 42700, 'cooling_capacity_kw' => 12.51, 'power_input_watt' => 3890, 'refrigerant' => 'R32', 'energy_rating' => 'Inverter Commercial'],
            ['brand' => 'Panasonic', 'model_name' => 'CS-PN9WKJ (Standard Split)', 'ac_type' => 'split_wall', 'nominal_pk' => 1.0, 'cooling_capacity_btuh' => 9000, 'cooling_capacity_kw' => 2.64, 'power_input_watt' => 760, 'refrigerant' => 'R32', 'energy_rating' => '4 Star'],
            ['brand' => 'Panasonic', 'model_name' => 'CS-PN18WKJ (Standard Split)', 'ac_type' => 'split_wall', 'nominal_pk' => 2.0, 'cooling_capacity_btuh' => 18000, 'cooling_capacity_kw' => 5.27, 'power_input_watt' => 1660, 'refrigerant' => 'R32', 'energy_rating' => '4 Star'],
            ['brand' => 'Gree', 'model_name' => 'GVC-48TS (Floor Standing Heavy Duty)', 'ac_type' => 'floor_standing', 'nominal_pk' => 5.0, 'cooling_capacity_btuh' => 48000, 'cooling_capacity_kw' => 14.06, 'power_input_watt' => 4600, 'refrigerant' => 'R410A', 'energy_rating' => 'Commercial'],
            ['brand' => 'Gree', 'model_name' => 'GUD140 (Ceiling Ducted High Static)', 'ac_type' => 'ceiling_ducted', 'nominal_pk' => 5.0, 'cooling_capacity_btuh' => 48000, 'cooling_capacity_kw' => 14.06, 'power_input_watt' => 4500, 'refrigerant' => 'R410A', 'energy_rating' => 'Duct Commercial'],
            ['brand' => 'Carrier', 'model_name' => '40QTC060 (Ducted Split Package)', 'ac_type' => 'ceiling_ducted', 'nominal_pk' => 6.0, 'cooling_capacity_btuh' => 58000, 'cooling_capacity_kw' => 17.00, 'power_input_watt' => 5400, 'refrigerant' => 'R410A', 'energy_rating' => 'Industrial'],
            ['brand' => 'Carrier', 'model_name' => '40QTC100 (Ducted Split Package)', 'ac_type' => 'ceiling_ducted', 'nominal_pk' => 10.0, 'cooling_capacity_btuh' => 96000, 'cooling_capacity_kw' => 28.13, 'power_input_watt' => 9200, 'refrigerant' => 'R410A', 'energy_rating' => 'Industrial'],
        ];

        foreach ($acUnits as $u) {
            DB::table('hvac_ac_catalog')->updateOrInsert(
                ['brand' => $u['brand'], 'model_name' => $u['model_name']],
                array_merge($u, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        // 5. City Design Temps
        $cities = [
            ['city_name' => 'Jakarta / Bodetabek', 'outdoor_db_temp' => 33.50, 'outdoor_rh_percent' => 75.00, 'outdoor_wb_temp' => 28.00],
            ['city_name' => 'Surabaya / Sidoarjo / Gresik', 'outdoor_db_temp' => 34.50, 'outdoor_rh_percent' => 70.00, 'outdoor_wb_temp' => 28.50],
            ['city_name' => 'Bandung (Dataran Tinggi)', 'outdoor_db_temp' => 29.00, 'outdoor_rh_percent' => 78.00, 'outdoor_wb_temp' => 24.50],
            ['city_name' => 'Semarang (Pesisir)', 'outdoor_db_temp' => 34.00, 'outdoor_rh_percent' => 75.00, 'outdoor_wb_temp' => 28.50],
            ['city_name' => 'Medan', 'outdoor_db_temp' => 33.00, 'outdoor_rh_percent' => 78.00, 'outdoor_wb_temp' => 28.00],
            ['city_name' => 'Cikarang / Karawang (Kawasan Industri)', 'outdoor_db_temp' => 34.00, 'outdoor_rh_percent' => 74.00, 'outdoor_wb_temp' => 28.20],
        ];

        foreach ($cities as $c) {
            DB::table('hvac_city_design_temps')->updateOrInsert(
                ['city_name' => $c['city_name']],
                array_merge($c, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}
