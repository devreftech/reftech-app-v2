<?php
use Illuminate\Support\Facades\Auth;

header('Content-Type: application/json');
$host = "localhost";
$users = "u877155683_reftech_my";
$pass = "REFtechjaya321!";

$databaseName = "u877155683_reftech_my";
$tableName = "product_in";

// Periksa apakah pengguna terotentikasi
if (Auth::check()) {
    // Pengguna terotentikasi
    $user = Auth::user();

    try {
        // Membuat koneksi PDO
        $pdo = new PDO("mysql:host=$host;dbname=$databaseName;charset=utf8", $users, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Query database for data
        $query = "SELECT p.*, CONCAT(pr.commodity, ' - ', dp.replacement) AS product, CONCAT(d.qty, ' ', pr.unit) as qty  FROM product_in p 
        LEFT JOIN detail_product_in AS d ON d.id_product_in = p.id 
        LEFT JOIN detail_product AS dp ON d.id_detail_product = dp.id 
        LEFT JOIN product AS pr ON dp.id_product = pr.id
        WHERE p.invoice IS NOT NULL AND p.tax = '0'
        ";

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