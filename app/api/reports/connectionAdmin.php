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
    $year = request('year', date('Y'));

    // Membuat koneksi PDO
    $pdo = new PDO("mysql:host=$host;dbname=$databaseName;charset=utf8", $users, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query database for data
    $query = "SELECT STRAIGHT_JOIN r.*, c.company, u.name AS technician, s.name AS sales ,  CONCAT(sp.brand, ' ', un.model) AS brand_type ,  CONCAT('(', COALESCE(m.serial, '-'), ') - ', COALESCE(m.tag, '-')) AS serial_tag
          FROM reports r
        JOIN machine m on r.id_machine = m.id
          LEFT JOIN pic p ON p.id = r.id_pic
          LEFT JOIN client c ON c.id = p.id_client
          INNER JOIN users u ON u.id = r.id_technician
          INNER JOIN users s ON s.id = c.id_sales
        INNER JOIN serial_product sp ON sp.id = m.id_unit
        INNER JOIN unit un ON un.id = sp.id_product
          WHERE YEAR(r.date) = :year
          GROUP BY r.id, un.id
          ORDER BY r.date ASC";

    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':year', $year, PDO::PARAM_INT);
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
