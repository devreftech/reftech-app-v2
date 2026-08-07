<?php
use Illuminate\Support\Facades\Auth;

header('Content-Type: application/json');
$host = env('DB_HOST', '127.0.0.1');
$users = env('DB_USERNAME', 'root');
$pass = env('DB_PASSWORD', '');

$databaseName = env('DB_DATABASE', 'u877155683_reftech_my');
$tableName = "quotation";

// Periksa apakah pengguna terotentikasi
if (Auth::check()) {
    // Pengguna terotentikasi
    $user = Auth::user();

    try {
        // Membuat koneksi PDO
        $pdo = new PDO("mysql:host=$host;dbname=$databaseName;charset=utf8", $users, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Query database for data
        $year = request()->get('year');
        $yearFilter = ($year && $year !== 'all') ? " AND YEAR(q.estimated_date) = " . intval($year) : "";
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
        ) AS description
        FROM quotation q
        LEFT JOIN pic p on p.id = q.id_pic
        LEFT JOIN client c on c.id = p.id_client
        INNER JOIN users u on u.id = q.id_sales
        WHERE u.id = $user->id AND q.status = '0' AND q.level = '1'$yearFilter
        GROUP BY q.id ORDER BY q.expired_date ASC";

        $stmt = $pdo->prepare($query);
        // $stmt->bindParam(':user_id', $user->id, PDO::PARAM_INT);
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
