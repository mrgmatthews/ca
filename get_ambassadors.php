<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$host = "sql206.infinityfree.com:3306"; // Change if needed
$user = "if0_38269952"; // Your MySQL username
$password = "5KdwMdmbu9xzK"; // Your MySQL password
$database = "if0_38269952_ca"; // Your database name

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

$sql = "SELECT * FROM ambassadors";
$result = $conn->query($sql);

$ambassadors = [];

while ($row = $result->fetch_assoc()) {
    $ambassadors[] = $row;
}

echo json_encode($ambassadors);
$conn->close();
?>
