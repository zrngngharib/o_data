
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
// filepath: /c:/xampp/htdocs/o_data/index.php
// ڕێچکەی پەڕگە: /c:/xampp/htdocs/o_data/index.php

// تێکەڵکردنی پەیوەندیدانی بنکەی داتا
include 'includes/db.php'; // ڕێڕەوی دروست بۆ db.php

// دەستپێکردنی ئینتەرنێت
session_start();



// Define Routes
$pages = ['daily_check', 'devices', 'tasks', 'telegram_bot', 'users'];

// Load requested page
if (isset($_GET['page']) && in_array($_GET['page'], $pages)) {
    include "views/{$_GET['page']}.php";
} else {
    include 'views/dashboard.php';
}
?>

<!-- Bootstrap Bundle JS (including Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
