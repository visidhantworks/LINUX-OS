<?php
session_start();

$_SESSION = array();

session_destroy();

header("Location: login/index-2.html");
exit();
?>