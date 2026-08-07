<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Let's count how many products have multiple detail_products
$multiDetailCount = DB::table('detail_product')
    ->select('id_product', DB::raw('count(*) as c'))
    ->groupBy('id_product')
    ->having('c', '>', 1)
    ->count();

echo "Number of products with multiple detail_products: " . $multiDetailCount . PHP_EOL;

// Let's print some examples of detail_products per product
$examples = DB::table('detail_product')
    ->select('id_product', DB::raw('count(*) as c'))
    ->groupBy('id_product')
    ->having('c', '>', 1)
    ->limit(5)
    ->get();

foreach ($examples as $ex) {
    echo "Product ID: {$ex->id_product} has {$ex->c} detail_products" . PHP_EOL;
    $details = DB::table('detail_product')->where('id_product', $ex->id_product)->get();
    foreach ($details as $d) {
        echo "  - Detail ID: {$d->id}, modal: {$d->modal}, stock: {$d->stock}" . PHP_EOL;
    }
}

// Let's run a benchmark of the optimized query vs the original query
$host = env('DB_HOST', '127.0.0.1');
$users = env('DB_USERNAME', 'root');
$pass = env('DB_PASSWORD', '');
$databaseName = env('DB_DATABASE');
$pdo = new PDO("mysql:host=$host;dbname=$databaseName;charset=utf8", $users, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Run query without LEFT JOIN detail_product_in dpi
$start = microtime(true);
$queryOpt1 = "SELECT 
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

$stmt = $pdo->prepare($queryOpt1);
$stmt->execute();
$res1 = $stmt->fetchAll(PDO::FETCH_ASSOC);
$time1 = microtime(true) - $start;
echo "Query without outer dpi join took: {$time1} seconds. Rows: " . count($res1) . PHP_EOL;

// Let's see if we can do an even faster query by avoiding GROUP BY over the join of s and dp.
// We can join with a pre-aggregated subquery of detail_product.
$start = microtime(true);
$queryOpt2 = "SELECT 
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
            dp.last_modal
        FROM 
            product p
        LEFT JOIN 
            serial_product s ON p.id = s.id_product
        LEFT JOIN (
            -- Pre-aggregate or get latest detail_product and its last_modal per product
            SELECT 
                dp1.id_product,
                dp1.modal,
                (
                    SELECT dpi.modal
                    FROM detail_product_in dpi
                    WHERE dpi.id_detail_product = dp1.id
                    ORDER BY dpi.id DESC
                    LIMIT 1
                ) AS last_modal
            FROM detail_product dp1
            -- Join only the latest/first detail product per product to avoid multiplication
            INNER JOIN (
                SELECT MIN(id) as id
                FROM detail_product
                GROUP BY id_product
            ) dp2 ON dp1.id = dp2.id
        ) dp ON p.id = dp.id_product
        -- group by is still needed if we want unique s.pn per product
        GROUP BY 
            p.id, s.pn";

$stmt = $pdo->prepare($queryOpt2);
$stmt->execute();
$res2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
$time2 = microtime(true) - $start;
echo "Query with subquery dp took: {$time2} seconds. Rows: " . count($res2) . PHP_EOL;

