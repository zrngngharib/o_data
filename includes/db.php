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

// دروستکردنی پەیوەندی بە داتابەیس
$conn = new mysqli($servername, $username, $password, $database);

// پشکنینی پەیوەندی
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Timezone setting
date_default_timezone_set('UTC');

