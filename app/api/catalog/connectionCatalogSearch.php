<?php
use Illuminate\Support\Facades\Auth;

header('Content-Type: application/json');

if (!Auth::check()) {
    echo json_encode(['error' => 'Pengguna tidak terotentikasi']);
    exit;
}

$host = config('database.connections.mysql.host');
$user = config('database.connections.mysql.username');
$pass = config('database.connections.mysql.password');
$db   = config('database.connections.mysql.database');

$q = trim(request()->get('q', ''));

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec("SET SESSION sql_mode = ''");

    $like = '%' . $q . '%';

    $sql = "SELECT
        u.id,
        u.sku,
        u.brand,
        u.model,
        u.unit,
        u.type_unit,
        u.bar,
        u.air_cap,
        u.power,
        u.voltage,
        u.exhaust,
        u.connect,
        u.cooling,
        u.refrigerant_type,
        u.pdp,
        u.filtration,
        u.oil_content,
        u.grade,
        u.capacity,
        u.material,
        u.test_pressure,
        u.inlet_pressure,
        u.outlet_pressure,
        u.inlet_cap,
        u.outlet_cap,
        u.dimension,
        u.weight,
        u.desc,
        COALESCE(cu.price_idr, u.harga_jual, 0) AS price,
        COALESCE(ui.stock_ready, 0) AS stock_ready,
        ui.serial_numbers
    FROM unit u
    LEFT JOIN catalog_unit cu ON cu.id_unit = u.id AND cu.is_active = 1
    LEFT JOIN (
        SELECT 
            id_unit,
            COUNT(*) as stock_ready,
            GROUP_CONCAT(serial_number ORDER BY id SEPARATOR ', ') as serial_numbers
        FROM unit_inventory
        WHERE status = 'available'
        GROUP BY id_unit
    ) ui ON ui.id_unit = u.id
    WHERE (cu.id IS NOT NULL OR COALESCE(ui.stock_ready, 0) > 0)
      AND (u.sku LIKE :q1 OR u.brand LIKE :q2 OR u.model LIKE :q3 OR u.unit LIKE :q4)
    ORDER BY (COALESCE(ui.stock_ready, 0) > 0) DESC, u.brand, u.sku
    LIMIT 30";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':q1', $like);
    $stmt->bindValue(':q2', $like);
    $stmt->bindValue(':q3', $like);
    $stmt->bindValue(':q4', $like);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($results, JSON_PRETTY_PRINT);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    $pdo = null;
}
?>
