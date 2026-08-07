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

$draw   = intval(request()->get('draw', 1));
$start  = intval(request()->get('start', 0));
$length = intval(request()->get('length', 10));
$search = trim(request()->get('search', ['value' => ''])['value'] ?? '');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $salesId = intval(request()->get('sales_id', 0));
    $year = request()->get('year');
    $where  = "WHERE uq.is_latest = 1 AND uq.status NOT IN ('hot_prospect','po_received')" . ($salesId ? " AND uq.id_sales = $salesId" : "");
    if ($year && $year !== 'all') {
        $where .= " AND YEAR(uq.date) = " . intval($year);
    }
    $params = [];

    if ($search !== '') {
        $where .= " AND (uq.no_quote LIKE :s1 OR c.company LIKE :s2 OR uq.title LIKE :s3 OR u.name LIKE :s4)";
        $like = '%' . $search . '%';
        $params[':s1'] = $like;
        $params[':s2'] = $like;
        $params[':s3'] = $like;
        $params[':s4'] = $like;
    }

    $sqlCount = "SELECT COUNT(*) FROM unit_quotation uq
                 LEFT JOIN client c ON c.id = NULLIF(uq.id_client, '')
                 INNER JOIN users u ON u.id = uq.id_sales
                 $where";
    $stmtCount = $pdo->prepare($sqlCount);
    foreach ($params as $k => $v) {
        $stmtCount->bindValue($k, $v);
    }
    $stmtCount->execute();
    $total = $stmtCount->fetchColumn();

    $sql = "SELECT
        uq.id,
        uq.no_quote,
        COALESCE(NULLIF(c.company, ''), '-') AS client,
        COALESCE(NULLIF(uq.title, ''), '-') AS title,
        DATE_FORMAT(uq.date, '%d-%m-%Y') AS date,
        uq.total,
        uq.status,
        u.name AS sales_name,
        u.image AS sales_image,
        (SELECT DATE_FORMAT(sh.created_at, '%d-%m-%y')
         FROM unit_quotation_status_history sh
         WHERE sh.id_unit_quotation = uq.id
         ORDER BY sh.created_at DESC LIMIT 1) AS last_note_date,
        (SELECT COALESCE(NULLIF(sh.note, ''), 'Belum di update')
         FROM unit_quotation_status_history sh
         WHERE sh.id_unit_quotation = uq.id
         ORDER BY sh.created_at DESC LIMIT 1) AS last_note
    FROM unit_quotation uq
    LEFT JOIN client c ON c.id = NULLIF(uq.id_client, '')
    INNER JOIN users u ON u.id = uq.id_sales
    $where
    ORDER BY uq.id DESC
    LIMIT :offset, :length";

    $stmt = $pdo->prepare($sql);
    foreach ($params as $k => $v) {
        $stmt->bindValue($k, $v);
    }
    $stmt->bindValue(':offset', $start, PDO::PARAM_INT);
    $stmt->bindValue(':length', $length, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'draw'            => $draw,
        'recordsTotal'    => $total,
        'recordsFiltered' => $total,
        'data'            => $rows,
    ]);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    $pdo = null;
}
?>
