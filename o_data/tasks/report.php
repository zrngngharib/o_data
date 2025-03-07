<?php
session_start();
include '../includes/db.php'; // Include database connection

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

// Fetch report data from the database
$query = "SELECT status, COUNT(*) as total FROM tasks GROUP BY status";
$result = mysqli_query($conn, $query);
$report_data = [];

while ($row = mysqli_fetch_assoc($result)) {
    $report_data[$row['status']] = $row['total'];
}

// Prepare data for display
$total_pending = isset($report_data['Pending']) ? $report_data['Pending'] : 0;
$total_in_progress = isset($report_data['In Progress']) ? $report_data['In Progress'] : 0;
$total_completed = isset($report_data['Completed']) ? $report_data['Completed'] : 0;

?>
<!DOCTYPE html>
<html lang="ku">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ڕاپۆرتی کارەکان</title>
    <link rel="stylesheet" href="../styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Zain:wght@200;300;400;700;800;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Zain', sans-serif;
            direction: rtl;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            text-align: center;
            max-width: 800px;
            width: 100%;
        }
        .custom-button {
            color: white;
            background-color: rgb(16, 0, 49);
            border-radius: 9999px;
            font-size: 0.875rem;
            padding: 0.625rem 1.25rem;
            margin: 0.5rem;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>ئەركەكانی ڕۆژانە</h1>
        <button class="custom-button" onclick="window.location.href='add_task.php'">➕ زیاد كردن</button>
        <button class="custom-button" onclick="window.location.href='pending_tasks.php'">⏳ کارە چاوەڕوانەکان</button>
        <button class="custom-button" onclick="window.location.href='completed_tasks.php'">✅ کارە تەواوبووەکان</button>
        <button class="custom-button" onclick="window.location.href='tasks.php'">📋 بەرگرتنی کارەکان</button>
        <h2>ڕاپۆرتی کارەکان</h2>
        <p>چاوەڕوانی: <?php echo $total_pending; ?></p>
        <p>کارکردن بەردەوامە: <?php echo $total_in_progress; ?></p>
        <p>تەواوبووەکان: <?php echo $total_completed; ?></p>
    </div>
</body>
</html>