<?php
session_start();
include_once('../includes/db.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);


if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$username = $_SESSION['username'];

// Check active user
$user_id = intval($_SESSION['user_id']);
$user_query = mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id AND status = 'active'");

if (mysqli_num_rows($user_query) == 0) {
    session_destroy();
    echo "<script>alert('❌ ئەکاونتەکەت ناچالاکە!'); window.location.href='../index.php';</script>";
    exit();
}

// Global Stats
$query = "SELECT 
    (SELECT COUNT(*) FROM tasks) AS all_tasks,
    (SELECT COUNT(*) FROM tasks WHERE status = 'تەواوکراوە') AS completed_tasks,
    (SELECT COUNT(*) FROM tasks WHERE status = 'چاوەڕوانی') AS pending_tasks,
    (SELECT COUNT(*) FROM users) AS total_users,
    (SELECT COUNT(*) FROM devices) AS devices";

$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

$all_tasks_count = $row['all_tasks'];
$completed_tasks_count = $row['completed_tasks'];
$pending_tasks_count = $row['pending_tasks'];
$total_users_count = $row['total_users'];
$devices_count = $row['devices'];

// Daily Completed Tasks
$dailyTasksQuery = mysqli_query($conn, "
    SELECT DATE(completion_date) AS day, COUNT(*) AS count 
    FROM tasks 
    WHERE status = 'تەواوکراوە' 
    GROUP BY day 
    ORDER BY day ASC
");

$dailyTasks = [];
while ($r = mysqli_fetch_assoc($dailyTasksQuery)) {
    $dailyTasks[$r['day']] = $r['count'];
}

// Monthly Completed Tasks
$monthlyTasksQuery = mysqli_query($conn, "
    SELECT DATE_FORMAT(completion_date, '%Y-%m') AS month, COUNT(*) AS count 
    FROM tasks 
    WHERE status = 'تەواوکراوە' 
    GROUP BY month 
    ORDER BY month ASC
");

$monthlyTasks = [];
while ($r = mysqli_fetch_assoc($monthlyTasksQuery)) {
    $monthlyTasks[$r['month']] = $r['count'];
}

?>
<!DOCTYPE html>
<html lang="ku" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>داشبۆرد - O_Data</title>

    <!-- Bootstrap RTL & TailwindCSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Chart.js & Animate.css -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"/>

    <!-- فۆنتی Zain -->
    <style>
        @font-face {
            font-family: 'Zain';
            src: url('../fonts/Zain.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        body, h1, h2, h3, h4, h5, h6, p, button, a, span, div {
            font-family: 'Zain', sans-serif !important;
        }

        body {
            background: linear-gradient(135deg, #dee8ff, #f5f7fa);
            direction: rtl;
            text-align: right;
        }

        .glass {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 1.5rem;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.2);
            padding: 1.5rem;
        }

        .dashboard-btn {
            background-color: #4F46E5;
            color: #fff;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .dashboard-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.4);
        }

        @media (max-width: 768px) {
            .btn-group {
                flex-direction: column;
            }
        }
    </style>
</head>
<body class="flex flex-col min-h-screen p-4">

    <!-- Header -->
    <header class="glass max-w-7xl w-full mx-auto mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-indigo-700 animate-pulse"><i class="fas fa-chart-line"></i> داشبۆرد</h1>
        <div class="flex gap-3 items-center">
            <span class="text-sm text-gray-700"><i class="fas fa-user"></i> <?= htmlspecialchars($username); ?></span>
            <a href="../views/ctrluser/logout.php" class="btn btn-danger text-white rounded-pill"><i class="fas fa-sign-out-alt"></i> دەرچوون</a>
        </div>
    </header>

    <!-- Welcome -->
    <section class="glass max-w-7xl w-full mx-auto mb-6 text-center space-y-3">
        <h2 class="text-2xl font-bold text-indigo-600 animate-pulse"><i class="fas fa-hand-sparkles"></i> بەخێربێیت، <?= htmlspecialchars($username); ?></h2>
    </section>

    <!-- Navigation Buttons -->
    <div class="glass flex flex-wrap justify-center gap-4 max-w-7xl w-full mx-auto mb-6 btn-group">
        <a href="/o_data/views/tasks.php" class="dashboard-btn px-6 py-3 rounded-pill text-center"><i class="fas fa-thumbtack"></i> ئەركەكانی ڕۆژانە</a>
        <a href="/o_data/views/devices.php" class="dashboard-btn px-6 py-3 rounded-pill text-center"><i class="fas fa-cogs"></i> ئامێرەكان</a>
        <a href="/o_data/views/ctrluser/users.php" class="dashboard-btn px-6 py-3 rounded-pill text-center"><i class="fas fa-users"></i> بەكارهێنەران</a>
        <a href="/o_data/views/telegram_bot.php" class="dashboard-btn px-6 py-3 rounded-pill text-center"><i class="fas fa-robot"></i> بۆتی تیلیگرام</a>
        <a href="/o_data/views/daily_check.php" class="dashboard-btn px-6 py-3 rounded-pill text-center"><i class="fas fa-stethoscope"></i> پشکنینی ڕۆژانە</a>
    </div>

    <!-- Stats -->
    <section class="glass max-w-7xl w-full mx-auto mt-10 flex flex-wrap justify-center gap-6 text-center">
        <div class="flex flex-col items-center justify-center space-y-2 w-48 p-4 glass animate__animated animate__fadeInUp">
            <div class="text-4xl"><i class="fas fa-tasks"></i></div>
            <h3 class="text-lg font-bold">هەموو کارەکان</h3>
            <p class="text-3xl font-bold"><?= $all_tasks_count; ?></p>
        </div>

        <div class="flex flex-col items-center justify-center space-y-2 w-48 p-4 glass animate__animated animate__fadeInUp">
            <div class="text-4xl"><i class="fas fa-check-circle"></i></div>
            <h3 class="text-lg font-bold">کارە تەواوبووەکان</h3>
            <p class="text-3xl font-bold"><?= $completed_tasks_count; ?></p>
        </div>

        <div class="flex flex-col items-center justify-center space-y-2 w-48 p-4 glass animate__animated animate__fadeInUp">
            <div class="text-4xl"><i class="fas fa-hourglass-half"></i></div>
            <h3 class="text-lg font-bold">چاوەڕوانی</h3>
            <p class="text-3xl font-bold"><?= $pending_tasks_count; ?></p>
        </div>

        <div class="flex flex-col items-center justify-center space-y-2 w-48 p-4 glass animate__animated animate__fadeInUp">
            <div class="text-4xl"><i class="fas fa-users"></i></div>
            <h3 class="text-lg font-bold"> بەکارهێنەران</h3>
            <p class="text-3xl font-bold"><?= $total_users_count; ?></p>
        </div>

        <div class="flex flex-col items-center justify-center space-y-2 w-48 p-4 glass animate__animated animate__fadeInUp">
            <div class="text-4xl"><i class="fas fa-cogs"></i></div>
            <h3 class="text-lg font-bold">ئامێرەکان</h3>
            <p class="text-3xl font-bold"><?= $devices_count; ?></p>
        </div>
    </section>

    <!-- Reports Section (Charts) -->
    <section class="glass max-w-7xl w-full mx-auto mt-10 p-6 text-center">
        <h2 class="text-xl font-bold text-indigo-600 mb-6"><i class="fas fa-chart-bar"></i> ڕاپۆرتەکان</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Daily Tasks Bar Chart -->
            <div class="bg-white rounded-2xl p-4 shadow-md">
                <h3 class="text-lg font-bold mb-3"><i class="fas fa-calendar-day"></i> ڕاپۆرتی ڕۆژانە</h3>
                <canvas id="dailyCompletedTasksChart"></canvas>
            </div>
            
            <!-- Monthly Tasks Bar Chart -->
            <div class="bg-white rounded-2xl p-4 shadow-md">
                <h3 class="text-lg font-bold mb-3"><i class="fas fa-calendar-alt"></i> ڕاپۆرتی مانگانە</h3>
                <canvas id="monthlyCompletedTasksChart"></canvas>
            </div>
        </div>
    </section>

    <!-- Chart.js Config -->
    <script>
        const dailyLabels = <?= json_encode(array_keys($dailyTasks)) ?>;
        const dailyData = <?= json_encode(array_values($dailyTasks)) ?>;

        const monthlyLabels = <?= json_encode(array_keys($monthlyTasks)) ?>;
        const monthlyData = <?= json_encode(array_values($monthlyTasks)) ?>;

        // Daily Bar Chart
        new Chart(document.getElementById('dailyCompletedTasksChart'), {
            type: 'bar',
            data: {
                labels: dailyLabels,
                datasets: [{
                    label: 'کارە تەواوبووەکان بە ڕۆژ',
                    data: dailyData,
                    backgroundColor: '#4F46E5',
                    borderRadius: 10
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'ڕاپۆرتی ڕۆژانە - کارە تەواوبووەکان',
                        font: { family: 'Zain', size: 16 }
                    },
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { font: { family: 'Zain' } }
                    },
                    x: {
                        ticks: { font: { family: 'Zain' } }
                    }
                }
            }
        });

        // Monthly Bar Chart
        new Chart(document.getElementById('monthlyCompletedTasksChart'), {
            type: 'bar',
            data: {
                labels: monthlyLabels,
                datasets: [{
                    label: 'کارە تەواوبووەکان بە مانگ',
                    data: monthlyData,
                    backgroundColor: '#10B981',
                    borderRadius: 10
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'ڕاپۆرتی مانگانە - کارە تەواوبووەکان',
                        font: { family: 'Zain', size: 16 }
                    },
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { font: { family: 'Zain' } }
                    },
                    x: {
                        ticks: { font: { family: 'Zain' } }
                    }
                }
            }
        });
    </script>

    <!-- Footer -->
    <footer class="text-center mt-12 text-gray-600 text-sm">
        &copy; <?= date('Y'); ?> O_Data - هەموو مافەکان پارێزراون
    </footer>

</body>
</html>