<?php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['loggedin'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once dirname(__DIR__, 2) . '/configure.php';
$conn = new mysqli(
    DB_HOST,
    DB_USER,
    DB_PASS,
    DB_NAME
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