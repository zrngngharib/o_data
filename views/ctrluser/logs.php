<?php
include_once('../../includes/db.php');
session_start();

// Admin access only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Build query with filters
$where = "WHERE 1=1";
if (!empty($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']);
    $where .= " AND user_id = $user_id";
}
if (!empty($_GET['action'])) {
    $action = mysqli_real_escape_string($conn, $_GET['action']);
    $where .= " AND action LIKE '%$action%'";
}
if (!empty($_GET['from_date'])) {
    $from_date = $_GET['from_date'];
    $where .= " AND DATE(created_at) >= '$from_date'";
}
if (!empty($_GET['to_date'])) {
    $to_date = $_GET['to_date'];
    $where .= " AND DATE(created_at) <= '$to_date'";
}

$log_query = "SELECT * FROM user_activity_log $where ORDER BY created_at DESC";
$log_result = mysqli_query($conn, $log_query);
?>

<!DOCTYPE html>
<html lang="ku" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تۆمارەکان - O_Data</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        @font-face {
            font-family: 'Zain';
            src: url('../../fonts/Zain.ttf') format('truetype');
        }
        body {
            font-family: 'Zain', sans-serif;
            background-color: #dee8ff;
            background-image: linear-gradient(135deg, #dee8ff, #f5f7fa);
        }
        .glass {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 2rem;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.2);
            padding: 2rem;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">

    <div class="glass max-w-3xl w-full animate-fade-in space-y-6">

        <div class="text-center">
            <h1 class="text-3xl font-bold text-indigo-700 animate-pulse">📋 تۆمارەکان</h1>
            <p class="text-sm mt-2 text-gray-700">لیستی هەموو چالاکیەکان</p>
        </div>

        <div class="table-responsive">
            <table class="table table-striped text-center align-middle bg-white rounded shadow-sm">
                <thead class="table-primary">
                    <tr>
                        <th>#</th>
                        <th>ناوی بەکارهێنەر</th>
                        <th>چالاکی</th>
                        <th>کات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $counter = 1;
                    while ($log = mysqli_fetch_assoc($log_result)) {
                        // Get username
                        $user_id = $log['user_id'];
                        $username_query = mysqli_query($conn, "SELECT username FROM users WHERE id = $user_id");
                        $username_row = mysqli_fetch_assoc($username_query);
                        $username = $username_row['username'];

                        echo "<tr>";
                        echo "<td>".$counter++."</td>";
                        echo "<td>".htmlspecialchars($username)."</td>";
                        echo "<td>".htmlspecialchars($log['action'])."</td>";
                        echo "<td>".$log['created_at']."</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="text-center mt-4 flex justify-center gap-2">
            <a href="users.php" class="btn btn-secondary rounded-pill">⬅️ گەڕانەوە بۆ بەکارهێنەران</a>
        </div>

    </div>

</body>
</html>