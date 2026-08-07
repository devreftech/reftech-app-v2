<?php
use Illuminate\Support\Facades\Auth;

header('Content-Type: application/json');

if (!Auth::check()) {
    echo json_encode(['error' => 'Pengguna tidak terotentikasi']);
    exit;
}

$host = env('DB_HOST', '127.0.0.1');
$users = env('DB_USERNAME', 'root');
$pass = env('DB_PASSWORD', '');
$databaseName = env('DB_DATABASE', 'u877155683_reftech_my');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$databaseName;charset=utf8", $users, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $salesId = request()->get('sales_id');
    $salesFilter = $salesId ? "AND u.id = " . intval($salesId) : "";

    $year = request()->get('year');
    $yearFilter = ($year && $year !== 'all') ? "AND YEAR(q.estimated_date) = " . intval($year) : "";

    $query = "
    SELECT q.id, q.no_quote, c.company, c.ru, q.subtotal, q.title, q.estimated_date,
           q.status, CONCAT(q.note, ' (', q.status_date, ')') AS tip, q.type,
           'service' AS row_type, u.name AS sales_name, u.image AS sales_image
    FROM quotation q
    LEFT JOIN pic p ON p.id = q.id_pic
    LEFT JOIN client c ON c.id = p.id_client
    INNER JOIN users u ON u.id = q.id_sales
    WHERE q.status IN (20,30,40,60,80) AND q.level = '1' AND q.is_primary = '1' AND q.type != 'Unit' $salesFilter $yearFilter
    GROUP BY q.primary_id
    ORDER BY q.estimated_date ASC";

    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['data' => $result], JSON_PRETTY_PRINT);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Kesalahan Database: ' . $e->getMessage()], JSON_PRETTY_PRINT);
} finally {
    $pdo = null;
}
?>
