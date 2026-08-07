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
        $userId = $user->id;
        $year = request()->get('year');
        $yearFilter = ($year && $year !== 'all') ? " AND YEAR(q.estimated_date) = " . intval($year) : "";
        $query = "
        SELECT q.id, q.no_quote, c.company, c.ru, q.subtotal, q.title, q.estimated_date,
               q.status, CONCAT(q.note, ' (', q.status_date, ')') AS tip, q.type, 'service' AS row_type
        FROM quotation q
        LEFT JOIN pic p ON p.id = q.id_pic
        LEFT JOIN client c ON c.id = p.id_client
        INNER JOIN users u ON u.id = q.id_sales
        WHERE u.id = $userId AND q.status IN (20,30,40,60,80) AND q.level = '1' AND q.is_primary = '1' AND q.type != 'Unit'$yearFilter
        GROUP BY q.primary_id
        ORDER BY estimated_date ASC";

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
