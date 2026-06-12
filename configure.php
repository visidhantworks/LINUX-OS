<?php

define('DB_HOST', 'sql302.infinityfree.com');
define('DB_USER', 'if0_42099223');
define('DB_PASS', 'your_password');
define('DB_NAME', 'if0_42099223_myos');
function getDbConnection() {
    return new mysqli(
        DB_HOST,
        DB_USER,
        DB_PASS,
        DB_NAME
    );
}
?>
