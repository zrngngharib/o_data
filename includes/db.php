
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Zain:wght@200;300;400;700;800;900&display=swap" rel="stylesheet">

<!-- Tailwind CSS -->
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.2.4/dist/tailwind.min.css" rel="stylesheet">

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- RTL Support -->
<style>
    body {
        direction: rtl;
        font-family: 'Zain', sans-serif;
    }
</style>
<?php
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$database = "o_data"; 

// چالاککردنی Error Reporting بۆ باشتر کردنەوەی هەڵەکان
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // پەیوەندی بە داتابەیس
    $conn = new mysqli($servername, $username, $password, $database);
    $conn->set_charset("utf8mb4"); // بۆ پشتیوانی نوسین لە کوردی و عەرەبی

} catch (Exception $e) {
    die("کێشە لە پەیوەندی بە داتابەیس: " . $e->getMessage());
}
?>


<!-- Bootstrap Bundle JS (including Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
