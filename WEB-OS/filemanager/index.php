 <head>
    <style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Segoe UI',sans-serif;
}

body{
    background:#1e1e1e;
    color:white;
    height:100vh;
    overflow:hidden;
}

.filemanager-window{
    width:100%;
    height:100vh;
    display:flex;
    flex-direction:column;
}

.header{
    height:45px;
    background:#2b2b2b;
    display:flex;
    align-items:center;
    padding-left:15px;
    font-weight:500;
}

#close-btn{
    position:absolute;
    right:10px;
    top:5px;
    width:35px;
    height:35px;
    border:none;
    background:none;
    color:white;
    font-size:24px;
    cursor:pointer;
}

#close-btn:hover{
    background:red;
}

.body{
    flex:1;
    display:flex;
}

.sidebar{
    width:220px;
    background:#252526;
}

.sidebar ul{
    list-style:none;
}

.sidebar li{
    padding:15px;
    cursor:pointer;
}

.sidebar li:hover{
    background:#333;
}

.toolbar{
    height:50px;
    background:#2d2d30;
    display:flex;
    align-items:center;
    gap:10px;
    padding:10px;
}

.toolbar button{
    background:#3a3a3a;
    color:white;
    border:none;
    padding:8px 15px;
    border-radius:6px;
    cursor:pointer;
}

.toolbar button:hover{
    background:#4d4d4d;
}

.file-list{
    flex:1;
    padding:20px;
    display:flex;
    flex-wrap:wrap;
    gap:20px;
    overflow:auto;
}
.file-item.selected {
    background: #0078d7;
}

.file-item{
    width:100px;
    height:100px;
    display:flex;
    flex-direction:column;
    justify-content:center;
    align-items:center;
    border-radius:10px;
    cursor:pointer;
}

.file-item::before{
    content:"📄";
    font-size:40px;
    margin-bottom:8px;
}

.file-item:hover{
    background:#333;
}

.editor{
    width:100%;
    height:100%;
    display:flex;
    flex-direction:column;
}

.editor h3{
    padding:10px;
}

#file-editor{
    flex:1;
    background:#1e1e1e;
    color:white;
    border:none;
    outline:none;
    padding:15px;
    resize:none;
    font-size:15px;
}

.editor-buttons{
    padding:10px;
    display:flex;
    gap:10px;
}

.editor-buttons button{
    background:#3a3a3a;
    color:white;
    border:none;
    padding:8px 15px;
    border-radius:6px;
    cursor:pointer;
}
.modal {
    display:none;
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.5);
    justify-content:center;
    align-items:center;
    z-index:9999;
}

.modal-content {
    width:350px;
    background:#2b2b2b;
    border-radius:10px;
    overflow:hidden;
    box-shadow:0 0 20px rgba(0,0,0,0.5);
}

.modal-header {
    background:#3a3a3a;
    padding:10px 15px;
    font-weight:600;
}

.modal-body {
    padding:20px;
}

.modal-body input {
    width:100%;
    padding:10px;
    background:#1e1e1e;
    color:white;
    border:1px solid #555;
    border-radius:5px;
}

.modal-buttons {
    display:flex;
    justify-content:flex-end;
    gap:10px;
    margin-top:15px;
}

.modal-buttons button {
    background:#0078d7;
    border:none;
    color:white;
    padding:8px 15px;
    border-radius:5px;
    cursor:pointer;
}

.modal-buttons button:hover {
    background:#0a84ff;
}
#status-bar{
    height:30px;
    background:#2b2b2b;
    border-top:1px solid #444;
    display:flex;
    align-items:center;
    padding-left:10px;
    color:#ccc;
    font-size:13px;
}
</style>
</head>
<body>

<div class="filemanager-window">

    <div class="header">
        File Explorer
        <button id="close-btn">×</button>
    </div>

    <div class="body">

    <div class="sidebar">
        <ul>
            <li onclick="loadFiles('home')">🏠 Home</li>
            <li onclick="loadFiles('downloads')">⬇️ Downloads</li>
            <li onclick="loadFiles('desktop')">💻 Desktop</li>
        </ul>
    </div>

    <div style="flex:1;display:flex;flex-direction:column;">

        <div class="toolbar">
            <button onclick="createFile()">📄 New File</button>
            <button onclick="deleteFile()">🗑 Delete</button>
            <button onclick="loadFiles(currentFolder)">🔄 Refresh</button>
        </div>

        <div class="file-list" id="file-list"></div>
          <div id="status-bar">Ready</div>


    </div>

</div>

</div>
<div id="create-file-modal" class="modal">
    <div class="modal-content">

        <div class="modal-header">
            Create New File
        </div>

        <div class="modal-body">

            <input
                type="text"
                id="new-file-name"
                placeholder="Enter file name"
            >

            <div class="modal-buttons">
                <button onclick="submitCreateFile()">Create</button>
                <button onclick="closeCreateFileModal()">Cancel</button>
            </div>
