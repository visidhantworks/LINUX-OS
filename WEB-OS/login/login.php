<?php
session_start();

// Database connection
require_once dirname(__DIR__, 2) . '/configure.php';
$conn = new mysqli(
    DB_HOST,
    DB_USER,
    DB_PASS,
    DB_NAME
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