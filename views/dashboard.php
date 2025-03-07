<?php
// dashboard.php
error_reporting(E_ALL);
ini_set('display_errors', 1);


// دەستپێکردنی ئینتەرنێت
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include "includes/db.php"; // پەیوەندی بە داتابەیس

?>
<!DOCTYPE html>
<html lang="ku">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>O_DATA Dashboard</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Zain:wght@200;300;400;700;800;900&display=swap" rel="stylesheet">

    <!-- RTL Support -->
    <style>
        body {
            direction: rtl;
            font-family: 'Zain', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 p-6">

    <?php
    $username = isset($_SESSION['user']) ? $_SESSION['user'] : 'میوان';
    ?>

    <h1 class="text-3xl font-bold text-gray-700 text-center">بەخێربێیت بەڕێز: <?php echo $username; ?></h1>

    <!-- Responsive Cards for Statistics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
        <div class="bg-blue-500 text-white p-6 rounded-lg shadow-lg transform transition duration-500 hover:scale-105">
            <div class="flex items-center space-x-4">
                <span class="text-4xl">📊</span>
                <div>
                    <h2 class="text-xl font-semibold">هەموو کارەکان</h2>
                    <p class="text-3xl font-bold">0</p>
                </div>
            </div>
        </div>
        <div class="bg-green-500 text-white p-6 rounded-lg shadow-lg transform transition duration-500 hover:scale-105">
            <div class="flex items-center space-x-4">
                <span class="text-4xl">✅</span>
                <div>
                    <h2 class="text-xl font-semibold">کارە تەواوبووەکان</h2>
                    <p class="text-3xl font-bold">0</p>
                </div>
            </div>
        </div>
        <div class="bg-yellow-500 text-white p-6 rounded-lg shadow-lg transform transition duration-500 hover:scale-105">
            <div class="flex items-center space-x-4">
                <span class="text-4xl">⏳</span>
                <div>
                    <h2 class="text-xl font-semibold">کارە نا تەواوبووەکان</h2>
                    <p class="text-3xl font-bold">0</p>
                </div>
            </div>
        </div>
        <div class="bg-red-500 text-white p-6 rounded-lg shadow-lg transform transition duration-500 hover:scale-105">
            <div class="flex items-center space-x-4">
                <span class="text-4xl">❗</span>
                <div>
                    <h2 class="text-xl font-semibold">کارە تاخیرکراوەکان</h2>
                    <p class="text-3xl font-bold">0</p>
                </div>
            </div>
        </div>
        <div class="bg-indigo-500 text-white p-6 rounded-lg shadow-lg transform transition duration-500 hover:scale-105">
            <div class="flex items-center space-x-4">
                <span class="text-4xl">🚪</span>
                <div>
                    <h2 class="text-xl font-semibold">داهاتنی بەکارهێنەران</h2>
                    <p class="text-3xl font-bold">0</p>
                </div>
            </div>
        </div>
        <div class="bg-gray-500 text-white p-6 rounded-lg shadow-lg transform transition duration-500 hover:scale-105">
            <div class="flex items-center space-x-4">
                <span class="text-4xl">🛠️</span>
                <div>
                    <h2 class="text-xl font-semibold">ئامێرەکان</h2>
                    <p class="text-3xl font-bold">0</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Buttons -->
    <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-6 gap-4 mt-10">
        <a href="/o_data/views/tasks.php" class="bg-blue-600 text-white p-4 rounded-lg text-center shadow-md transform transition hover:scale-105">
            📌 ئەركەكانی ڕۆژانە
        </a>
        <a href="/o_data/views/devices.php" class="bg-green-600 text-white p-4 rounded-lg text-center shadow-md transform transition hover:scale-105">
            ⚙️ ئامێرەكان
        </a>
        <a href="/o_data/views/users.php" class="bg-purple-600 text-white p-4 rounded-lg text-center shadow-md transform transition hover:scale-105">
            👤 بەكارهێنەران
        </a>
        <a href="/o_data/views/telegram_bot.php" class="bg-yellow-600 text-white p-4 rounded-lg text-center shadow-md transform transition hover:scale-105">
            🤖 بۆتی تیلیگرام
        </a>
        <a href="/o_data/views/daily_check.php" class="bg-indigo-600 text-white p-4 rounded-lg text-center shadow-md transform transition hover:scale-105">
            🏥 پشکنینی ڕۆژانە
        </a>
        <a href="/o_data/views/logout.php" class="bg-red-600 text-white p-4 rounded-lg text-center shadow-md transform transition hover:scale-105">
            🚪 دەرچوون
        </a>
    </div>

</body>
</html>
