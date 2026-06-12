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