<?php
use Illuminate\Support\Facades\Auth;

header('Content-Type: application/json');
$host         = env('DB_HOST', '127.0.0.1');
$users        = env('DB_USERNAME', 'root');
$pass         = env('DB_PASSWORD', '');
$databaseName = env('DB_DATABASE', 'u877155683_reftech_my');

if (Auth::check()) {
    $user   = Auth::user();
    $userId = $user->id;

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$databaseName;charset=utf8", $users, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $year = request()->get('year');
        $yearFilterQ = ($year && $year !== 'all') ? " AND YEAR(q.estimated_date) = " . intval($year) : "";
        $yearFilterU = ($year && $year !== 'all') ? " AND YEAR(uq.date) = " . intval($year) : "";

        $query = "
        SELECT q.id, q.no_quote, c.company, c.ru, q.subtotal, q.title, q.estimated_date,
               q.status, CONCAT(q.note, ' (', q.status_date, ')') AS tip, q.type, 'service' AS row_type
        FROM quotation q
        LEFT JOIN pic p ON p.id = q.id_pic
        LEFT JOIN client c ON c.id = p.id_client
        INNER JOIN users u ON u.id = q.id_sales
        WHERE u.id = $userId AND q.status = 80 AND q.level = '1' AND q.is_primary = '1' AND q.type != 'Unit'$yearFilterQ
        GROUP BY q.primary_id

        UNION ALL

        SELECT uq.id, uq.no_quote,
               COALESCE(NULLIF(c2.company,''),'-') AS company,
               NULL AS ru,
               uq.subtotal,
               COALESCE(NULLIF(uq.title,''),'-') AS title,
               uq.date AS estimated_date,
               uq.status,
               (SELECT CONCAT(DATE_FORMAT(sh.created_at,'%d-%m-%y'),' | ',COALESCE(NULLIF(sh.note,''),'Belum di update'))
                FROM unit_quotation_status_history sh
                WHERE sh.id_unit_quotation = uq.id
                ORDER BY sh.created_at DESC LIMIT 1) AS tip,
               uq.type,
               'unit' AS row_type
        FROM unit_quotation uq
        LEFT JOIN client c2 ON c2.id = NULLIF(uq.id_client,'')
        WHERE uq.id_sales = $userId AND uq.status = 'hot_prospect' AND uq.is_latest = 1$yearFilterU

        ORDER BY estimated_date ASC";

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
?>
