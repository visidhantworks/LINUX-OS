<?php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
require_once dirname(__DIR__, 2) . '/configure.php';
$conn = getDbConnection();

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$folder = $_GET['folder'] ?? '';
$filename = $_GET['filename'] ?? '';

if (!in_array($folder, ['home', 'downloads', 'desktop']) || !$filename) {
    echo json_encode(['error' => 'Invalid folder or filename']);
    exit;
}

$stmt = $conn->prepare(
    "SELECT content
     FROM files
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

$stmt->execute();

$result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode([
            'content' => $row['content']
        ]);
    } else {
        echo json_encode([
            'error' => 'File not found'
        ]);
    }

$stmt->close();
$conn->close();
?>