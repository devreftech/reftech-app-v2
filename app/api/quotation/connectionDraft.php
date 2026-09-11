<?php
use Illuminate\Support\Facades\Auth;

header('Content-Type: application/json');
$host = config('database.connections.mysql.host');
$users = config('database.connections.mysql.username');
$pass = config('database.connections.mysql.password');
$databaseName = config('database.connections.mysql.database');

if (Auth::check()) {
    $user = Auth::user();

    // Draft hanya untuk role sales, tidak untuk Admin / Sales Manager
    if (in_array($user->role, ['Admin', 'Sales Manager'])) {
        echo json_encode(['data' => []], JSON_PRETTY_PRINT);
        exit;
    }

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$databaseName;charset=utf8", $users, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec("SET SESSION sql_mode = ''");

        $userId = (int) $user->id;
        $year = request()->get('year');
        $yearFilterU = ($year && $year !== 'all') ? " AND YEAR(uq.date) = " . intval($year) : "";

        $query = "
        SELECT uq.id, uq.no_quote,
               COALESCE(NULLIF(c2.company,''),'-') AS company,
               c2.ru AS ru,
               uq.total AS subtotal,
               COALESCE(NULLIF(uq.title,''),'-') AS title,
               uq.date AS estimated_date,
               'Draft' AS status,
               (SELECT CONCAT(DATE_FORMAT(sh.created_at,'%d-%m-%y'),' | ',COALESCE(NULLIF(sh.note,''),'Draft tersimpan'))
                FROM unit_quotation_status_history sh
                WHERE sh.id_unit_quotation = uq.id
                ORDER BY sh.created_at DESC LIMIT 1) AS tip,
               uq.type,
               'unit' AS row_type,
               1 AS is_draft
        FROM unit_quotation uq
        LEFT JOIN client c2 ON c2.id = NULLIF(uq.id_client,'')
        WHERE uq.id_sales = $userId AND uq.is_draft = 1 AND (uq.is_latest = 1 OR uq.is_latest IS NULL)$yearFilterU
        ORDER BY uq.updated_at DESC, uq.date DESC";

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
    echo json_encode(['data' => []], JSON_PRETTY_PRINT);
}
