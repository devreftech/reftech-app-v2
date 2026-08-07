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
    $salesFilter2 = $salesId ? "AND u2.id = " . intval($salesId) : "";

    $year = request()->get('year');
    $yearFilter = ($year && $year !== 'all') ? "AND YEAR(q.po_date) = " . intval($year) : "";
    $yearFilterU = ($year && $year !== 'all') ? " AND YEAR(sh2.created_at) = " . intval($year) : "";

    $query = "
    SELECT q.id, q.no_quote, c.company, c.ru, q.nett, q.title, q.po_date,
           inv.id AS invoice_id, inv.no_po, inv.no_invoice,
           'quote' AS row_type, q.type, u.name AS sales_name, u.image AS sales_image
    FROM quotation q
    LEFT JOIN pic p ON p.id = q.id_pic
    LEFT JOIN client c ON c.id = p.id_client
    INNER JOIN users u ON u.id = q.id_sales
    LEFT JOIN invoice inv ON inv.id_quotation = q.id
    WHERE q.status = '100' AND q.level = '1' AND q.is_primary = '1' $salesFilter $yearFilter
    GROUP BY q.id

    UNION ALL

    SELECT uq.id, uq.no_quote,
           COALESCE(NULLIF(cl.company,''),'-') AS company,
           NULL AS ru,
           (uq.total - uq.tax_amount) AS nett,
           COALESCE(NULLIF(uq.title,''),'-') AS title,
           (SELECT DATE(sh.created_at)
            FROM unit_quotation_status_history sh
            WHERE sh.id_unit_quotation = uq.id AND sh.status = 'po_received'
            ORDER BY sh.created_at DESC LIMIT 1) AS po_date,
           NULL AS invoice_id,
           uq.po_number AS no_po,
           NULL AS no_invoice,
           'unit' AS row_type, NULL AS type,
           u2.name AS sales_name, u2.image AS sales_image
    FROM unit_quotation uq
    LEFT JOIN client cl ON cl.id = NULLIF(uq.id_client,'')
    INNER JOIN users u2 ON u2.id = uq.id_sales
    WHERE uq.status = 'po_received' AND uq.is_latest = 1 $salesFilter2
    AND EXISTS (
        SELECT 1 FROM unit_quotation_status_history sh2
        WHERE sh2.id_unit_quotation = uq.id AND sh2.status = 'po_received'$yearFilterU
    )

    ORDER BY po_date DESC";

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
