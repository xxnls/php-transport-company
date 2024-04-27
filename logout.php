<?php
session_start();
session_destroy();
header("location: login.html");
// Exits all running scripts
exit();
?>