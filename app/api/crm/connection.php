<?php
use Illuminate\Support\Facades\Auth;

header('Content-Type: application/json');
$host = "localhost";
$users = "u877155683_reftech_my";
$pass = "REFtechjaya321!";

$databaseName = "u877155683_reftech_my";
$tableName = "client";

// Periksa apakah pengguna terotentikasi
if (Auth::check()) {
    // Pengguna terotentikasi
    $user = Auth::user();

    try {
        // Membuat koneksi PDO
        $pdo = new PDO("mysql:host=$host;dbname=$databaseName;charset=utf8", $users, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Query database for data
        $query = "SELECT c.*, p.name_pic, s.status, u.name, DATE_FORMAT(MAX(a.date), '%d-%m-%Y') AS date, DATE_FORMAT(MAX(a.follow_up), '%d-%m-%Y') AS follow_up, MAX(a.note) AS note 
                  FROM client c
                  INNER JOIN users u ON c.id_sales = u.id
                  LEFT OUTER JOIN pic p ON c.id = p.id_client
                  LEFT JOIN crm_status s ON c.id = s.id_client
                  LEFT JOIN activities a ON a.id_client = c.id
                  WHERE c.role = 'Customers' AND u.id = $user->id AND c.id != 5508
                  GROUP BY c.id  
                  ORDER BY MAX(a.date) DESC";

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