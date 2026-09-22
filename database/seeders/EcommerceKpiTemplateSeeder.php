<?php

namespace Database\Seeders;

use App\Models\EcommerceKpiTemplate;
use Illuminate\Database\Seeder;

class EcommerceKpiTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $templates = [
            [
                'code' => 'REVENUE',
                'name' => 'Pencapaian Omset / Revenue',
                'description' => 'Realisasi omset penjualan / PO masuk dibanding target bulanan',
                'type' => 'automatic',
                'unit' => 'IDR',
                'default_target' => 100000000,
                'default_weight' => 25.00,
                'calculation_handler' => 'revenue',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'code' => 'NEW_PRODUCT',
                'name' => 'Upload Produk Baru / Listing',
                'description' => 'Jumlah produk baru yang di-upload ke marketplace',
                'type' => 'automatic',
                'unit' => 'Qty',
                'default_target' => 100,
                'default_weight' => 15.00,
                'calculation_handler' => 'product_upload',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'code' => 'ORDER_COUNT',
                'name' => 'Jumlah Transaksi / PO Masuk',
                'description' => 'Jumlah transaksi quotation / PO deal yang berhasil dicapai',
                'type' => 'automatic',
                'unit' => 'PO',
                'default_target' => 10,
                'default_weight' => 10.00,
                'calculation_handler' => 'po_count',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'code' => 'SW_UPDATE',
                'name' => 'Aktivitas Broadcast & Status (SW)',
                'description' => 'Frekuensi update status WhatsApp & broadcast penawaran harian',
                'type' => 'automatic',
                'unit' => 'Kali',
                'default_target' => 60,
                'default_weight' => 10.00,
                'calculation_handler' => 'sw_update',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'code' => 'DATA_ACCURACY',
                'name' => 'Akurasi Data Listing & Stok',
                'description' => 'Kesesuaian harga, deskripsi, gambar, dan stok di seluruh channel marketplace',
                'type' => 'hybrid',
                'unit' => '%',
                'default_target' => 100,
                'default_weight' => 10.00,
                'calculation_handler' => 'akurasi',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'code' => 'RESPONSE_CHAT',
                'name' => 'Kecepatan & Kualitas Respon Chat',
                'description' => 'Tingkat kecepatan response time & keramahan penanganan calon pembeli',
                'type' => 'hybrid',
                'unit' => '%',
                'default_target' => 100,
                'default_weight' => 10.00,
                'calculation_handler' => 'response',
                'sort_order' => 6,
                'is_active' => true,
            ],
            [
                'code' => 'DELIVERY_SUCCESS',
                'name' => 'Performa Pengiriman / Fulfillment',
                'description' => 'Ketepatan waktu proses pesanan dan persentase pesanan terkirim sukses',
                'type' => 'hybrid',
                'unit' => '%',
                'default_target' => 100,
                'default_weight' => 10.00,
                'calculation_handler' => 'delivery',
                'sort_order' => 7,
                'is_active' => true,
            ],
            [
                'code' => 'DISCIPLINE_INITIATIVE',
                'name' => 'Inisiatif, Sikap Kerja & Kedisiplinan',
                'description' => 'Penilaian langsung evaluator terhadap inisiatif perbaikan, kerapian toko, dan kedisiplinan',
                'type' => 'manual',
                'unit' => 'Poin',
                'default_target' => 100,
                'default_weight' => 10.00,
                'calculation_handler' => 'manual',
                'sort_order' => 8,
                'is_active' => true,
            ],
        ];

        foreach ($templates as $data) {
            EcommerceKpiTemplate::updateOrCreate(
                ['code' => $data['code']],
                $data
            );
        }
    }
}
