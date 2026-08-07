<?php
use Illuminate\Support\Facades\Auth;

header('Content-Type: application/json');

if (!Auth::check()) {
    echo json_encode(['error' => 'Pengguna tidak terotentikasi']);
    exit;
}

$host         = env('DB_HOST', '127.0.0.1');
$users        = env('DB_USERNAME', 'root');
$pass         = env('DB_PASSWORD', '');
$databaseName = env('DB_DATABASE', 'u877155683_reftech_my');

$typeUnit   = request()->get('type_unit', '');
$generation = request()->get('generation', '');
$category   = request()->get('category', '');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$databaseName;charset=utf8", $users, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $conditions = [];
    $params     = [];

    if ($category !== '') {
        $conditions[] = 'u.unit = :category';
        $params[':category'] = $category;
    }
    if ($typeUnit !== '') {
        $conditions[] = 'u.type_unit = :type_unit';
        $params[':type_unit'] = $typeUnit;
    }
    if ($generation !== '') {
        $conditions[] = 'u.generation = :generation';
        $params[':generation'] = $generation;
    }

    $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

    $query = "
    SELECT cu.id, cu.id_unit, cu.price_idr, cu.price_usd, cu.spec_note, cu.is_active,
           u.sku, u.brand, u.model, u.unit AS category, u.type_unit, u.generation,
           u.power, u.air_cap, u.bar, u.cooling, u.connect, u.exhaust,
           u.refrigerant_type, u.pdp, u.dimension, u.weight, u.desc,
           u.filtration, u.oil_content, u.capacity, u.grade, u.material, u.voltage
    FROM catalog_unit cu
    INNER JOIN unit u ON u.id = cu.id_unit
    $where
    ORDER BY u.brand, u.model
    ";

    $stmt = $pdo->prepare($query);
    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val);
    }
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['data' => $result], JSON_PRETTY_PRINT);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Kesalahan Database: ' . $e->getMessage()], JSON_PRETTY_PRINT);
} finally {
    $pdo = null;
}
?>
