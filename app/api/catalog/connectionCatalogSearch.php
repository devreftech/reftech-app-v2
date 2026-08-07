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
        cu.price_idr AS price
    FROM catalog_unit cu
    INNER JOIN unit u ON u.id = cu.id_unit
    WHERE cu.is_active = 1
      AND (u.sku LIKE :q1 OR u.brand LIKE :q2 OR u.model LIKE :q3 OR u.unit LIKE :q4)
    ORDER BY u.brand, u.sku
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
