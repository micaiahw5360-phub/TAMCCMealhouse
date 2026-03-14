<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = "localhost";
$user = "root";
$password = "";
$database = "tamccdeli";

$conn = new mysqli($host, $user, $password, $database);
$conn->set_charset("utf8mb4");
?>