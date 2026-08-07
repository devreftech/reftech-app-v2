<?php
use Illuminate\Support\Facades\Auth;

header('Content-Type: application/json');
$host         = env('DB_HOST', '127.0.0.1');
$users        = env('DB_USERNAME', 'root');
$pass         = env('DB_PASSWORD', '');
$databaseName = env('DB_DATABASE', 'u877155683_reftech_my');

if (Auth::check()) {
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$databaseName;charset=utf8", $users, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "SELECT
                    s.id AS serial_id,
                    p.id AS product_id,
                    CONCAT(p.commodity, ' (',
                        CASE
                            WHEN p.go = 'Replacement' THEN 'R'
                            WHEN p.go = 'Genuine' THEN 'G'
                            ELSE p.go
                        END,
                    ')') AS sku,
                    p.description,
                    s.brand,
                    s.pn,
                    s.price AS selling_price,
                    COUNT(DISTINCT spvp.id_supplier) AS vendor_count,
                    MIN(spvp.price_idr) AS min_price_idr,
                    (SELECT sup2.supplier FROM spare_part_vendor_prices spvp2
                        JOIN supplier sup2 ON sup2.id = spvp2.id_supplier
                        WHERE spvp2.id_serial_product = s.id
                        ORDER BY spvp2.price_idr ASC LIMIT 1) AS cheapest_vendor,
                    MAX(spvp.date) AS last_inquiry
                FROM serial_product s
                INNER JOIN product p ON p.id = s.id_product
                INNER JOIN spare_part_vendor_prices spvp ON spvp.id_serial_product = s.id
                LEFT JOIN supplier sup ON sup.id = spvp.id_supplier
                GROUP BY s.id, p.id
                ORDER BY last_inquiry DESC";

        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['data' => $result], JSON_PRETTY_PRINT);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Kesalahan Database: ' . $e->getMessage()], JSON_PRETTY_PRINT);
    } finally {
        $pdo = null;
    }
} else {
    echo json_encode(['error' => 'Pengguna tidak terotentikasi'], JSON_PRETTY_PRINT);
}
