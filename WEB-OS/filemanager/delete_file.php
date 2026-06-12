<?php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['loggedin'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$conn = new mysqli(
    "sql302.infinityfree.com",
    "if0_42099223",
    "sidhant1326",
    "if0_42099223_myos"
);

$user_id = $_SESSION['user_id'];

$folder = $_POST['folder'] ?? '';
$filename = $_POST['filename'] ?? '';

$stmt = $conn->prepare(
    "DELETE FROM files
     WHERE user_id = ?
     AND folder = ?
     AND filename = ?"
);

$stmt->bind_param(
    "iss",
    $user_id,
    $folder,
    $filename
);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'Delete failed']);
}

$stmt->close();
$conn->close();
?>