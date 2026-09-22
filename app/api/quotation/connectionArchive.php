<?php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

header('Content-Type: application/json');

// Periksa apakah pengguna terotentikasi
if (Auth::check()) {
    // Pengguna terotentikasi
    $user = Auth::user();

    try {
        $pdo = DB::connection()->getPdo();
        $pdo->exec("SET SESSION sql_mode = ''");

        // Query database for data
        $year = request()->get('year');
        $yearFilter = ($year && $year !== 'all') ? " AND YEAR(q.estimated_date) = " . intval($year) : "";
        $query = "SELECT q.*, COALESCE(NULLIF(TRIM(CONCAT_WS(' ', q.note, CASE WHEN q.status_date IS NOT NULL AND q.status_date != '' THEN CONCAT('(', q.status_date, ')') ELSE '' END)), ''), 'Belum di update') AS tip, c.company, u.name, NULL AS plant_name FROM quotation q
        LEFT JOIN pic p on p.id = q.id_pic
        LEFT JOIN client c on c.id = p.id_client
        INNER JOIN users u on u.id = q.id_sales
        WHERE u.id = $user->id AND q.status IN (20,30,40,60,80,100) AND q.level = '0'$yearFilter
        GROUP BY id ORDER BY q.expired_date ASC";

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
    } catch (\Throwable $e) {
        // Kesalahan koneksi atau eksekusi kueri
        echo json_encode(['data' => [], 'error' => 'Kesalahan Database: ' . $e->getMessage()], JSON_PRETTY_PRINT);
    }
} else {
    // Pengguna tidak terotentikasi
    echo json_encode(['data' => [], 'error' => 'Pengguna tidak terotentikasi'], JSON_PRETTY_PRINT);
}
?>
