<?php
$conn = new mysqli(
    "sql302.infinityfree.com",
    "if0_42099223",
    "sidhant1326",
    "if0_42099223_myos"
);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = trim($_POST['username'] ?? '');
$pin = trim($_POST['pin'] ?? '');

if (empty($username) || empty($pin)) {
    die("Please fill all fields.");
}

$check = $conn->prepare("SELECT id FROM users WHERE username = ?");
$check->bind_param("s", $username);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    die("Username already exists.");
}

$stmt = $conn->prepare(
    "INSERT INTO users (username, pin) VALUES (?, ?)"
);

$stmt->bind_param("ss", $username, $pin);

if ($stmt->execute()) {
    header("Location: index-2.html");
    exit();
} else {
    echo "Registration failed.";
}

$conn->close();
?>