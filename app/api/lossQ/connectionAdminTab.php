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
    SELECT q.id, q.no_quote, c.company, c.ru, q.harga_total, q.estimated_date,
           q.status, q.note, u.name AS sales_name, u.image AS sales_image,
           COALESCE(
               NULLIF(TRIM(q.title), ''),
               (
                   SELECT GROUP_CONCAT(DISTINCT COALESCE(NULLIF(TRIM(ds.detail), ''), NULLIF(TRIM(s.subtitle), '')) SEPARATOR ' | ')
                   FROM subtitle_quotation s
                   LEFT JOIN detail_service_quotation ds ON ds.id_subtitle = s.id
                   WHERE s.id_quotation = q.id
               )
           ) AS description
    FROM quotation q
    LEFT JOIN pic p ON p.id = q.id_pic
    LEFT JOIN client c ON c.id = p.id_client
    INNER JOIN users u ON u.id = q.id_sales
    WHERE q.status = '0' AND q.level = '1' $salesFilter $yearFilter
    GROUP BY q.id
    ORDER BY q.expired_date ASC";

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
