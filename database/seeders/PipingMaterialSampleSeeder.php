<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PipingMaterial;
use App\Models\PipingMaterialVendorPrice;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PipingMaterialSampleSeeder extends Seeder
{
    public function run()
    {
        // 1. Ensure Key Piping Suppliers Exist
        $supplierList = [
            'PT. CITRA PIPING TEKNIK'      => 'Distributor Resmi Pipa Baja & Galvanis SNI',
            'PT. AIRNET INDONESIA MAKMUR'  => 'Distributor Spesialis Pipa Aluminium Quick-Fit Airnet',
            'PT. SURYA BAJA UTAMA'         => 'Stockist Stainless Steel & Carbon Steel Sch40',
            'PT. MEGATAMA MULTITEKNIKA'    => 'Supplier Valve, Fitting & Industrial Flanges',
            'HYDRO INDOJAYA PERKASA'       => 'Pneumatic, Tubing & High Pressure Valve',
            'INDO STEEL - TOKOPEDIA'       => 'Penyedia Material Pipa & Support Project',
        ];

        $supplierIds = [];
        foreach ($supplierList as $name => $note) {
            $sup = Supplier::firstOrCreate(
                ['supplier' => $name],
                ['info' => $note]
            );
            $supplierIds[$name] = $sup->id;
        }

        // Fallback to existing suppliers if needed
        $existingSuppliers = Supplier::pluck('id')->toArray();

        // 2. Comprehensive Piping Material Catalog List
        $materialsData = [
            // ==================== 1. PIPA GALVANIS MEDIUM SNI (6 METER) ====================
            [
                'category' => 'pipe',
                'item_name' => 'Pipa Galvanis Medium SNI',
                'material_type' => 'Galvanized Steel',
                'size' => '1/2" (DN15)',
                'connection_type' => 'Drat / Threaded',
                'unit' => 'Batang',
                'length_per_unit' => 6.00,
                'default_waste_percent' => 5.0,
                'notes' => 'Pipa baja galvanis celup panas (HDG) medium SNI panjang 6 meter untuk jalur compressed air & air kerja.',
                'vendors' => [
                    ['name' => 'PT. CITRA PIPING TEKNIK', 'price' => 145000, 'notes' => 'SNI Spindo Tebal 2.6mm', 'is_primary' => true],
                    ['name' => 'INDO STEEL - TOKOPEDIA', 'price' => 152000, 'notes' => 'Ready stok toko', 'is_primary' => false],
                    ['name' => 'PT. SURYA BAJA UTAMA', 'price' => 158000, 'notes' => 'Merk Bakrie / PPI', 'is_primary' => false],
                ]
            ],
            [
                'category' => 'pipe',
                'item_name' => 'Pipa Galvanis Medium SNI',
                'material_type' => 'Galvanized Steel',
                'size' => '3/4" (DN20)',
                'connection_type' => 'Drat / Threaded',
                'unit' => 'Batang',
                'length_per_unit' => 6.00,
                'default_waste_percent' => 5.0,
                'notes' => 'Pipa baja galvanis medium SNI panjang 6 meter.',
                'vendors' => [
                    ['name' => 'PT. CITRA PIPING TEKNIK', 'price' => 195000, 'notes' => 'SNI Spindo Tebal 2.6mm', 'is_primary' => true],
                    ['name' => 'INDO STEEL - TOKOPEDIA', 'price' => 205000, 'notes' => 'Ready stok gudang Tangerang', 'is_primary' => false],
                    ['name' => 'PT. SURYA BAJA UTAMA', 'price' => 212000, 'notes' => 'SNI Grade A', 'is_primary' => false],
                ]
            ],
            [
                'category' => 'pipe',
                'item_name' => 'Pipa Galvanis Medium SNI',
                'material_type' => 'Galvanized Steel',
                'size' => '1" (DN25)',
                'connection_type' => 'Drat / Threaded',
                'unit' => 'Batang',
                'length_per_unit' => 6.00,
                'default_waste_percent' => 5.0,
                'notes' => 'Pipa galvanis medium SNI 1 inch panjang 6m.',
                'vendors' => [
                    ['name' => 'PT. CITRA PIPING TEKNIK', 'price' => 285000, 'notes' => 'Spindo Tebal 3.2mm', 'is_primary' => true],
                    ['name' => 'INDO STEEL - TOKOPEDIA', 'price' => 298000, 'notes' => 'Include sertifikat pabrik', 'is_primary' => false],
                    ['name' => 'PT. SURYA BAJA UTAMA', 'price' => 310000, 'notes' => 'Merk Bakrie', 'is_primary' => false],
                ]
            ],
            [
                'category' => 'pipe',
                'item_name' => 'Pipa Galvanis Medium SNI',
                'material_type' => 'Galvanized Steel',
                'size' => '1-1/2" (DN40)',
                'connection_type' => 'Drat / Threaded',
                'unit' => 'Batang',
                'length_per_unit' => 6.00,
                'default_waste_percent' => 5.0,
                'notes' => 'Pipa galvanis medium SNI 1.5 inch panjang 6m.',
                'vendors' => [
                    ['name' => 'PT. CITRA PIPING TEKNIK', 'price' => 450000, 'notes' => 'Spindo tebal 3.25mm', 'is_primary' => true],
                    ['name' => 'INDO STEEL - TOKOPEDIA', 'price' => 470000, 'notes' => 'Ready stok', 'is_primary' => false],
                    ['name' => 'PT. SURYA BAJA UTAMA', 'price' => 485000, 'notes' => 'Katalog 2026', 'is_primary' => false],
                ]
            ],
            [
                'category' => 'pipe',
                'item_name' => 'Pipa Galvanis Medium SNI',
                'material_type' => 'Galvanized Steel',
                'size' => '2" (DN50)',
                'connection_type' => 'Drat / Flange',
                'unit' => 'Batang',
                'length_per_unit' => 6.00,
                'default_waste_percent' => 5.0,
                'notes' => 'Pipa galvanis medium SNI 2 inch panjang 6 meter jalur distribusi kompresor.',
                'vendors' => [
                    ['name' => 'PT. CITRA PIPING TEKNIK', 'price' => 580000, 'notes' => 'Spindo Tebal 3.65mm', 'is_primary' => true],
                    ['name' => 'PT. SURYA BAJA UTAMA', 'price' => 605000, 'notes' => 'Ready stok Cikarang', 'is_primary' => false],
                    ['name' => 'INDO STEEL - TOKOPEDIA', 'price' => 620000, 'notes' => 'Free ongkir jabodetabek > 10 btg', 'is_primary' => false],
                ]
            ],
            [
                'category' => 'pipe',
                'item_name' => 'Pipa Galvanis Medium SNI',
                'material_type' => 'Galvanized Steel',
                'size' => '3" (DN80)',
                'connection_type' => 'Flange / Welded',
                'unit' => 'Batang',
                'length_per_unit' => 6.00,
                'default_waste_percent' => 5.0,
                'notes' => 'Pipa galvanis medium SNI 3 inch panjang 6 meter untuk jalur header kompresor.',
                'vendors' => [
                    ['name' => 'PT. CITRA PIPING TEKNIK', 'price' => 920000, 'notes' => 'Spindo Tebal 4.05mm', 'is_primary' => true],
                    ['name' => 'PT. SURYA BAJA UTAMA', 'price' => 960000, 'notes' => 'Stockist Cilegon', 'is_primary' => false],
                    ['name' => 'INDO STEEL - TOKOPEDIA', 'price' => 985000, 'notes' => 'Ready stok', 'is_primary' => false],
                ]
            ],
            [
                'category' => 'pipe',
                'item_name' => 'Pipa Galvanis Medium SNI',
                'material_type' => 'Galvanized Steel',
                'size' => '4" (DN100)',
                'connection_type' => 'Flange / Welded',
                'unit' => 'Batang',
                'length_per_unit' => 6.00,
                'default_waste_percent' => 5.0,
                'notes' => 'Pipa galvanis medium SNI 4 inch header utama kapasitas besar.',
                'vendors' => [
                    ['name' => 'PT. CITRA PIPING TEKNIK', 'price' => 1350000, 'notes' => 'Spindo Tebal 4.5mm', 'is_primary' => true],
                    ['name' => 'PT. SURYA BAJA UTAMA', 'price' => 1395000, 'notes' => 'Ready stok', 'is_primary' => false],
                    ['name' => 'INDO STEEL - TOKOPEDIA', 'price' => 1440000, 'notes' => 'Harga per batang 6m', 'is_primary' => false],
                ]
            ],

            // ==================== 2. PIPA ALUMINIUM AIRNET / AIRPIPE (4 METER & 6 METER) ====================
            [
                'category' => 'pipe',
                'item_name' => 'Pipa Aluminium Airnet / Airpipe Blue (Powder Coated)',
                'material_type' => 'Aluminium 6063-T6',
                'size' => '25mm (1")',
                'connection_type' => 'Quick-Fit Push-in / Clamp',
                'unit' => 'Batang',
                'length_per_unit' => 6.00,
                'default_waste_percent' => 3.0,
                'notes' => 'Pipa aluminium anti karat, zero leak, hambatan rendah (smooth inner bore) untuk udara bertekanan higienis.',
                'vendors' => [
                    ['name' => 'PT. AIRNET INDONESIA MAKMUR', 'price' => 420000, 'notes' => 'Original Airnet Atlas Copco Blue 6m', 'is_primary' => true],
                    ['name' => 'HYDRO INDOJAYA PERKASA', 'price' => 445000, 'notes' => 'Eq. Transair / Aircom', 'is_primary' => false],
                    ['name' => 'PT. MEGATAMA MULTITEKNIKA', 'price' => 460000, 'notes' => 'Include sertifikat ISO 8573', 'is_primary' => false],
                ]
            ],
            [
                'category' => 'pipe',
                'item_name' => 'Pipa Aluminium Airnet / Airpipe Blue (Powder Coated)',
                'material_type' => 'Aluminium 6063-T6',
                'size' => '40mm (1-1/2")',
                'connection_type' => 'Quick-Fit Push-in / Clamp',
                'unit' => 'Batang',
                'length_per_unit' => 6.00,
                'default_waste_percent' => 3.0,
                'notes' => 'Pipa aluminium airnet 40mm anti korosi 16 bar.',
                'vendors' => [
                    ['name' => 'PT. AIRNET INDONESIA MAKMUR', 'price' => 680000, 'notes' => 'Original Airnet 6m batang', 'is_primary' => true],
                    ['name' => 'HYDRO INDOJAYA PERKASA', 'price' => 715000, 'notes' => 'Garansi 10 tahun anti korosi', 'is_primary' => false],
                    ['name' => 'PT. MEGATAMA MULTITEKNIKA', 'price' => 740000, 'notes' => 'Ready stok Jakarta', 'is_primary' => false],
                ]
            ],
            [
                'category' => 'pipe',
                'item_name' => 'Pipa Aluminium Airnet / Airpipe Blue (Powder Coated)',
                'material_type' => 'Aluminium 6063-T6',
                'size' => '50mm (2")',
                'connection_type' => 'Quick-Fit Push-in / Clamp',
                'unit' => 'Batang',
                'length_per_unit' => 6.00,
                'default_waste_percent' => 3.0,
                'notes' => 'Pipa aluminium airnet 50mm untuk jalur distribusi utama kompresor 37-75 kW.',
                'vendors' => [
                    ['name' => 'PT. AIRNET INDONESIA MAKMUR', 'price' => 950000, 'notes' => 'Original Airnet 6m batang', 'is_primary' => true],
                    ['name' => 'HYDRO INDOJAYA PERKASA', 'price' => 990000, 'notes' => 'Tekanan maks 16 Bar', 'is_primary' => false],
                    ['name' => 'PT. MEGATAMA MULTITEKNIKA', 'price' => 1025000, 'notes' => 'Stok gudang Cikarang', 'is_primary' => false],
                ]
            ],
            [
                'category' => 'pipe',
                'item_name' => 'Pipa Aluminium Airnet / Airpipe Blue (Powder Coated)',
                'material_type' => 'Aluminium 6063-T6',
                'size' => '80mm (3")',
                'connection_type' => 'Quick-Fit Clamp',
                'unit' => 'Batang',
                'length_per_unit' => 6.00,
                'default_waste_percent' => 3.0,
                'notes' => 'Pipa aluminium airnet 80mm untuk header utama kompresor 100 kW+.',
                'vendors' => [
                    ['name' => 'PT. AIRNET INDONESIA MAKMUR', 'price' => 1650000, 'notes' => 'Airnet 80mm 6m', 'is_primary' => true],
                    ['name' => 'HYDRO INDOJAYA PERKASA', 'price' => 1720000, 'notes' => 'Termasuk end cap pelindung', 'is_primary' => false],
                    ['name' => 'PT. MEGATAMA MULTITEKNIKA', 'price' => 1780000, 'notes' => 'Ready stok import', 'is_primary' => false],
                ]
            ],

            // ==================== 3. PIPA STAINLESS STEEL 304 SCH 10 (6 METER) ====================
            [
                'category' => 'pipe',
                'item_name' => 'Pipa Stainless Steel 304 Seamless / Welded Sch 10',
                'material_type' => 'SS 304',
                'size' => '1" (DN25)',
                'connection_type' => 'Argon Welded / Ferrule',
                'unit' => 'Batang',
                'length_per_unit' => 6.00,
                'default_waste_percent' => 5.0,
                'notes' => 'Pipa SS304 food-grade / pharma / chemical industry panjang 6 meter.',
                'vendors' => [
                    ['name' => 'PT. SURYA BAJA UTAMA', 'price' => 495000, 'notes' => 'SS304 Sch10 Welded Mirror', 'is_primary' => true],
                    ['name' => 'PT. CITRA PIPING TEKNIK', 'price' => 525000, 'notes' => 'Ready stok Cilegon', 'is_primary' => false],
                    ['name' => 'INDO STEEL - TOKOPEDIA', 'price' => 550000, 'notes' => 'Harga include mill test certificate', 'is_primary' => false],
                ]
            ],
            [
                'category' => 'pipe',
                'item_name' => 'Pipa Stainless Steel 304 Seamless / Welded Sch 10',
                'material_type' => 'SS 304',
                'size' => '2" (DN50)',
                'connection_type' => 'Argon Welded / Flange',
                'unit' => 'Batang',
                'length_per_unit' => 6.00,
                'default_waste_percent' => 5.0,
                'notes' => 'Pipa SS304 Sch 10 2 inch panjang 6 meter.',
                'vendors' => [
                    ['name' => 'PT. SURYA BAJA UTAMA', 'price' => 980000, 'notes' => 'SS304 Sch10 OD 60.3mm', 'is_primary' => true],
                    ['name' => 'PT. CITRA PIPING TEKNIK', 'price' => 1030000, 'notes' => 'Standard ASTM A312', 'is_primary' => false],
                    ['name' => 'INDO STEEL - TOKOPEDIA', 'price' => 1080000, 'notes' => 'Grade A Foodgrade', 'is_primary' => false],
                ]
            ],

            // ==================== 4. FITTINGS & SAMBUNGAN ====================
            [
                'category' => 'fitting',
                'item_name' => 'Elbow 90° Galvanized Iron (GI)',
                'material_type' => 'Malleable Cast Iron',
                'size' => '1/2"',
                'connection_type' => 'Drat Dalam / Female Thread',
                'unit' => 'Pcs',
                'length_per_unit' => 1.00,
                'default_waste_percent' => 0.0,
                'notes' => 'Fitting sambungan belokan 90 derajat galvanis drat dalam.',
                'vendors' => [
                    ['name' => 'PT. CITRA PIPING TEKNIK', 'price' => 12500, 'notes' => 'Merk TSP / G Brand', 'is_primary' => true],
                    ['name' => 'INDO STEEL - TOKOPEDIA', 'price' => 14000, 'notes' => 'Heavy duty 150 PSI', 'is_primary' => false],
                    ['name' => 'PT. MEGATAMA MULTITEKNIKA', 'price' => 15500, 'notes' => 'Ready stok', 'is_primary' => false],
                ]
            ],
            [
                'category' => 'fitting',
                'item_name' => 'Elbow 90° Galvanized Iron (GI)',
                'material_type' => 'Malleable Cast Iron',
                'size' => '1"',
                'connection_type' => 'Drat Dalam / Female Thread',
                'unit' => 'Pcs',
                'length_per_unit' => 1.00,
                'default_waste_percent' => 0.0,
                'notes' => 'Fitting belokan 90 derajat galvanis 1 inch.',
                'vendors' => [
                    ['name' => 'PT. CITRA PIPING TEKNIK', 'price' => 24000, 'notes' => 'Merk TSP / G Brand', 'is_primary' => true],
                    ['name' => 'INDO STEEL - TOKOPEDIA', 'price' => 26500, 'notes' => 'Galvanis tebal', 'is_primary' => false],
                    ['name' => 'PT. MEGATAMA MULTITEKNIKA', 'price' => 28000, 'notes' => 'Stok banyak', 'is_primary' => false],
                ]
            ],
            [
                'category' => 'fitting',
                'item_name' => 'Elbow 90° Galvanized Iron (GI)',
                'material_type' => 'Malleable Cast Iron',
                'size' => '2"',
                'connection_type' => 'Drat Dalam / Female Thread',
                'unit' => 'Pcs',
                'length_per_unit' => 1.00,
                'default_waste_percent' => 0.0,
                'notes' => 'Fitting belokan 90 derajat galvanis 2 inch.',
                'vendors' => [
                    ['name' => 'PT. CITRA PIPING TEKNIK', 'price' => 58000, 'notes' => 'Merk TSP / G Brand', 'is_primary' => true],
                    ['name' => 'INDO STEEL - TOKOPEDIA', 'price' => 63000, 'notes' => 'Heavy duty', 'is_primary' => false],
                    ['name' => 'PT. MEGATAMA MULTITEKNIKA', 'price' => 67000, 'notes' => 'Ready stok', 'is_primary' => false],
                ]
            ],
            [
                'category' => 'fitting',
                'item_name' => 'Tee Equal Galvanized Iron (GI)',
                'material_type' => 'Malleable Cast Iron',
                'size' => '1"',
                'connection_type' => 'Drat Dalam / Female Thread',
                'unit' => 'Pcs',
                'length_per_unit' => 1.00,
                'default_waste_percent' => 0.0,
                'notes' => 'Sambungan cabang 3 equal galvanis 1 inch.',
                'vendors' => [
                    ['name' => 'PT. CITRA PIPING TEKNIK', 'price' => 32000, 'notes' => 'Merk TSP / G Brand', 'is_primary' => true],
                    ['name' => 'INDO STEEL - TOKOPEDIA', 'price' => 35000, 'notes' => 'Ready stok', 'is_primary' => false],
                    ['name' => 'PT. MEGATAMA MULTITEKNIKA', 'price' => 38000, 'notes' => 'SNI', 'is_primary' => false],
                ]
            ],
            [
                'category' => 'fitting',
                'item_name' => 'Tee Equal Galvanized Iron (GI)',
                'material_type' => 'Malleable Cast Iron',
                'size' => '2"',
                'connection_type' => 'Drat Dalam / Female Thread',
                'unit' => 'Pcs',
                'length_per_unit' => 1.00,
                'default_waste_percent' => 0.0,
                'notes' => 'Sambungan cabang 3 equal galvanis 2 inch.',
                'vendors' => [
                    ['name' => 'PT. CITRA PIPING TEKNIK', 'price' => 78000, 'notes' => 'Merk TSP / G Brand', 'is_primary' => true],
                    ['name' => 'INDO STEEL - TOKOPEDIA', 'price' => 84000, 'notes' => 'Heavy duty', 'is_primary' => false],
                    ['name' => 'PT. MEGATAMA MULTITEKNIKA', 'price' => 89000, 'notes' => 'Ready stok', 'is_primary' => false],
                ]
            ],
            [
                'category' => 'fitting',
                'item_name' => 'Water Mur / Union GI (Galvanis)',
                'material_type' => 'Malleable Cast Iron + Brass Seat',
                'size' => '1"',
                'connection_type' => 'Drat Dalam',
                'unit' => 'Pcs',
                'length_per_unit' => 1.00,
                'default_waste_percent' => 0.0,
                'notes' => 'Sambungan bongkar-pasang pipa (water mur) dudukan kuningan anti bocor.',
                'vendors' => [
                    ['name' => 'PT. CITRA PIPING TEKNIK', 'price' => 45000, 'notes' => 'Brass Seat 150 PSI', 'is_primary' => true],
                    ['name' => 'INDO STEEL - TOKOPEDIA', 'price' => 49000, 'notes' => 'Merk TSP', 'is_primary' => false],
                    ['name' => 'PT. MEGATAMA MULTITEKNIKA', 'price' => 52000, 'notes' => 'Ready stok', 'is_primary' => false],
                ]
            ],
            [
                'category' => 'fitting',
                'item_name' => 'Airnet Quick Drop Bracket / Saddle Clamp',
                'material_type' => 'Aluminium + NBR Seal',
                'size' => '50mm x 25mm',
                'connection_type' => 'Clamp-On Quick Drop',
                'unit' => 'Pcs',
                'length_per_unit' => 1.00,
                'default_waste_percent' => 0.0,
                'notes' => 'Bracket drop point airnet anti kondensasi air, pasang tanpa potong pipa utama.',
                'vendors' => [
                    ['name' => 'PT. AIRNET INDONESIA MAKMUR', 'price' => 285000, 'notes' => 'Original Airnet Quick Drop', 'is_primary' => true],
                    ['name' => 'HYDRO INDOJAYA PERKASA', 'price' => 310000, 'notes' => 'Eq. Transair Quick Assembly', 'is_primary' => false],
                    ['name' => 'PT. MEGATAMA MULTITEKNIKA', 'price' => 325000, 'notes' => 'Include O-ring EPDM', 'is_primary' => false],
                ]
            ],

            // ==================== 5. VALVES (KRAN / KATUP) ====================
            [
                'category' => 'valve',
                'item_name' => 'Ball Valve Kuningan (Brass) Full Bore 600 WOG',
                'material_type' => 'Forged Brass Nickel Plated',
                'size' => '1/2" (DN15)',
                'connection_type' => 'Drat Dalam (BSPT/NPT)',
                'unit' => 'Pcs',
                'length_per_unit' => 1.00,
                'default_waste_percent' => 0.0,
                'notes' => 'Katup bola kuningan tebal untuk buka-tutup jalur drop point mesin/alat.',
                'vendors' => [
                    ['name' => 'HYDRO INDOJAYA PERKASA', 'price' => 65000, 'notes' => 'Merk Kitz / Onda Heavy Duty', 'is_primary' => true],
                    ['name' => 'PT. MEGATAMA MULTITEKNIKA', 'price' => 72000, 'notes' => 'Kitz Original Japan / Thailand', 'is_primary' => false],
                    ['name' => 'INDO STEEL - TOKOPEDIA', 'price' => 78000, 'notes' => 'Tekanan maks 28 bar', 'is_primary' => false],
                ]
            ],
            [
                'category' => 'valve',
                'item_name' => 'Ball Valve Kuningan (Brass) Full Bore 600 WOG',
                'material_type' => 'Forged Brass Nickel Plated',
                'size' => '1" (DN25)',
                'connection_type' => 'Drat Dalam (BSPT/NPT)',
                'unit' => 'Pcs',
                'length_per_unit' => 1.00,
                'default_waste_percent' => 0.0,
                'notes' => 'Ball valve kuningan 1 inch heavy duty.',
                'vendors' => [
                    ['name' => 'HYDRO INDOJAYA PERKASA', 'price' => 145000, 'notes' => 'Merk Kitz Brass 600 WOG', 'is_primary' => true],
                    ['name' => 'PT. MEGATAMA MULTITEKNIKA', 'price' => 158000, 'notes' => 'Kitz Type S', 'is_primary' => false],
                    ['name' => 'INDO STEEL - TOKOPEDIA', 'price' => 165000, 'notes' => 'Ready stok', 'is_primary' => false],
                ]
            ],
            [
                'category' => 'valve',
                'item_name' => 'Ball Valve Stainless Steel 304 (3-Piece / 2-Piece)',
                'material_type' => 'SS 304 CF8',
                'size' => '2" (DN50)',
                'connection_type' => 'Drat Dalam 1000 WOG',
                'unit' => 'Pcs',
                'length_per_unit' => 1.00,
                'default_waste_percent' => 0.0,
                'notes' => 'Katup bola SS304 tahan tekanan tinggi 1000 WOG dan anti karat.',
                'vendors' => [
                    ['name' => 'PT. MEGATAMA MULTITEKNIKA', 'price' => 485000, 'notes' => 'SS304 2-Piece 1000 WOG PTFE Seal', 'is_primary' => true],
                    ['name' => 'PT. SURYA BAJA UTAMA', 'price' => 520000, 'notes' => 'Merk Arita / Kitz SS', 'is_primary' => false],
                    ['name' => 'HYDRO INDOJAYA PERKASA', 'price' => 545000, 'notes' => 'Ready stok gudang', 'is_primary' => false],
                ]
            ],
            [
                'category' => 'valve',
                'item_name' => 'Butterfly Valve Wafer Type Lever Handle (JIS 10K / PN16)',
                'material_type' => 'Cast Iron Body + SS304 Disc + EPDM Seat',
                'size' => '3" (DN80)',
                'connection_type' => 'Wafer Flange Mount',
                'unit' => 'Pcs',
                'length_per_unit' => 1.00,
                'default_waste_percent' => 0.0,
                'notes' => 'Katup butterfly wafer type untuk isolasi header utama kompresor / tangki udara.',
                'vendors' => [
                    ['name' => 'PT. MEGATAMA MULTITEKNIKA', 'price' => 620000, 'notes' => 'Merk Tomoe / Toyo Eq. Disc SS304', 'is_primary' => true],
                    ['name' => 'HYDRO INDOJAYA PERKASA', 'price' => 665000, 'notes' => 'JIS 10K / PN16 Dual Rating', 'is_primary' => false],
                    ['name' => 'PT. CITRA PIPING TEKNIK', 'price' => 695000, 'notes' => 'Ready stok', 'is_primary' => false],
                ]
            ],

            // ==================== 6. SUPPORT & BRACKET HANGER ====================
            [
                'category' => 'support',
                'item_name' => 'Unistrut / C-Channel Galvanized (Slotted)',
                'material_type' => 'Hot Dip Galvanized Steel',
                'size' => '41 x 41 x 2.0 mm (Panjang 3 Meter)',
                'connection_type' => 'Bolt-On',
                'unit' => 'Batang',
                'length_per_unit' => 3.00,
                'default_waste_percent' => 5.0,
                'notes' => 'Kanal C berlubang galvanis untuk rangka gantungan pipa di atap / dinding workshop.',
                'vendors' => [
                    ['name' => 'PT. CITRA PIPING TEKNIK', 'price' => 145000, 'notes' => 'HDG Tebal 2.0mm 3 meter', 'is_primary' => true],
                    ['name' => 'INDO STEEL - TOKOPEDIA', 'price' => 155000, 'notes' => 'Merk Three Star / B-Line Eq.', 'is_primary' => false],
                    ['name' => 'PT. MEGATAMA MULTITEKNIKA', 'price' => 165000, 'notes' => 'Ready stok', 'is_primary' => false],
                ]
            ],
            [
                'category' => 'support',
                'item_name' => 'Threaded Rod / Long Drat Galvanis M10 + Nut & Washer',
                'material_type' => 'Electro Galvanized Mild Steel',
                'size' => 'M10 x 2 Meter',
                'connection_type' => 'Threaded Rod Hanger',
                'unit' => 'Batang',
                'length_per_unit' => 2.00,
                'default_waste_percent' => 5.0,
                'notes' => 'Besi as drat gantungan plafon / rangka atap workshop.',
                'vendors' => [
                    ['name' => 'INDO STEEL - TOKOPEDIA', 'price' => 28000, 'notes' => 'Grade 4.8 Galvanized 2m', 'is_primary' => true],
                    ['name' => 'PT. CITRA PIPING TEKNIK', 'price' => 31000, 'notes' => 'Include 2 mur + 2 ring', 'is_primary' => false],
                    ['name' => 'PT. MEGATAMA MULTITEKNIKA', 'price' => 34000, 'notes' => 'Ready stok ribuan batang', 'is_primary' => false],
                ]
            ],
            [
                'category' => 'support',
                'item_name' => 'U-Bolt Clamp Galvanis + 2 Nuts (Untuk Pipa 2")',
                'material_type' => 'Galvanized Steel',
                'size' => '2" (DN50)',
                'connection_type' => 'Clamp U-Bolt',
                'unit' => 'Set',
                'length_per_unit' => 1.00,
                'default_waste_percent' => 0.0,
                'notes' => 'Klem U-Bolt pipa galvanis/baja lengkap 2 mur pengunci.',
                'vendors' => [
                    ['name' => 'INDO STEEL - TOKOPEDIA', 'price' => 12500, 'notes' => 'Drat M8 galvanis putih', 'is_primary' => true],
                    ['name' => 'PT. CITRA PIPING TEKNIK', 'price' => 14000, 'notes' => 'Baut tebal', 'is_primary' => false],
                    ['name' => 'PT. MEGATAMA MULTITEKNIKA', 'price' => 16000, 'notes' => 'Heavy duty', 'is_primary' => false],
                ]
            ],

            // ==================== 7. CONSUMABLE & SEALING ====================
            [
                'category' => 'consumable',
                'item_name' => 'Sealtape Heavy Duty High Density (Teflon Tape)',
                'material_type' => 'PTFE 100%',
                'size' => '19mm x 15 Meter (Tebal 0.1mm)',
                'connection_type' => 'Thread Sealing',
                'unit' => 'Roll',
                'length_per_unit' => 1.00,
                'default_waste_percent' => 0.0,
                'notes' => 'Sealtape tebal anti sobek untuk kerapatan drat pipa kompresor udara hingga 25 bar.',
                'vendors' => [
                    ['name' => 'HYDRO INDOJAYA PERKASA', 'price' => 11000, 'notes' => 'Merk Unipak / Tombo Tebal', 'is_primary' => true],
                    ['name' => 'INDO STEEL - TOKOPEDIA', 'price' => 12500, 'notes' => 'Density tinggi anti bocor', 'is_primary' => false],
                    ['name' => 'PT. MEGATAMA MULTITEKNIKA', 'price' => 14000, 'notes' => 'Original Tombo 9082', 'is_primary' => false],
                ]
            ],
            [
                'category' => 'consumable',
                'item_name' => 'Flange Gasket EPDM / Klingerit 1000 (JIS 10K / PN16)',
                'material_type' => 'Non-Asbestos / EPDM Rubber',
                'size' => '3" (DN80)',
                'connection_type' => 'Flange Gasket Ring Type',
                'unit' => 'Pcs',
                'length_per_unit' => 1.00,
                'default_waste_percent' => 0.0,
                'notes' => 'Packing paking flange sambungan pipa anti rembes udara bertekanan.',
                'vendors' => [
                    ['name' => 'PT. MEGATAMA MULTITEKNIKA', 'price' => 28000, 'notes' => 'Klingerit 1000 Tebal 3mm', 'is_primary' => true],
                    ['name' => 'HYDRO INDOJAYA PERKASA', 'price' => 32000, 'notes' => 'EPDM Sheet High Elastic', 'is_primary' => false],
                    ['name' => 'PT. CITRA PIPING TEKNIK', 'price' => 35000, 'notes' => 'JIS 10K 3 inch', 'is_primary' => false],
                ]
            ],
        ];

        // 3. Insert / Update Records
        DB::beginTransaction();
        try {
            foreach ($materialsData as $mData) {
                $vendors = $mData['vendors'];
                unset($mData['vendors']);

                // Find or create material
                $material = PipingMaterial::updateOrCreate(
                    [
                        'item_name' => $mData['item_name'],
                        'size'      => $mData['size'],
                    ],
                    $mData
                );

                // Insert / Update Multi-Vendor Prices
                foreach ($vendors as $vData) {
                    $supId = $supplierIds[$vData['name']] ?? ($existingSuppliers[0] ?? 4);

                    PipingMaterialVendorPrice::updateOrCreate(
                        [
                            'id_piping_material' => $material->id,
                            'id_supplier'        => $supId,
                        ],
                        [
                            'price_idr'   => $vData['price'],
                            'price_usd'   => null,
                            'kurs_usd'    => 16000,
                            'date'        => Carbon::now()->subDays(rand(1, 20))->format('Y-m-d'),
                            'notes'       => $vData['notes'] ?? null,
                            'is_primary'  => $vData['is_primary'] ?? false,
                        ]
                    );
                }
            }

            DB::commit();
            $this->command->info("Successfully seeded " . count($materialsData) . " piping materials with multi-vendor prices!");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("Seeder failed: " . $e->getMessage());
            throw $e;
        }
    }
}
