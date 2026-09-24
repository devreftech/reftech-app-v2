<?php
use Illuminate\Support\Facades\Auth;

header('Content-Type: application/json');
$host = config('database.connections.mysql.host');
$users = config('database.connections.mysql.username');
$pass = config('database.connections.mysql.password');

$databaseName = config('database.connections.mysql.database');
$tableName = "product";

// Periksa apakah pengguna terotentikasi
if (Auth::check()) {
    // Pengguna terotentikasi
    $user = Auth::user();

    try {
        // Membuat koneksi PDO
        $pdo = new PDO("mysql:host=$host;dbname=$databaseName;charset=utf8", $users, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec("SET SESSION sql_mode = ''");

        // Query database for data with last out date and out history
        $query = "SELECT 
            p.*,
            s.pn,
            s.price,
            p.id AS id_p, 
            (p.stock + p.warehouse_stock) AS all_stock,
            IFNULL(
                GROUP_CONCAT(DISTINCT CONCAT(dp.replacement, '( Rp ', FORMAT(dp.modal, 2), ')' ) SEPARATOR ' || '), 
                'Tidak Ada Replacement'
            ) AS modal_replacements,
            out_history.last_out_date,
            IFNULL(out_history.total_out_90_days, 0) AS total_out_90_days
        FROM 
            product p
        LEFT JOIN 
            serial_product s ON p.id = s.id_product
        LEFT JOIN 
            detail_product dp ON p.id = dp.id_product
        LEFT JOIN (
            SELECT 
                COALESCE(dp.id_product, sp.id_product) as id_product,
                MAX(COALESCE(po.date, po.created_at, dpo.created_at)) as last_out_date,
                SUM(CASE WHEN COALESCE(po.date, po.created_at, dpo.created_at) >= DATE_SUB(NOW(), INTERVAL 90 DAY) THEN dpo.qty ELSE 0 END) as total_out_90_days
            FROM detail_product_out dpo
            LEFT JOIN product_out po ON po.id = dpo.id_product_out
            LEFT JOIN detail_product dp ON dp.id = dpo.id_detail_product
            LEFT JOIN serial_product sp ON sp.id = dpo.id_serial_product
            WHERE dp.id_product IS NOT NULL OR sp.id_product IS NOT NULL
            GROUP BY COALESCE(dp.id_product, sp.id_product)
        ) out_history ON out_history.id_product = p.id
        GROUP BY 
            p.id";

        $stmt = $pdo->prepare($query);
        $stmt->execute();

        // Fetch result 
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $result = [];
        $kpi = [
            'fast_moving' => 0,
            'slow_moving' => 0,
            'dead_stock' => 0,
            'by_order' => 0,
            'dead_stock_asset' => 0,
        ];

        $now = time();

        foreach ($rows as $row) {
            $allStock = (float)($row['stock'] ?? 0) + (float)($row['warehouse_stock'] ?? 0);
            $procurementType = !empty($row['procurement_type']) ? $row['procurement_type'] : 'ready_stock';
            $lastOutDate = !empty($row['last_out_date']) ? $row['last_out_date'] : null;
            
            $daysInactive = null;
            $lastOutFormatted = '-';
            if ($lastOutDate) {
                $lastOutTs = strtotime($lastOutDate);
                if ($lastOutTs > 0) {
                    $daysInactive = max(0, (int)floor(($now - $lastOutTs) / 86400));
                    $lastOutFormatted = date('d M Y', $lastOutTs);
                }
            }

            // Determine FSN / By Order classification
            if ($procurementType === 'by_order' && $allStock <= 0) {
                $movementStatus = 'by_order';
                $statusLabel = 'By Order';
                $badgeClass = 'bg-label-info';
                $kpi['by_order']++;
            } elseif ($allStock > 0 && ($daysInactive === null || $daysInactive > 180)) {
                $movementStatus = 'dead_stock';
                $statusLabel = 'Dead Stock';
                $badgeClass = 'bg-label-danger';
                $kpi['dead_stock']++;
            } elseif ($daysInactive !== null && $daysInactive <= 60) {
                $movementStatus = 'fast_moving';
                $statusLabel = 'Fast Moving';
                $badgeClass = 'bg-label-success';
                $kpi['fast_moving']++;
            } elseif ($daysInactive !== null && $daysInactive <= 180) {
                $movementStatus = 'slow_moving';
                $statusLabel = 'Slow Moving';
                $badgeClass = 'bg-label-warning';
                $kpi['slow_moving']++;
            } else {
                if ($allStock <= 0) {
                    $movementStatus = 'out_of_stock';
                    $statusLabel = 'Habis';
                    $badgeClass = 'bg-label-secondary';
                } else {
                    $movementStatus = 'dead_stock';
                    $statusLabel = 'Dead Stock';
                    $badgeClass = 'bg-label-danger';
                    $kpi['dead_stock']++;
                }
            }

            $row['procurement_type'] = $procurementType;
            $row['movement_status'] = $movementStatus;
            $row['status_label'] = $statusLabel;
            $row['badge_class'] = $badgeClass;
            $row['days_inactive'] = $daysInactive;
            $row['last_out_formatted'] = $lastOutFormatted;
            
            $result[] = $row;
        }

        $arr = [
            "data" => $result,
            "kpi" => $kpi,
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