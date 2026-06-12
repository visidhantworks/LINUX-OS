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
$conn = new mysqli(
    DB_HOST,
    DB_USER,
    DB_PASS,
    DB_NAME
);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$folder = $_GET['folder'] ?? '';

if (!in_array($folder, ['home', 'downloads', 'desktop'])) {
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare(
    "SELECT filename FROM files
     WHERE user_id = ? AND folder = ?"
);

$stmt->bind_param("is", $user_id, $folder);
$stmt->execute();

$result = $stmt->get_result();

$files = [];

while ($row = $result->fetch_assoc()) {
    $files[] = $row['filename'];
}

echo json_encode($files);

$stmt->close();
$conn->close();
?>