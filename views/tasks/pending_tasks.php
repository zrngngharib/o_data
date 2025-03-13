<?php
session_start();
include '../../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

// ڕوونکردنەوەی هەڵەکان
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ڕیزبەندی بەپێی بەروار
$order = isset($_GET['order']) && $_GET['order'] == 'asc' ? 'ASC' : 'DESC';
$query = "SELECT * FROM tasks WHERE status IN ('Pending', 'In Progress') ORDER BY date $order";
$result = mysqli_query($conn, $query);
if (!$result) {
    die("کێشە لە ناردنی داتا: " . mysqli_error($conn));
}
?>


<!DOCTYPE html>
<html lang="ku" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>⏳ کارە چاوەڕوانەکان</title>

    <!-- TailwindCSS + Bootstrap RTL -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- FontAwesome Icons -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>

    <!-- Custom Fonts -->
    <style>
        @font-face {
            font-family: 'Zain';
            src: url('../fonts/Zain.ttf');
        }

        body {
            font-family: 'Zain', sans-serif;
            background: linear-gradient(135deg, #dee8ff, #f5f7fa);
            color: #333;
        }

        .glass {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.1);
            transition: all 0.4s ease;
        }

        .glass:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px rgba(31, 38, 135, 0.15);
        }

        .btn-custom {
            background-color: #4F46E5;
            color: #fff;
            padding: 0.6rem 1.4rem;
            border-radius: 1.5rem;
            transition: 0.3s;
            font-weight: bold;
        }

        .btn-custom:hover {
            background-color: #6366F1;
            transform: scale(1.05);
            box-shadow: 0 8px 15px rgba(79, 70, 229, 0.4);
        }

        .table-container {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        .task-table th {
            background-color: #4F46E5;
            color: #fff;
            text-align: center;
            padding: 12px;
        }

        .task-table td {
            text-align: center;
            padding: 12px;
        }

        .task-table tr:hover {
            background-color: #f1f5f9;
        }

        .status {
            display: inline-block;
            padding: 5px 14px;
            font-size: 12px;
            font-weight: bold;
            border-radius: 25px;
            color: #fff;
        }

        .pending {
            background-color: #FBBF24;
        }

        .in-progress {
            background-color: #3B82F6;
        }

        .completed {
            background-color: #10B981;
        }

        .dropdown-menu {
            min-width: 160px;
            border-radius: 8px;
            box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .dropdown-item {
            padding: 10px 16px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .dropdown-item:hover {
            background-color: #f1f1f1;
        }

        .hidden {
            display: none;
        }
    </style>
</head>

<body class="p-4">
    <header class="glass max-w-7xl mx-auto mb-6 flex justify-between items-center p-4">
        <h1 class="text-3xl font-bold text-indigo-700">⏳ کارە چاوەڕوانەکان</h1>
        <div class="flex gap-3 items-center">
            <span>👤 <?= htmlspecialchars($username); ?></span>
            <a href="../../ctrluser/logout.php" class="btn btn-danger">🚪 دەرچوون</a>
        </div>
    </header>

    <!-- Table -->
    <div class="glass max-w-7xl mx-auto p-4 overflow-x-auto animate-zoom-in table-container">
        <table class="table task-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>ئەرك </th>
                    <th>ژمارە </th>
                    <th>شوێن </th>
                    <th>کارمەند </th>
                    <th>تیم </th>
                    <th>حاڵەت </th>
                    <th>بەروار </th>
                    <th>تێپەڕبوون</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) {
                    $task_date = new DateTime($row['date']);
                    $current_date = new DateTime();
                    $interval = $current_date->diff($task_date);
                    $days_passed = $interval->days;
                    
                    // Check if the date is valid and days_passed is calculated correctly
                    if ($task_date && $current_date && $interval) {
                        $days_passed = $interval->days;
                    } else {
                        $days_passed = 'N/A'; // Set a default value if there's an issue
                    }
                ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['task_name']) ?></td>
                        <td><?= htmlspecialchars($row['task_number']) ?></td>
                        <td><?= htmlspecialchars($row['location']) ?></td>
                        <td><?= htmlspecialchars($row['employee']) ?></td>
                        <td><?= htmlspecialchars($row['team']) ?></td>
                        <td>
                            <span class="status <?= $row['status'] == 'Pending' ? 'pending' : ($row['status'] == 'In Progress' ? 'in-progress' : 'completed') ?>">
                                <?= htmlspecialchars($row['status']) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($row['date']) ?></td>
                        <td><?= $days_passed ?> ڕۆژ</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- Floating Add Button -->
    <div class="fab fixed bottom-4 left-4 bg-blue-700 text-white text-xl p-4 rounded-full shadow-lg hover:bg-blue-800 transition" onclick="window.location.href='add_task.php'">
        ➕
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Scripts -->
    <script>
        function toggleDropdown() {
            const dropdown = document.getElementById("dropdownMenu");
            dropdown.classList.toggle("hidden");
        }

        function sortTable(order) {
            const url = new URL(window.location.href);
            url.searchParams.set('order', order);
            window.location.href = url.toString();
        }

        // Close dropdown if clicked outside
        window.onclick = function(event) {
            if (!event.target.matches('.btn-custom')) {
                const dropdowns = document.getElementsByClassName("dropdown-menu");
                for (let i = 0; i < dropdowns.length; i++) {
                    const openDropdown = dropdowns[i];
                    if (!openDropdown.classList.contains('hidden')) {
                        openDropdown.classList.add('hidden');
                    }
                }
            }
        }
    </script>

    <!-- Animations -->
    <style>
        .animate-fade-in {
            animation: fadeIn 0.8s ease-in-out;
        }

        .animate-slide-in {
            animation: slideIn 0.8s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes zoomIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
    </style>
</body>
</html>
