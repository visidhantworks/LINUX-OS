<?php
session_start();

header('Content-Type: application/json');

if (
    !isset($_SESSION['loggedin']) ||
    $_SESSION['loggedin'] !== true ||
    !isset($_SESSION['user_id'])
) {
    echo json_encode([
        'output' => 'Unauthorized'
    ]);
    exit;
}
require_once dirname(__DIR__, 2) . '/configure.php';
$conn = new mysqli(
    DB_HOST,
    DB_USER,
    DB_PASS,
    DB_NAME
);

if ($conn->connect_error) {
    echo json_encode([
        'output' => 'Database connection failed'
    ]);
    exit;
}

$user_id = $_SESSION['user_id'];

if (!isset($_SESSION['current_folder'])) {
    $_SESSION['current_folder'] = 'home';
}

$current_folder = $_SESSION['current_folder'];

$command = trim($_POST['command'] ?? '');

if (empty($command)) {
    echo json_encode([
        'output' => ''
    ]);
    exit;
}

$parts = explode(' ', $command);
$cmd = strtolower($parts[0]);
$args = array_slice($parts, 1);

switch ($cmd) {

    case 'pwd':

        echo json_encode([
            'output' => $current_folder
        ]);
        break;

    case 'cd':

        if (empty($args[0])) {
            $_SESSION['current_folder'] = 'home';
        } else {
            $_SESSION['current_folder'] = $args[0];
        }

        echo json_encode([
            'output' => '',
            'currentDir' => $_SESSION['current_folder']
        ]);
        break;

    case 'ls':

        $stmt = $conn->prepare(
            "SELECT filename
             FROM files
             WHERE user_id = ?
             AND folder = ?"
        );

        $stmt->bind_param(
            "is",
            $user_id,
            $current_folder
        );

        $stmt->execute();

        $result = $stmt->get_result();

        $files = [];

        while ($row = $result->fetch_assoc()) {
            $files[] = $row['filename'];
        }

        $output = empty($files)
            ? "No files found"
            : implode("\n", $files);

        echo json_encode([
            'output' => $output
        ]);

        break;

    case 'touch':

        if (empty($args[0])) {
            echo json_encode([
                'output' => 'touch: missing filename'
            ]);
            break;
        }

        $filename = $args[0];

        $stmt = $conn->prepare(
            "INSERT INTO files
            (user_id, filename, folder, content)
            VALUES (?, ?, ?, '')"
        );

        $stmt->bind_param(
            "iss",
            $user_id,
            $filename,
            $current_folder
        );

        if ($stmt->execute()) {
            echo json_encode([
                'output' => "Created $filename"
            ]);
        } else {
            echo json_encode([
                'output' => "File already exists"
            ]);
        }

        break;

    case 'cat':

        if (empty($args[0])) {
            echo json_encode([
                'output' => 'cat: missing filename'
            ]);
            break;
        }

        $filename = $args[0];

        $stmt = $conn->prepare(
            "SELECT content
             FROM files
             WHERE user_id = ?
             AND filename = ?
             AND folder = ?"
        );

        $stmt->bind_param(
            "iss",
            $user_id,
            $filename,
            $current_folder
        );

        $stmt->execute();

        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {

            echo json_encode([
                'output' => $row['content']
            ]);

        } else {

            echo json_encode([
                'output' => 'File not found'
            ]);

        }

        break;

    case 'rm':

        if (empty($args[0])) {
            echo json_encode([
                'output' => 'rm: missing filename'
            ]);
            break;
        }

        $filename = $args[0];

        $stmt = $conn->prepare(
            "DELETE FROM files
             WHERE user_id = ?
             AND filename = ?
             AND folder = ?"
        );

        $stmt->bind_param(
            "iss",
            $user_id,
            $filename,
            $current_folder
        );

        $stmt->execute();

        if ($stmt->affected_rows > 0) {

            echo json_encode([
                'output' => "Deleted $filename"
            ]);

        } else {

            echo json_encode([
                'output' => "File not found"
            ]);

        }

        break;

    default:

        echo json_encode([
            'output' => 'Command not supported'
        ]);
}

$conn->close();
?>