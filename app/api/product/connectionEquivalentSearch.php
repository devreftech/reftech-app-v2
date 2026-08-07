<?php
use Illuminate\Support\Facades\Auth;

header('Content-Type: application/json');

if (!Auth::check()) {
    echo json_encode(['error' => 'Pengguna tidak terotentikasi']);
    exit;
}

$host = env('DB_HOST', '127.0.0.1');
$user = env('DB_USERNAME', 'root');
$pass = env('DB_PASSWORD', '');
$db   = env('DB_DATABASE', 'u877155683_reftech_my');

$q = trim(request()->get('q', ''));

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $like = '%' . $q . '%';

    $sql = "SELECT 
                COALESCE(sp.id, p.id) AS id_equivalent,
                sp.pn,
                sp.brand,
                COALESCE(sp.price, 0) AS price,
                sp.fxp_parts,
                p.commodity AS product_name,
                p.description AS product_desc,
                p.go AS genuine_status,
                p.unit AS product_unit,
                p.warehouse_stock,
                p.stock,
                p.pending_stock,
                dp.replacement
            FROM product p
            LEFT JOIN serial_product sp ON p.id = sp.id_product
            LEFT JOIN detail_product dp ON p.id = dp.id_product
            WHERE (
                sp.pn LIKE :q1
                OR sp.brand LIKE :q2
                OR sp.fxp_parts LIKE :q3
                OR p.commodity LIKE :q4
                OR p.description LIKE :q6
                OR dp.replacement LIKE :q7
            )
            LIMIT 30";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':q1', $like);
    $stmt->bindValue(':q2', $like);
    $stmt->bindValue(':q3', $like);
    $stmt->bindValue(':q4', $like);
    $stmt->bindValue(':q6', $like);
    $stmt->bindValue(':q7', $like);
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($results, JSON_PRETTY_PRINT);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    $pdo = null;
}
?>
