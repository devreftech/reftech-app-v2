<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Adding indexes..." . PHP_EOL;

try {
    DB::statement("ALTER TABLE `serial_product` ADD INDEX `idx_serial_product_id_product` (`id_product`)");
    echo "Added index on serial_product.id_product" . PHP_EOL;
} catch (\Exception $e) {
    echo "serial_product.id_product index failed: " . $e->getMessage() . PHP_EOL;
}

try {
    DB::statement("ALTER TABLE `detail_product` ADD INDEX `idx_detail_product_id_product` (`id_product`)");
    echo "Added index on detail_product.id_product" . PHP_EOL;
} catch (\Exception $e) {
    echo "detail_product.id_product index failed: " . $e->getMessage() . PHP_EOL;
}

try {
    DB::statement("ALTER TABLE `detail_product_in` ADD INDEX `idx_detail_product_in_id_detail_product` (`id_detail_product`)");
    echo "Added index on detail_product_in.id_detail_product" . PHP_EOL;
} catch (\Exception $e) {
    echo "detail_product_in.id_detail_product index failed: " . $e->getMessage() . PHP_EOL;
}

// Now let's benchmark the queries with indexes!
$host = env('DB_HOST', '127.0.0.1');
$users = env('DB_USERNAME', 'root');
$pass = env('DB_PASSWORD', '');
$databaseName = env('DB_DATABASE');
$pdo = new PDO("mysql:host=$host;dbname=$databaseName;charset=utf8", $users, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Query 1: Original connection.php query
$start = microtime(true);
$query1 = "SELECT 
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

$stmt = $pdo->prepare($query1);
$stmt->execute();
$res1 = $stmt->fetchAll(PDO::FETCH_ASSOC);
$time1 = microtime(true) - $start;
echo "WITH INDEXES - Original connection.php query took: {$time1} seconds. Rows: " . count($res1) . PHP_EOL;

// Query 2: Optimized connection.php query (without outer dpi join)
$start = microtime(true);
$query2 = "SELECT 
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

$stmt = $pdo->prepare($query2);
$stmt->execute();
$res2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
$time2 = microtime(true) - $start;
echo "WITH INDEXES - Optimized query took: {$time2} seconds. Rows: " . count($res2) . PHP_EOL;

// Query 3: connectionSales.php query
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
$resSales = $stmt->fetchAll(PDO::FETCH_ASSOC);
$timeSales = microtime(true) - $start;
echo "WITH INDEXES - connectionSales.php query took: {$timeSales} seconds. Rows: " . count($resSales) . PHP_EOL;
