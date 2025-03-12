<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "o_data";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $db = new mysqli($servername, $username, $password, $database);
    $db->set_charset("utf8mb4");
} catch (Exception $e) {
    die("کێشە لە پەیوەندی بە داتابەیس: " . $e->getMessage());
}

if (!$db) {
    die("Database connection failed: " . mysqli_connect_error());
}

$conn = $db;
date_default_timezone_set('Asia/Baghdad');
?>