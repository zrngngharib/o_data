<?php
session_start();
include '../includes/db.php'; // Include database connection

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

// Fetch completed tasks from the database
$query = "SELECT * FROM tasks WHERE status = 'Completed' ORDER BY date DESC";
$result = mysqli_query($conn, $query);

// Count total completed tasks
$total_completed = mysqli_num_rows($result);
?>
<!DOCTYPE html>
<html lang="ku">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>کارە تەواوبووەکان</title>
    <link href="https://fonts.googleapis.com/css2?family=Zain:wght@200;300;400;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.2.4/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            direction: rtl;
            font-family: 'Zain', sans-serif;
        }
        .container {
            max-width: 1200px;
            margin: auto;
            padding: 20px;
        }
        table th, table td {
            padding: 3px;
        }
        table tr:hover {
            background-color: #f5f5f5;
        }
        .custom-button {
            color: white;
            background-color: rgb(16, 0, 49);
            border-radius: 9999px;
            font-size: 0.875rem;
            padding: 0.625rem 1.25rem;
            margin-bottom: 0.5rem;
            margin-right: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="container text-center">
        <h1>کارە تەواوبووەکان</h1>
        <button class="custom-button" onclick="window.location.href='add_task.php'">➕ زیاد كردن</button>
        <button class="custom-button" onclick="window.location.href='pending_tasks.php'">⏳ کارە چاوەڕوانەکان</button>
        <button class="custom-button" onclick="window.location.href='report.php'">📊 ڕاپۆرتی گشتی</button>
        <br><br>
        <h2>ژمارەی کارە تەواوبووەکان: <?php echo $total_completed; ?></h2>
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">ID</th>
                        <th scope="col" class="px-6 py-3">ئەرك 📋</th>
                        <th scope="col" class="px-6 py-3">ژمارە 🔢</th>
                        <th scope="col" class="px-6 py-3">شوێن 📍</th>
                        <th scope="col" class="px-6 py-3">کارمەند 👤</th>
                        <th scope="col" class="px-6 py-3">ژمارە مۆبایل 📞</th>
                        <th scope="col" class="px-6 py-3">تیم 👥</th>
                        <th scope="col" class="px-6 py-3">نرخ 💰</th>
                        <th scope="col" class="px-6 py-3">بەروار 📅</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr class='odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200'>";
                        echo "<td class='px-6 py-4 whitespace-nowrap'>{$row['id']}</td>";
                        echo "<td class='px-6 py-4 whitespace-nowrap'>{$row['task_name']}</td>";
                        echo "<td class='px-6 py-4 whitespace-nowrap'>{$row['task_number']}</td>";
                        echo "<td class='px-6 py-4 whitespace-nowrap'>{$row['location']}</td>";
                        echo "<td class='px-6 py-4 whitespace-nowrap'>{$row['employee']}</td>";
                        echo "<td class='px-6 py-4 whitespace-nowrap'>{$row['mobile_number']}</td>";
                        echo "<td class='px-6 py-4 whitespace-nowrap'>{$row['team']}</td>";
                        echo "<td class='px-6 py-4 whitespace-nowrap'>{$row['cost']} {$row['currency']}</td>";
                        echo "<td class='px-6 py-4 whitespace-nowrap'>{$row['date']}</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>