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
        $year = request()->get('year');
        $yearFilter = ($year && $year !== 'all') ? " AND YEAR(q.estimated_date) = " . intval($year) : "";
        $yearFilterU = ($year && $year !== 'all') ? " AND YEAR(uq.date) = " . intval($year) : "";

        $query = "SELECT q.id, q.no_quote, q.harga_total, q.title, q.estimated_date, q.status, q.note, q.type,
        c.company, c.ru, u.name,
        COALESCE(
            NULLIF(TRIM(q.title), ''),
            (
                SELECT GROUP_CONCAT(DISTINCT COALESCE(NULLIF(TRIM(ds.detail), ''), NULLIF(TRIM(s.subtitle), '')) SEPARATOR ' | ')
                FROM subtitle_quotation s
                LEFT JOIN detail_service_quotation ds ON ds.id_subtitle = s.id
                WHERE s.id_quotation = q.id
            )
        ) AS description,
        'service' AS row_type
        FROM quotation q
        LEFT JOIN pic p on p.id = q.id_pic
        LEFT JOIN client c on c.id = p.id_client
        INNER JOIN users u on u.id = q.id_sales
        WHERE u.id = $user->id AND q.status = '0' AND q.level = '1' AND q.is_primary = '1'$yearFilter
        AND NOT EXISTS (SELECT 1 FROM payment pay WHERE pay.id_quotation = q.id AND pay.method = 'Escrow')
        GROUP BY q.id

        UNION ALL

        SELECT uq.id, uq.no_quote, uq.total AS harga_total, COALESCE(NULLIF(uq.title,''),'-') AS title, uq.date AS estimated_date, uq.status,
        (SELECT COALESCE(NULLIF(sh.note,''),'Belum di update') FROM unit_quotation_status_history sh WHERE sh.id_unit_quotation = uq.id ORDER BY sh.created_at DESC LIMIT 1) AS note,
        uq.type, COALESCE(NULLIF(c2.company,''),'-') AS company, NULL AS ru, u2.name, COALESCE(NULLIF(uq.title,''),'-') AS description, 'unit' AS row_type
        FROM unit_quotation uq
        LEFT JOIN client c2 ON c2.id = NULLIF(uq.id_client,'')
        INNER JOIN users u2 ON u2.id = uq.id_sales
        WHERE uq.id_sales = $user->id AND uq.status IN ('loss','cancel') AND uq.is_latest = 1$yearFilterU
        AND NOT EXISTS (SELECT 1 FROM payment pay2 WHERE pay2.id_unit_quotation = uq.id AND pay2.method = 'Escrow')

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
