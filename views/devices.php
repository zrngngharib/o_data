<?php
session_start();
include_once('../includes/db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="ku">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ئامێرەکان</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Font: Zain -->
    <link href="https://fonts.googleapis.com/css2?family=Zain:ital,wght@0,200;0,300;0,400;0,700;0,800;0,900;1,300;1,400&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Zain', sans-serif;
            direction: rtl;
            text-align: right;
        }
    </style>
</head>
<body class="bg-gray-100">

<!-- Header -->
<div class="bg-blue-600 text-white p-6 md:p-8">
    <h1 class="text-3xl md:text-4xl font-semibold text-center">ئامێرەکان</h1>
</div>

<!-- Main Content -->
<div class="container mx-auto p-6 md:p-8">
    <div class="bg-white shadow-lg rounded-lg p-6 md:p-8">
        <!-- Button Section with Links -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4 mb-6">
            <a href="devices/add_devices.php" class="bg-green-500 text-white py-3 px-6 rounded-lg hover:bg-green-600 transition duration-300 text-center">زیادکردنی ئامێر</a>
            <a href="devices/edit_devices.php" class="bg-blue-500 text-white py-3 px-6 rounded-lg hover:bg-blue-600 transition duration-300 text-center">دەستکاری ئامێر</a>
            <a href="devices/delete_devices.php" class="bg-red-500 text-white py-3 px-6 rounded-lg hover:bg-red-600 transition duration-300 text-center">سڕینەوەی ئامێر</a>
            <a href="devices/view_devices.php" class="bg-yellow-500 text-white py-3 px-6 rounded-lg hover:bg-yellow-600 transition duration-300 text-center">بینینی ئامێرەکان</a>
            <a href="devices/report_devices.php" class="bg-purple-500 text-white py-3 px-6 rounded-lg hover:bg-purple-600 transition duration-300 text-center">ڕاپۆرت</a>
        </div>
    </div>
</div>

</body>
</html>
