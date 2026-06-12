<?php
header('Content-Type: application/json');

$conn = new mysqli(
    "sql302.infinityfree.com",
    "if0_42099223",
    "sidhant1326",
    "if0_42099223_myos"
);
 

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit();
}

$sql = "SELECT username FROM users";
$result = $conn->query($sql);

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row['username'];
}

echo json_encode($users);
?>
