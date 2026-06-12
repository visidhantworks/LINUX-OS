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
$filename = trim($_POST['filename'] ?? '');

if (!$filename) {
    echo json_encode(['error' => 'Filename required']);
    exit;
}

$stmt = $conn->prepare(
    "INSERT INTO files
    (user_id, filename, folder, content)
    VALUES (?, ?, ?, '')"
);

$stmt->bind_param(
    "iss",
    $user_id,
    $filename,
    $folder
);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'File already exists']);
}

$stmt->close();
$conn->close();
?>