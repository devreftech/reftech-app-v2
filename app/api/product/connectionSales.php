<?php
use Illuminate\Support\Facades\Auth;

header('Content-Type: application/json');
$host = env('DB_HOST', '127.0.0.1');
$users = env('DB_USERNAME', 'root');
$pass = env('DB_PASSWORD', '');

$databaseName = env('DB_DATABASE', 'u877155683_reftech_my');
$tableName = "product";

// Periksa apakah pengguna terotentikasi
if (Auth::check()) {
    // Pengguna terotentikasi
    $user = Auth::user();

    try {
        // Membuat koneksi PDO
        $pdo = new PDO("mysql:host=$host;dbname=$databaseName;charset=utf8", $users, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Query database for data
        $query = "SELECT p.*, p.go as go,
                    CONCAT(' (', 
                        CASE 
                            WHEN p.go = 'Replacement' THEN 'R'
                            WHEN p.go = 'Genuine' THEN 'G'
                            ELSE p.go
                        END
                    , ')', p.description) as descrip, s.image, s.brand, s.pn, s.price, CONCAT(p.stock, ' - ', p.warehouse_stock ) AS stok FROM product p
        RIGHT JOIN serial_product s on p.id = s.id_product";

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