<?php
session_start();

unset($_SESSION['user']);

header("Location: http://localhost/practic/login.html");
?>