<?php
use Illuminate\Support\Facades\Auth;

header('Content-Type: application/json');
$host = config('database.connections.mysql.host');
$users = config('database.connections.mysql.username');
$pass = config('database.connections.mysql.password');

$databaseName = config('database.connections.mysql.database');
$tableName = "quotation";

// Periksa apakah pengguna terotentikasi
if (Auth::check()) {
    // Pengguna terotentikasi
    $user = Auth::user();

    try {
        // Membuat koneksi PDO
        $pdo = new PDO("mysql:host=$host;dbname=$databaseName;charset=utf8", $users, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$pdo->exec("SET SESSION sql_mode = ''");

        // Query database for data
        $userId = $user->id;
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
        WHERE u.id = $userId AND q.status IN (20,30,40,60,80) AND q.level = '1' AND q.is_primary = '1' AND q.type != 'Unit'$yearFilterQ
        GROUP BY q.primary_id

        UNION ALL

        SELECT uq.id, uq.no_quote,
               COALESCE(NULLIF(c2.company,''),'-') AS company,
               c2.ru AS ru,
               uq.total AS subtotal,
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
        WHERE uq.id_sales = $userId AND uq.status NOT IN ('hot_prospect','po_received','loss','cancel') AND uq.is_latest = 1$yearFilterU

        ORDER BY estimated_date DESC";

        $stmt = $pdo->prepare($query);
        $stmt->execute();

        // Fetch result 
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $arr = [
            "data" => $result,
        ];

        // Echo result as JSON 
        $hasil = json_encode($arr, JSON_PRETTY_PRINT);

        // Menampilkan hasil JSON
        echo $hasil;
    } catch (PDOException $e) {
        // Kesalahan koneksi atau eksekusi kueri
        echo json_encode(['error' => 'Kesalahan Database: ' . $e->getMessage()], JSON_PRETTY_PRINT);
    } finally {
        // Menutup koneksi PDO
        $pdo = null;
    }
} else {
    // Pengguna tidak terotentikasi
    echo json_encode(['error' => 'Pengguna tidak terotentikasi'], JSON_PRETTY_PRINT);
}
?>
