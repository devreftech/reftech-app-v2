<?php
use Illuminate\Support\Facades\Auth;

header('Content-Type: application/json');
$host = "localhost";
$users = "u877155683_reftech_my";
$pass = "REFtechjaya321!";

$databaseName = "u877155683_reftech_my";
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
        $query = "SELECT 
        m.id,
        u.sku,
        u.unit,
        s.brand,
        s.pn,
        m.serial,
        u.power,
        s.bar,
        s.air_cap,
        m.status,
        m.price,
        m.tag,
        m.price_rental,
        m.price_best
            FROM 
                machine m
            JOIN 
                serial_product s ON s.id = m.id_unit
            JOIN 
                unit u ON u.id = s.id_product
            WHERE m.id_client = 5387 AND m.status_unit = 'Second'
            GROUP BY 
                m.id";

        // $query = "SELECT 
        // u.*,
        // s.pn,
        // s.brand,
        // s.price,
        // s.id AS id_pn,
        // u.id AS id_p
        //     FROM 
        //         unit u
        //     LEFT JOIN 
        //         serial_product s ON u.id = s.id_product
        //     LEFT JOIN 
        //         detail_product dp ON u.id = dp.id_product
        //         WHERE u.type = 'lokal' 
        //     GROUP BY 
        //         u.id, s.pn";

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