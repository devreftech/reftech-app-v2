<?php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

header('Content-Type: application/json');

if (Auth::check()) {
    $user   = Auth::user();
    $userId = $user->id;

    try {
        $pdo = DB::connection()->getPdo();
        $pdo->exec("SET SESSION sql_mode = ''");

        $year = request()->get('year');
        $yearFilterQ = ($year && $year !== 'all') ? " AND YEAR(q.estimated_date) = " . intval($year) : "";
        $yearFilterU = ($year && $year !== 'all') ? " AND YEAR(uq.date) = " . intval($year) : "";

        $query = "
        SELECT q.id, q.no_quote, c.company, c.ru, q.subtotal, q.title, q.estimated_date,
               q.status,
               COALESCE(NULLIF(TRIM(CONCAT_WS(' ', q.note, CASE WHEN q.status_date IS NOT NULL AND q.status_date != '' THEN CONCAT('(', q.status_date, ')') ELSE '' END)), ''), 'Belum di update') AS tip,
               q.type, 'service' AS row_type,
               NULL AS plant_name
        FROM quotation q
        LEFT JOIN pic p ON p.id = q.id_pic
        LEFT JOIN client c ON c.id = p.id_client
        INNER JOIN users u ON u.id = q.id_sales
        WHERE u.id = $userId AND q.status = 80 AND q.level = '1' AND q.is_primary = '1' AND q.type != 'Unit'$yearFilterQ
        GROUP BY q.id

        UNION ALL

        SELECT uq.id, uq.no_quote,
               COALESCE(NULLIF(c2.company,''),'-') AS company,
               NULL AS ru,
               uq.subtotal,
               COALESCE(NULLIF(uq.title,''),'-') AS title,
               uq.date AS estimated_date,
               uq.status,
               COALESCE(
                   (SELECT CONCAT(DATE_FORMAT(sh.created_at,'%d-%m-%y'),' | ',COALESCE(NULLIF(sh.note,''),'Belum di update'))
                    FROM unit_quotation_status_history sh
                    WHERE sh.id_unit_quotation = uq.id
                    ORDER BY sh.created_at DESC LIMIT 1),
                   'Belum di update'
               ) AS tip,
               uq.type,
               'unit' AS row_type,
               cp.name AS plant_name
        FROM unit_quotation uq
        LEFT JOIN client c2 ON c2.id = NULLIF(uq.id_client,'')
        LEFT JOIN client_plants cp ON cp.id = NULLIF(uq.id_plant,'')
        WHERE uq.id_sales = $userId AND uq.status = 'hot_prospect' AND uq.is_latest = 1$yearFilterU

        ORDER BY estimated_date ASC";

        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['data' => $result], JSON_PRETTY_PRINT);
    } catch (PDOException $e) {
        echo json_encode(['data' => [], 'error' => 'Kesalahan Database: ' . $e->getMessage()], JSON_PRETTY_PRINT);
    } finally {
        $pdo = null;
    }
} else {
    echo json_encode(['data' => [], 'error' => 'Pengguna tidak terotentikasi'], JSON_PRETTY_PRINT);
}
?>
