<?php
use Illuminate\Support\Facades\Auth;

header('Content-Type: application/json');
$host = env('DB_HOST', 'localhost');
$users = env('DB_USERNAME', 'root');
$pass = env('DB_PASSWORD', '');

$databaseName = env('DB_DATABASE', 'u877155683_reftech_my');
$tableName = "reports";

// Periksa apakah pengguna terotentikasi
if (Auth::check()) {
    // Pengguna terotentikasi
    $user = Auth::user();

    try {
        // Membuat koneksi PDO
        $pdo = new PDO("mysql:host=$host;dbname=$databaseName;charset=utf8", $users, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Query database for data
        $query = "SELECT r.id, r.no_service, c.company, r.jobdesc, CONCAT(sp.brand, ' ', un.model) AS brand_type,
        COALESCE(NULLIF(CONCAT_WS(' / ', m.serial, m.tag), ''), '-') AS serial_tag, r.date
        FROM reports r
        JOIN machine m on r.id_machine = m.id
        LEFT JOIN pic p on p.id = r.id_pic
        LEFT JOIN client c on c.id = p.id_client
        INNER JOIN users u on u.id = r.id_technician
        INNER JOIN serial_product sp ON sp.id = m.id_unit
        INNER JOIN unit un ON un.id = sp.id_product
        WHERE u.id = :user_id
        ORDER BY r.date DESC";

        $stmt = $pdo->prepare($query);
        $stmt->bindValue(':user_id', $user->id, PDO::PARAM_INT);
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
    } catch (\Exception $e) {
        // Kesalahan koneksi atau eksekusi kueri
        echo json_encode(['error' => 'Kesalahan: ' . $e->getMessage()], JSON_PRETTY_PRINT);
    } finally {
        // Menutup koneksi PDO
        $pdo = null;
    }
} else {
    // Pengguna tidak terotentikasi
    echo json_encode(['error' => 'Pengguna tidak terotentikasi'], JSON_PRETTY_PRINT);
}
?>
