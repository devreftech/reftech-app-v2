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
        $year = request()->get('year');
        $yearFilter = ($year && $year !== 'all') ? " AND YEAR(q.po_date) = " . intval($year) : "";
        $yearFilterU = ($year && $year !== 'all') ? " AND YEAR(sh.created_at) = " . intval($year) : "";
        $query = "SELECT q.id, q.no_quote, c.company, c.ru, q.nett, q.title, q.po_date,
                         inv.id AS invoice_id, inv.no_po, inv.no_invoice,
                         'service' AS row_type
                  FROM quotation q
                  LEFT JOIN pic p ON p.id = q.id_pic
                  LEFT JOIN client c ON c.id = p.id_client
                  INNER JOIN users u ON u.id = q.id_sales
                  LEFT JOIN invoice inv ON inv.id_quotation = q.id
                  WHERE u.id = $user->id AND q.status = '100' AND q.level = '1' AND q.is_primary = '1'$yearFilter
                  GROUP BY q.id

                  UNION ALL

                  SELECT uq.id, uq.no_quote,
                         COALESCE(NULLIF(cl.company,''),'-') AS company,
                         NULL AS ru,
                         (uq.total - uq.tax_amount) AS nett,
                         COALESCE(NULLIF(uq.title,''),'-') AS title,
                         (SELECT DATE(sh.created_at)
                          FROM unit_quotation_status_history sh
                          WHERE sh.id_unit_quotation = uq.id AND sh.status = 'po_received'
                          ORDER BY sh.created_at DESC LIMIT 1) AS po_date,
                         NULL AS invoice_id,
                         uq.po_number AS no_po,
                         NULL AS no_invoice,
                         'unit' AS row_type
                  FROM unit_quotation uq
                  LEFT JOIN client cl ON cl.id = NULLIF(uq.id_client,'')
                  WHERE uq.id_sales = $user->id AND uq.status = 'po_received' AND uq.is_latest = 1
                  AND EXISTS (
                      SELECT 1 FROM unit_quotation_status_history sh
                      WHERE sh.id_unit_quotation = uq.id AND sh.status = 'po_received'$yearFilterU
                  )

                  ORDER BY po_date DESC";

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
