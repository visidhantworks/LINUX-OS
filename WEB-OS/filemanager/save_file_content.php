<?php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];

$conn = new mysqli(
    "sql302.infinityfree.com",
    "if0_42099223",
    "sidhant1326",
    "if0_42099223_myos"
);
if ($conn->connect_error) {
    echo json_encode([
        'error' => 'Database connection failed'
    ]);
    exit;
}

$folder = $_POST['folder'] ?? '';
$filename = $_POST['filename'] ?? '';
$content = $_POST['content'] ?? '';

if (!in_array($folder, ['home', 'downloads', 'desktop']) || empty($filename)) {
    echo json_encode([
        'error' => 'Invalid folder or filename'
    ]);
    exit;
}

$stmt = $conn->prepare("
    UPDATE files
    SET content = ?
    WHERE user_id = ?
    AND folder = ?
    AND filename = ?
");

$stmt->bind_param(
    "siss",
    $content,
    $user_id,
    $folder,
    $filename
);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true
    ]);
} else {
    echo json_encode([
        'error' => 'Failed to save file'
    ]);
}

$stmt->close();
$conn->close();
?>