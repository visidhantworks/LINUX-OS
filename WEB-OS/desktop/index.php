<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../login.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Windows Desktop Clone</title>
    <link rel="stylesheet" href="desktop.css" />
  </head>
  <body>
    <div class="desktop">
<!--       
      <div class="icon-grid">
        <div class="icon">
          <img src="recyclebin.png" alt="Recycle Bin" />
          <span>Recycle Bin</span>
        </div>
        <div class="icon">
          <img src="myfolder.png" alt="My Folder" />
          <span>My Folder</span>
        </div>
        <div class="icon">
          <img src="documentt.png" alt="Document" />
          <span>Document.txt</span>
        </div> -->

 <div
    class="icon"
    role="button"
    tabindex="0"
    onclick="openTerminal()"
    onkeydown="if(event.key==='Enter'||event.key===' '){openTerminal();}"
>

          <img src="terminal.svg" alt="Document" />
          <span>Terminal</span>
        </div>
        <!-- Add more icons here -->
      </div>
      <iframe 
      src="../terminal/terminal.php" 
      id="terminalid" 
      title= 'Terminal Window'
      style="display: none; position: absolute; top: 10%; left: 20%; width: 60%; height: 70%; border: none; box-shadow: 0 0 15px rgba(0,0,0,0.5); border-radius: 10px; z-index: 999;">
    </iframe>
    <iframe 
    src="../filemanager/index.php" 
    id="fileid" 
    title = "filemanager"
    style="display: none; position: absolute; top: 10%; left: 20%; width: 60%; height: 70%; border: none; box-shadow: 0 0 15px rgba(0,0,0,0.5); border-radius: 10px; z-index: 999;">
  </iframe>
 
  
      <iframe 
    src="../code/index.php" 
    id="codeid" 
    title = 'code'
    scrolling="no"
    style="display: none; position: absolute; top: 5%; left: 5%; width: 95%; height: 80%; border: none; box-shadow: 0 0 15px rgba(0,0,0,0.5); border-radius: 10px; z-index: 999;">
  </iframe>

      <script src="terminal.js"></script>
      <script src="code.js"></script>
      


      <!-- Taskbar -->
      <div class="taskbar">
        <div class="start-button">🪟</div>
        <div class="search-bar">
          <input type="text" class="search" placeholder="Type here to search..." />
        </div>
        <div class="pinned-icons">
          <div
    class="pinned-icon"
    role="button"
    tabindex="0"
    onclick="openfile()"
    onkeydown="if(event.key==='Enter'||event.key===' '){openfile();}"
>
    📁
</div>
          <div class="pinned-icon">🌐</div>
    <div
    class="pinned-icon"
    role="button"
    tabindex="0"
    onclick="opencode()"
    onkeydown="if(event.key==='Enter'||event.key===' '){opencode();}"
>
    🖥️
</div>
        </div>
        <div class="system-tray">
          <span>🔔</span>
          <span>🔋</span>
          <span>🌐</span>
          <span>ENG</span>
          <span id="mytime">🕒 8:30 AM</span>
          <button id="logout-btn">⏻</button>
        </div>
      </div>
    </div>
    <script src="livetime.js"></script>
<script src="fileman.js"></script>
    <script src="drag.js"></script>
    <script>
document.getElementById("logout-btn").addEventListener("click", () => {

        window.location.href = "../logout.php";
    
});
window.addEventListener("message", (event) => {

    if (event.data === "closeFileManager") {
        document.getElementById("fileid").style.display = "none";
    }

});
</script>
  </body>
</html>
