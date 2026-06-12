<?php
// Save this as index.php in your project folder
// Make sure your server has write permission on this folder

// Get all .c files
$files = glob("*.c");

// Current file name is passed via GET or POST or fallback to first .c file or "program.c"
$currentFile = null;
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  // POST from Save or Run form
  $currentFile = basename($_POST['filename'] ?? '');
  $code = $_POST['code'] ?? '';

  // Save code to current file if valid name
  if ($currentFile && preg_match('/^[a-zA-Z0-9_\-]+\.c$/', $currentFile)) {
    file_put_contents($currentFile, $code);
    // Update files list (file might be newly created)
    $files = glob("*.c");
  }
} else {
  // GET request - file parameter
  $requestedFile = $_GET['file'] ?? null;
  if ($requestedFile && preg_match('/^[a-zA-Z0-9_\-]+\.c$/', $requestedFile)) {
    $currentFile = $requestedFile;
  } else {
    $currentFile = $files[0] ?? "program.c";
  }
}

// *** New: Create the file immediately if it doesn't exist ***
if (!file_exists($currentFile)) {
  file_put_contents($currentFile, "// New file. Start coding...\n");
}

// Load current file content for editor
$codeContent = file_get_contents($currentFile);

// Handle Run (compile & execute) if user clicked Run button
$output = "";
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['run'])) {
  $exeFile = basename($currentFile, ".c") . ".out";

  // Compile command
  $compileCmd = "gcc " . escapeshellarg($currentFile) . " -o " . escapeshellarg($exeFile) . " 2>&1";
  $compileOutput = shell_exec($compileCmd);

  if (!empty($compileOutput)) {
    $output = "<span style='color: #f44747;'>" . htmlspecialchars($compileOutput) . "</span>";
  } else {
    $runCmd = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? $exeFile : "./" . $exeFile;
    $runOutput = shell_exec($runCmd . " 2>&1");
    $output = nl2br(htmlspecialchars($runOutput));
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>Web C IDE</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
<link rel="stylesheet" href="style.css">
</head>

<body>


  <div class="code-window">
    <div class="header">Simple File Manager</div>
    <button id="code-close-btn" title="Close" aria-label="Close file manager">
      ×
    </button>
    <div class="inner-body">

      <div class="sidebar">
        <i class="fas fa-copy" title="Copy"></i>
        <i class="fas fa-magnifying-glass" title="Search"></i>
        <i class="fas fa-code" title="Code"></i>
        <i class="fas fa-gear" title="Settings"></i>
      </div>

      <div class="explorer">
        <h3>
          EXPLORER
          <button class="new-file-button" onclick="newFile()">+ New File</button>
        </h3>
        <ul id="fileList">
          <?php foreach ($files as $file): ?>
            <li data-filename="<?= htmlspecialchars($file) ?>" class="<?= $file === $currentFile ? "active" : "" ?>">
              <?= htmlspecialchars($file) ?>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>

      <div class="main">
        <div class="editor-header">
          <span id="currentFilename"><?= htmlspecialchars($currentFile) ?></span>
        </div>

        <form method="POST" id="cForm" action="" autocomplete="off">
          <input type="hidden" name="filename" id="filenameInput" value="<?= htmlspecialchars($currentFile) ?>" />
          <div class="editor-area">
            <textarea id="codeEditor" name="code" spellcheck="false" rows="25"
              autofocus><?= htmlspecialchars($codeContent) ?></textarea>
          </div>

          <div class="button-group">
            <button type="submit" class="save-button" name="save" title="Save File">Save</button>
            <button type="submit" class="run-button" name="run" title="Compile & Run">Run</button>
          </div>
        </form>

        <div class="terminal-header">Output / Terminal</div>
        <div class="output-section" id="output">
          <?= $output ?: "No output yet. Click Run to compile and execute your code." ?>
        </div>
      </div>

    </div>
  </div>
  <script>
    const fileList = document.getElementById('fileList');
    const filenameInput = document.getElementById('filenameInput');
    const currentFilename = document.getElementById('currentFilename');
    const codeEditor = document.getElementById('codeEditor');

    // Click file in sidebar to load it
    fileList.addEventListener('click', e => {
      if (e.target.tagName === 'LI') {
        const selectedFile = e.target.getAttribute('data-filename');
        if (selectedFile) {
          // Redirect to load selected file
          window.location.href = "?file=" + encodeURIComponent(selectedFile);
        }
      }
    });

    // New File button
    function newFile() {
      let newName = prompt("Enter new filename (with .c extension):");
      if (!newName) return;
      newName = newName.trim();
      if (!newName.match(/^[a-zA-Z0-9_\-]+\.c$/)) {
        alert("Invalid filename. Use letters, numbers, underscore, hyphen and end with .c");
        return;
      }
      // Redirect with new file name, PHP will create it automatically now
      window.location.href = "?file=" + encodeURIComponent(newName);
    }
  </script>



    <script>
      document.addEventListener("DOMContentLoaded", () => {
        const closeButton = document.getElementById("code-close-btn");
        if (closeButton) {
          closeButton.addEventListener("click", () => {
            // Access the parent window from within the iframe
            window.parent.document.getElementById("codeid").style.display =
              "none";
            // Optional: Reset the showfile variable if needed
            window.parent.showfile = 1;
          });
        }
      });
    </script>
</body>

</html>