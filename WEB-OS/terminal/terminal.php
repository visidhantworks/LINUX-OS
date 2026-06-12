<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../login.html");
    exit();
}

$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terminal</title>
    <link rel="stylesheet" href="terminal.css">
</head>
<body>
    <div class="terminal-window">
        <div class="terminal-header">
            <span>Terminal</span>
             <div class="window-controls">
                <span class="close-button" id="close-terminal">×</span>
            </div>
        </div>
        <div class="terminal-body">
            <div class="terminal-output">
                <p> <span class="user"><?php echo htmlspecialchars($username); ?>@myos:</span>
                <span class="prompt">$</span> ls -l
                </p>
            </div>
             <div class="terminal-input"><span class="user"><?php echo htmlspecialchars($username); ?>@myos: </span>
             <span class="prompt">$</span>
            <input type="text" autofocus>
        </div>
        </div>
    </div>
    
        <script>
        function closeTerminal() {
            document.querySelector(".terminal-window").style.display = "none";
        }
        document.addEventListener("DOMContentLoaded", () => {

            const input = document.querySelector(".terminal-input input");
            const output = document.querySelector(".terminal-output");
            const username = "<?php echo htmlspecialchars($username); ?>";

    document
        .getElementById("close-terminal")
        .addEventListener("click", closeTerminal);

    let currentDir = "~";

    function appendOutput(text) {
    const line = document.createElement("pre");
    line.textContent = text;
    output.appendChild(line);
    output.scrollTop = output.scrollHeight;
}
    input.addEventListener("keydown", (e) => {
        if (e.key === "Enter") {
            const command = input.value.trim();

             appendOutput(
                `${username}@myos:${currentDir} $ ${command}`
            );

            handleCommand(command);

            input.value = "";
        }
    });

    function handleCommand(command) {

        if (command === "exit") {
            closeTerminal();
            return;
        }

        fetch("terminal_api.php", {
            method: "POST",
            headers: {
                "Content-Type":
                    "application/x-www-form-urlencoded"
            },
            body:
                `command=${encodeURIComponent(command)}`
        })
        .then(res => res.json())
        .then(data => {

    if (data.currentDir) {
        currentDir = data.currentDir;
    }

    if (data.output) {
        appendOutput(data.output);
    }

})
        .catch(error => {
            console.error(error);
            appendOutput("Terminal error");
        });
    }

});
</script>

    
 

</body>
</html>