</div>
 

 
      </div>
      
</div>
<script>
let currentFolder = "home";
let currentFile = null;
let selectedFile = null;
function setStatus(message) {
    document.getElementById("status-bar").textContent = message;
}

function loadFiles(folder) {
  currentFolder = folder;
  selectedFile = null;

  fetch(`get_files.php?folder=${folder}`)
    .then((res) => res.json())
    .then((files) => {
      const fileList = document.getElementById("file-list");
      fileList.innerHTML = "";

      if (files.length === 0) {
        fileList.innerHTML = "<p>No files found.</p>";
        return;
      }

      files.forEach((file) => {
        const div = document.createElement("div");
        div.className = "file-item";
        div.textContent = file;

        // Single click = select file
        div.addEventListener("click", () => {
          document.querySelectorAll(".file-item").forEach((item) => {
            item.classList.remove("selected");
          });

          div.classList.add("selected");
          selectedFile = file;
        });

        // Double click = open file
        div.addEventListener("dblclick", () => {
          openFile(file);
        });

        fileList.appendChild(div);
      });
    })
    .catch((err) => {
      console.error("Error loading files:", err);
      alert("Failed to load files");
    });
}

function openFile(filename) {
  currentFile = filename;

  fetch(
    `get_file_content.php?folder=${currentFolder}&filename=${encodeURIComponent(
      filename
    )}`
  )
    .then((res) => res.json())
    .then((data) => {
      if (data.error) {
        setStatus(data.error);
        return;
      }

      showEditor(filename, data.content);
    })
    .catch((err) => {
      console.error("Error loading file content:", err);
      setStatus("Failed to load file content");
    });
}

function showEditor(filename, content) {
  const fileList = document.getElementById("file-list");

  fileList.innerHTML = `
    <div class="editor">
      <h3>${filename}</h3>
      <textarea id="file-editor">${content}</textarea>

      <div class="editor-buttons">
        <button id="save-btn">Save</button>
        <button id="cancel-btn">Cancel</button>
      </div>
    </div>
  `;

  document.getElementById("save-btn").onclick = saveFile;

  document.getElementById("cancel-btn").onclick = () => {
    loadFiles(currentFolder);
  };
}

function saveFile() {
  const newContent = document.getElementById("file-editor").value;

  fetch("save_file_content.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: `folder=${encodeURIComponent(
      currentFolder
    )}&filename=${encodeURIComponent(
      currentFile
    )}&content=${encodeURIComponent(newContent)}`,
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        setStatus("File saved successfully!");
        loadFiles(currentFolder);
      } else {
        setStatus("Error saving file: " + (data.error || "Unknown error"));
      }
    })
    .catch((err) => {
      console.error("Error saving file:", err);
        setStatus("Failed to save file");
    });
}

function createFile() {
    document.getElementById("create-file-modal").style.display = "flex";

    document.getElementById("new-file-name").value = "";

    document.getElementById("new-file-name").focus();
}
function closeCreateFileModal() {
    document.getElementById("create-file-modal").style.display = "none";
}

function submitCreateFile() {

    const filename = document
        .getElementById("new-file-name")
        .value
        .trim();

    if (!filename) return;

    fetch("create_file.php", {
        method: "POST",
        headers: {
            "Content-Type":
                "application/x-www-form-urlencoded",
        },
        body: `folder=${encodeURIComponent(
            currentFolder
        )}&filename=${encodeURIComponent(filename)}`
    })
    .then((res) => res.json())
    .then((data) => {

        if (data.success) {

            closeCreateFileModal();

            loadFiles(currentFolder);

        } else {

            setStatus(data.error || "Failed to create file");

        }

    })
    .catch((err) => {

        console.error(err);

        setStatus("Failed to create file");

    });
}
function deleteFile() {
  if (!selectedFile) {
    setStatus("Please select a file first.");
    return;
  }

  

  fetch("delete_file.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: `folder=${encodeURIComponent(
      currentFolder
    )}&filename=${encodeURIComponent(selectedFile)}`,
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        setStatus("File deleted successfully!");
        selectedFile = null;
        loadFiles(currentFolder);
      } else {
        setStatus(data.error || "Failed to delete file");
      }
    })
    .catch((err) => {
      console.error("Error deleting file:", err);
       
    });
}

window.onload = () => {
  loadFiles("home");
};
document.getElementById("close-btn").addEventListener("click", () => {
    window.parent.postMessage("closeFileManager", "*");
});
document.addEventListener("keydown", (e) => {

    const modal =
        document.getElementById("create-file-modal");

    if (
        modal.style.display === "flex" &&
        e.key === "Enter"
    ) {
        submitCreateFile();
    }
});
</script>

</body>
