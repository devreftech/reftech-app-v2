<?php
use Illuminate\Support\Facades\Auth;

header('Content-Type: application/json');
$host = config('database.connections.mysql.host');
$users = config('database.connections.mysql.username');
$pass = config('database.connections.mysql.password');

$databaseName = config('database.connections.mysql.database');

if (Auth::check()) {
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$databaseName;charset=utf8", $users, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "SELECT
        u.*,
        s.pn,
        s.brand,
        s.price,
        s.id AS id_pn,
        u.id AS id_p
            FROM unit u
            LEFT JOIN serial_product s ON u.id = s.id_product
            LEFT JOIN detail_product dp ON u.id = dp.id_product
            WHERE u.type = 'global' AND u.unit = 'PISTON COMPRESSOR'
            GROUP BY u.id
            ORDER BY u.id DESC";

        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(["data" => $result], JSON_PRETTY_PRINT);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Kesalahan Database: ' . $e->getMessage()], JSON_PRETTY_PRINT);
    } finally {
        $pdo = null;
    }
} else {
    echo json_encode(['error' => 'Pengguna tidak terotentikasi'], JSON_PRETTY_PRINT);
}
?>
