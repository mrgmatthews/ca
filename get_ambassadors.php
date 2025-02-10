<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$host = $_ENV['DB_URL'];;
$user = $_ENV['DB_USER'];
$password = $_ENV['DB_PASS'];
$database = $_ENV['DB_NAME'];

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
