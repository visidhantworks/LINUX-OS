<?php
session_start();

// Database connection
$conn = new mysqli(
    "sql302.infinityfree.com",
    "if0_42099223",
    "sidhant1326",
    "if0_42099223_myos"
);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
 

// Get submitted username and pin
$username = $_POST['username'] ?? '';
$pin = $_POST['pin'] ?? '';

if (!empty($username) && !empty($pin)) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND pin = ?");
    $stmt->bind_param("ss", $username, $pin);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $userData = $result->fetch_assoc();
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $userData['username'];
        $_SESSION['user_id'] = $userData['id'];

        header("Location: ../desktop");
        exit();
        }
     else {
        echo "Invalid Username or PIN!";
    }
}