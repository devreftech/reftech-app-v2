<?php
header('Content-Type: application/json');
include 'db.php';
$con = new mysqli($host, $user, $pass, $databaseName);


$qResult = $con->query("SELECT q.*, c.company, u.name FROM quotation q 
LEFT JOIN pic p on p.id = q.id_pic
LEFT JOIN client c on c.id = p.id_client
INNER JOIN users u on u.id = q.id_sales
GROUP BY id ORDER BY q.expired_date ASC;");        
$result = array();                       
while ($fetchData = $qResult->fetch_assoc()) {
    $result[] = $fetchData;
}

$arr = [
    "data" => $result,
];
$hasil = json_encode($arr, JSON_PRETTY_PRINT);

// Menampilkan hasil JSON
echo $hasil;

?>