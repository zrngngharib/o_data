<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "o_data";

// چالاککردنی Error Reporting بۆ باشتر کردنەوەی هەڵەکان
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // پەیوەندی بە داتابەیس
    $db = new mysqli($servername, $username, $password, $database);
    $db->set_charset("utf8mb4"); // بۆ پشتیوانی نوسین لە کوردی و عەرەبی

} catch (Exception $e) {
    die("کێشە لە پەیوەندی بە داتابەیس: " . $e->getMessage());
}

// Create connection
$conn = mysqli_connect($servername, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>