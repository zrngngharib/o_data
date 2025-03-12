<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

include '../../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

// بەروارەکان
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// فیلترین بەپێی ناوی ئەرک / تیم / کارمەند
$task_name_filter = isset($_GET['task_name']) ? mysqli_real_escape_string($db, $_GET['task_name']) : '';
$team_filter = isset($_GET['team']) ? mysqli_real_escape_string($db, $_GET['team']) : '';
$employee_filter = isset($_GET['employee']) ? mysqli_real_escape_string($db, $_GET['employee']) : '';

// دروستکردنی WHERE Clause
$where_clauses = [];
$where_clauses[] = "date BETWEEN '$start_date' AND '$end_date'";

if (!empty($task_name_filter)) {
    $where_clauses[] = "task_name LIKE '%$task_name_filter%'";
}

if (!empty($team_filter)) {
    $where_clauses[] = "team LIKE '%$team_filter%'";
}

if (!empty($employee_filter)) {
    $where_clauses[] = "employee LIKE '%$employee_filter%'";
}

$where_sql = implode(" AND ", $where_clauses);

// هەموو ئەرکەکان بەپێی فلتەرەکان
$query = "SELECT *, IF(status='Completed', completion_date, NULL) as completion_date FROM tasks WHERE $where_sql";
$result = mysqli_query($db, $query);

// ستاتیستیکی گشتی
$query_count = "SELECT 
    (SELECT COUNT(*) FROM tasks WHERE $where_sql) as total,
    (SELECT COUNT(*) FROM tasks WHERE status='Completed' AND $where_sql) as completed,
    (SELECT COUNT(*) FROM tasks WHERE status='In Progress' AND $where_sql) as in_progress,
    (SELECT COUNT(*) FROM tasks WHERE status='Pending' AND $where_sql) as pending
";
$stats_result = mysqli_query($db, $query_count);
$stats = mysqli_fetch_assoc($stats_result);
?>

<!DOCTYPE html>
<html lang="ku" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>📊 ڕاپۆرتی بەروار</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Table2Excel (Export) -->
 
    <script src="../../js/table2excel.min.js"></script>


    <style>
        @font-face {
            font-family: 'Zain';
            src: url('../../fonts/Zain.ttf');
        }
        body {
            font-family: 'Zain', sans-serif;
            background: linear-gradient(135deg, #dee8ff, #f5f7fa);
        }
        .glass {
            background: rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.1);
        }
        .dashboard-btn {
            background-color: #4F46E5;
            color: #fff;
            padding: 0.5rem 1.5rem;
            border-radius: 1rem;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .dashboard-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.4);
        }
    </style>
</head>

<body class="p-4">

    <div class="glass w-full max-w-7xl mx-auto p-6 mb-6">
        <h1 class="text-3xl font-bold text-indigo-700 mb-4">📊 ڕاپۆرتی بەروار</h1>

        <!-- Date Range Filter Form + Extra Filters -->
        <form action="" method="GET" class="flex flex-wrap gap-4 mb-6">
            <div class="flex flex-col">
                <label>📅 بەرواری دەستپێک</label>
                <input type="date" name="start_date" value="<?= $start_date ?>" class="form-control rounded-lg border-2 border-indigo-300" required>
            </div>
            <div class="flex flex-col">
                <label>📅 بەرواری کۆتایی</label>
                <input type="date" name="end_date" value="<?= $end_date ?>" class="form-control rounded-lg border-2 border-indigo-300" required>
            </div>
            <div class="flex flex-col">
                <label>📝 ناوی ئەرک</label>
                <input type="text" name="task_name" value="<?= htmlspecialchars($task_name_filter) ?>" class="form-control rounded-lg border-2 border-indigo-300">
            </div>
            <div class="flex flex-col">
                <label>👥 تیم</label>
                <input type="text" name="team" value="<?= htmlspecialchars($team_filter) ?>" class="form-control rounded-lg border-2 border-indigo-300">
            </div>
            <div class="flex flex-col">
                <label>👤 کارمەند</label>
                <input type="text" name="employee" value="<?= htmlspecialchars($employee_filter) ?>" class="form-control rounded-lg border-2 border-indigo-300">
            </div>
            <div class="flex items-end">
                <button type="submit" class="dashboard-btn">🔎 فلتەر</button>
            </div>
        </form>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="glass p-6 text-center">
                <h2 class="text-lg font-bold">📌 هەموو ئەرکەکان</h2>
                <p class="text-2xl text-indigo-700"><?= $stats['total'] ?></p>
            </div>
            <div class="glass p-6 text-center">
                <h2 class="text-lg font-bold">✅ تەواوبووەکان</h2>
                <p class="text-2xl text-green-600"><?= $stats['completed'] ?></p>
            </div>
            <div class="glass p-6 text-center">
                <h2 class="text-lg font-bold">🚧 دەستپێکردوەکان</h2>
                <p class="text-2xl text-yellow-500"><?= $stats['in_progress'] ?></p>
            </div>
            <div class="glass p-6 text-center">
                <h2 class="text-lg font-bold">⏳ چاوەڕوانەکان</h2>
                <p class="text-2xl text-red-600"><?= $stats['pending'] ?></p>
            </div>
        </div>
    </div>

    <!-- Filtered Results Table -->
    <div class="glass w-full max-w-7xl mx-auto p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">📋 لیستی ئەرکەکان (فلتەر کراو)</h2>
            <button class="dashboard-btn bg-green-600 hover:bg-green-700">
                ⬇️ دابەزاندنی Excel
            </button>
        </div>

        <div class="overflow-x-auto">
            <table id="reportTable" class="table table-bordered w-full text-center bg-white shadow-lg rounded-lg border-collapse border border-indigo-200">
                <thead class="bg-indigo-600 text-white">
                    <tr>
                        <th>ID</th>
                        <th>ناوی ئەرک</th>
                        <th>ژمارە</th>
                        <th>شوێن</th>
                        <th>کارمەند</th>
                        <th>تیم</th>
                        <th>حاڵەت</th>
                        <th>نرخ</th>
                        <th>دراو</th>
                        <th>بەروار</th>
                        <th>بەرواری تەواوبوون</th>
                        <th>هاوپێچەکان</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['task_name']) ?></td>
                        <td><?= htmlspecialchars($row['task_number']) ?></td>
                        <td><?= htmlspecialchars($row['location']) ?></td>
                        <td><?= htmlspecialchars($row['employee']) ?></td>
                        <td><?= htmlspecialchars($row['team']) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td><?= $row['cost'] ?></td>
                        <td><?= htmlspecialchars($row['currency']) ?></td>
                        <td><?= htmlspecialchars($row['date']) ?></td>
                        <td><?= htmlspecialchars($row['completion_date']) ?></td>
                        <td>
                            <?php
                            $files = explode(',', $row['files']);
                            if (!empty($files[0])) {
                                foreach ($files as $file) {
                                    echo "<a href='../../{$file}' target='_blank' class='text-blue-600 underline'>📂 فایل</a><br>";
                                }
                            } else {
                                echo "❌ هیچ فایلێک نیە";
                            }
                            ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Export Table to Excel -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function exportTableToExcel() {
                let table = document.getElementById('reportTable');
                let table2excel = new Table2Excel();
                table2excel.export([table], { name: "Filtered_Report" });
            }

            document.querySelector('.dashboard-btn.bg-green-600').addEventListener('click', exportTableToExcel);
        });
    </script>

</body>
</html>
