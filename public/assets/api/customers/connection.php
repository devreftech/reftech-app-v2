<?php
header('Content-Type: application/json');
include 'db.php';
$con = new mysqli($host, $user, $pass, $databaseName);

//--------------------------------------------------------------------------
// 2) Query database for data
//--------------------------------------------------------------------------
$qResult = $con->query("SELECT c.* , p.name_pic, i.issue, u.name, MAX(a.date) AS date, MAX(a.follow_up) AS follow_up FROM client c
INNER JOIN pic p on c.id = p.id_client
INNER JOIN issues i on c.id_issues = i.id
INNER JOIN users u on c.id_sales = u.id
LEFT JOIN activities a on a.id_client = c.id
WHERE c.role = 'Customers'
GROUP BY id  
ORDER BY a.date DESC;");          //query
$result = array();                        //fetch result 

while ($fetchData = $qResult->fetch_assoc()) {
    $result[] = $fetchData;
}

$arr = [
    "data" => $result,
];

//--------------------------------------------------------------------------
// 3) echo result as json 
//--------------------------------------------------------------------------
$hasil = json_encode($arr, JSON_PRETTY_PRINT);

// Menampilkan hasil JSON
echo $hasil;

?>