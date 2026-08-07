<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Models\DetailProduct;
use App\Models\SerialProduct;
use Illuminate\Support\Facades\DB;

// Test index logic
$start = microtime(true);
$commodity = Product::count();
$dproduct = DetailProduct::count();
$sproduct = SerialProduct::count();
$replace = DetailProduct::all();
$asset = $replace->sum(function ($replacement) {
    return $replacement->modal * $replacement->stock;
});
$equiv = SerialProduct::join('product', 'product.id', '=', 'serial_product.id_product')->groupBy('product.id')->get();
$revenue = $equiv->sum(function ($equivalent) {
    return $equivalent->price * $equivalent->stock;
});
$end = microtime(true);
echo "Eloquent Index logic took: " . ($end - $start) . " seconds" . PHP_EOL;

// Test optimized index logic
$start = microtime(true);
$commodity = Product::count();
$dproduct = DetailProduct::count();
$sproduct = SerialProduct::count();
$assetOpt = DetailProduct::sum(DB::raw('modal * stock'));
$revenueOpt = DB::table(DB::raw('(SELECT p.stock * s.price AS val FROM serial_product s JOIN product p ON p.id = s.id_product GROUP BY p.id) as sub'))
    ->sum('val');
$end = microtime(true);
echo "Optimized Index logic took: " . ($end - $start) . " seconds" . PHP_EOL;
echo "Asset original: $asset vs optimized: $assetOpt" . PHP_EOL;
echo "Revenue original: $revenue vs optimized: $revenueOpt" . PHP_EOL;

// Test connection.php query
$host = env('DB_HOST', '127.0.0.1');
$users = env('DB_USERNAME', 'root');
$pass = env('DB_PASSWORD', '');
$databaseName = env('DB_DATABASE');

$start = microtime(true);
$pdo = new PDO("mysql:host=$host;dbname=$databaseName;charset=utf8", $users, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$query = "SELECT 
            p.*,
            CONCAT(' (', 
                CASE 
                    WHEN p.go = 'Replacement' THEN 'R'
                    WHEN p.go = 'Genuine' THEN 'G'
                    ELSE p.go
                END
            , ')', p.description) as descrip,
            s.pn,
            s.brand,
            s.price,
            s.id AS id_pn,
            p.id AS id_p, 
            p.stock AS all_stock, 
            IFNULL(
                    CONCAT(
                        'Average HPP ', 
                        'Rp ', 
                        FORMAT(dp.modal, 2)
                    ),
                'Tidak Ada Replacement'
            ) AS modal_replacements,
            CONCAT(p.stock, ' - ', p.warehouse_stock) AS stok,
            (
                SELECT dpi.modal
                FROM detail_product_in dpi
                WHERE dpi.id_detail_product = dp.id
                ORDER BY dpi.id DESC
                LIMIT 1
            ) AS last_modal
        FROM 
            product p
        LEFT JOIN 
            serial_product s ON p.id = s.id_product
        LEFT JOIN 
            detail_product dp ON p.id = dp.id_product
        LEFT JOIN 
            detail_product_in dpi ON dp.id = dpi.id_detail_product
        GROUP BY 
            p.id, s.pn";

$stmt = $pdo->prepare($query);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
$end = microtime(true);
echo "Original connection.php query took: " . ($end - $start) . " seconds. Row count: " . count($result) . PHP_EOL;

// Test optimized connection.php query (without useless LEFT JOIN detail_product_in)
$start = microtime(true);
$queryOpt = "SELECT 
            p.*,
            CONCAT(' (', 
                CASE 
                    WHEN p.go = 'Replacement' THEN 'R'
                    WHEN p.go = 'Genuine' THEN 'G'
                    ELSE p.go
                END
            , ')', p.description) as descrip,
            s.pn,
            s.brand,
            s.price,
            s.id AS id_pn,
            p.id AS id_p, 
            p.stock AS all_stock, 
            IFNULL(
                    CONCAT(
                        'Average HPP ', 
                        'Rp ', 
                        FORMAT(dp.modal, 2)
                    ),
                'Tidak Ada Replacement'
            ) AS modal_replacements,
            CONCAT(p.stock, ' - ', p.warehouse_stock) AS stok,
            (
                SELECT dpi.modal
                FROM detail_product_in dpi
                WHERE dpi.id_detail_product = dp.id
                ORDER BY dpi.id DESC
                LIMIT 1
            ) AS last_modal
        FROM 
            product p
        LEFT JOIN 
            serial_product s ON p.id = s.id_product
        LEFT JOIN 
            detail_product dp ON p.id = dp.id_product
        GROUP BY 
            p.id, s.pn";

$stmt = $pdo->prepare($queryOpt);
$stmt->execute();
$resultOpt = $stmt->fetchAll(PDO::FETCH_ASSOC);
$end = microtime(true);
echo "Optimized connection.php query took: " . ($end - $start) . " seconds. Row count: " . count($resultOpt) . PHP_EOL;

// Test connectionSales.php query
$start = microtime(true);
$querySales = "SELECT p.*,
            CONCAT(' (', 
                CASE 
                    WHEN p.go = 'Replacement' THEN 'R'
                    WHEN p.go = 'Genuine' THEN 'G'
                    ELSE p.go
                END
            , ')', p.description) as descrip, s.image, s.brand, s.pn, s.price, CONCAT(p.stock, ' - ', p.warehouse_stock ) AS stok FROM product p
RIGHT JOIN serial_product s on p.id = s.id_product";
$stmt = $pdo->prepare($querySales);
$stmt->execute();
$resultSales = $stmt->fetchAll(PDO::FETCH_ASSOC);
$end = microtime(true);
echo "connectionSales.php query took: " . ($end - $start) . " seconds. Row count: " . count($resultSales) . PHP_EOL;
