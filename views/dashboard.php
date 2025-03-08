<?php
// dashboard.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// دەستپێکردنی ئینتەرنێت
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Use absolute path for including db.php
include __DIR__ . '/../includes/db.php'; // پەیوەندی بە داتابەیس

// Check database connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch counts from the database
$all_tasks_count = 0;
$completed_tasks_count = 0;
$pending_tasks_count = 0;
$in_progress_tasks_count = 0;
$new_users_count = 0;
$devices_count = 0;

$query = "SELECT 
    (SELECT COUNT(*) FROM tasks) AS all_tasks,
    (SELECT COUNT(*) FROM tasks WHERE status = 'completed') AS completed_tasks,
    (SELECT COUNT(*) FROM tasks WHERE status = 'pending') AS pending_tasks,
    (SELECT COUNT(*) FROM tasks WHERE status = 'in_progress') AS in_progress_tasks,
    (SELECT COUNT(*) FROM users WHERE created_at >= CURDATE()) AS new_users,
    (SELECT COUNT(*) FROM devices) AS devices";

$result = mysqli_query($conn, $query);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $all_tasks_count = $row['all_tasks'];
    $completed_tasks_count = $row['completed_tasks'];
    $pending_tasks_count = $row['pending_tasks'];
    $in_progress_tasks_count = $row['in_progress_tasks'];
    $new_users_count = $row['new_users'];
    $devices_count = $row['devices'];
} else {
    die("Query failed: " . mysqli_error($conn));
}

if (!$row) {
    die("No data found");
}
// Fetch in-progress tasks from the database
$in_progress_tasks = [];
$query_in_progress = "SELECT * FROM tasks WHERE status = 'in_progress'";
$result_in_progress = mysqli_query($conn, $query_in_progress);
if ($result_in_progress) {
    while ($row = mysqli_fetch_assoc($result_in_progress)) {
        $in_progress_tasks[] = $row;
    }
}
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

    <!-- Navigation Buttons -->
    <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-6 gap-4 mt-10">
        <a href="/o_data/views/tasks.php" class="bg-white border-2 border-purple-600 text-black p-4 rounded-lg text-center shadow-md transform transition hover:scale-105">
            📌 ئەركەكانی ڕۆژانە
        </a>
        <a href="/o_data/views/devices.php" class="bg-white border-2 border-purple-600 text-black p-4 rounded-lg text-center shadow-md transform transition hover:scale-105">
            ⚙️ ئامێرەكان
        </a>
        <a href="/o_data/views/users.php" class="bg-white border-2 border-purple-600 text-black p-4 rounded-lg text-center shadow-md transform transition hover:scale-105">
            👤 بەكارهێنەران
        </a>
        <a href="/o_data/views/telegram_bot.php" class="bg-white border-2 border-purple-600 text-black p-4 rounded-lg text-center shadow-md transform transition hover:scale-105">
            🤖 بۆتی تیلیگرام
        </a>
        <a href="/o_data/views/daily_check.php" class="bg-white border-2 border-purple-600 text-black p-4 rounded-lg text-center shadow-md transform transition hover:scale-105">
            🏥 پشکنینی ڕۆژانە
        </a>
        <a href="/o_data/views/logout.php" class="bg-white border-2 border-purple-600 text-black p-4 rounded-lg text-center shadow-md transform transition hover:scale-105">
            🚪 دەرچوون
        </a>
    </div>
    
    <!-- Responsive Cards for Statistics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
        <div class="bg-blue-500 text-white p-6 rounded-lg shadow-lg transform transition duration-500 hover:scale-105">
            <div class="flex items-center space-x-4">
                <span class="text-4xl">📊</span>
                <div>
                    <h2 class="text-xl font-semibold">هەموو کارەکان</h2>
                    <p class="text-3xl font-bold"><?php echo $all_tasks_count; ?></p>
                </div>
            </div>
        </div>
        <div class="bg-green-500 text-white p-6 rounded-lg shadow-lg transform transition duration-500 hover:scale-105">
            <div class="flex items-center space-x-4">
                <span class="text-4xl">✅</span>
                <div>
                    <h2 class="text-xl font-semibold">کارە تەواوبووەکان</h2>
                    <p class="text-3xl font-bold"><?php echo $completed_tasks_count; ?></p>
                </div>
            </div>
        </div>
        <div class="bg-yellow-500 text-white p-6 rounded-lg shadow-lg transform transition duration-500 hover:scale-105">
            <div class="flex items-center space-x-4">
                <span class="text-4xl">⏳</span>
                <div>
                    <h2 class="text-xl font-semibold">کارە دەستپێکردوەکان </h2>
                    <p class="text-3xl font-bold"><?php echo $in_progress_tasks_count; ?></p>
                </div>
            </div>
        </div>
        <div class="bg-red-500 text-white p-6 rounded-lg shadow-lg transform transition duration-500 hover:scale-105">
            <div class="flex items-center space-x-4">
                <span class="text-4xl">❗</span>
                <div>
                    <h2 class="text-xl font-semibold"> چاوەڕوانی</h2>
                    <p class="text-3xl font-bold"><?php echo $pending_tasks_count; ?></p>
                </div>
            </div>
        </div>
        <div class="bg-indigo-500 text-white p-6 rounded-lg shadow-lg transform transition duration-500 hover:scale-105">
            <div class="flex items-center space-x-4">
                <span class="text-4xl">🚪</span>
                <div>
                    <h2 class="text-xl font-semibold">داهاتنی بەکارهێنەران</h2>
                    <p class="text-3xl font-bold"><?php echo $new_users_count; ?></p>
                </div>
            </div>
        </div>
        <div class="bg-gray-500 text-white p-6 rounded-lg shadow-lg transform transition duration-500 hover:scale-105">
            <div class="flex items-center space-x-4">
                <span class="text-4xl">🛠️</span>
                <div>
                    <h2 class="text-xl font-semibold">ئامێرەکان</h2>
                    <p class="text-3xl font-bold"><?php echo $devices_count; ?></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

