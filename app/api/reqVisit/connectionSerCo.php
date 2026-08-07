<?php
use Illuminate\Support\Facades\Auth;

header('Content-Type: application/json');
$host = "localhost";
$users = "u877155683_reftech_my";
$pass = "REFtechjaya321!";

$databaseName = "u877155683_reftech_my";
$tableName = "req_visit";

// Periksa apakah pengguna terotentikasi
if (Auth::check()) {
    // Pengguna terotentikasi
    $user = Auth::user();

    try {
        // Membuat koneksi PDO
        $pdo = new PDO("mysql:host=$host;dbname=$databaseName;charset=utf8", $users, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Query database for data
        $query = "SELECT r.*, c.company, u.name, CONCAT(s.brand, ' ', un.sku) AS machine
                  FROM req_visit r
                  INNER JOIN machine m ON r.id_machine = m.id
                  INNER JOIN unit un ON un.id = m.id_unit
                  INNER JOIN serial_product s ON s.id_product = un.id
                  INNER JOIN client c ON m.id_client = c.id
                  INNER JOIN users u ON c.id_sales = u.id
                  WHERE r.date IS NULL
                  GROUP BY un.id
                  ORDER BY r.req_date ASC";

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
