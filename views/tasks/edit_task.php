<?php
session_start();
include '../../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "<script>alert('❌ ئەركەکە نەدۆزرایەوە!'); window.location.href='pending_tasks.php';</script>";
    exit();
}

$task_id = intval($_GET['id']);

// Fetch task
$query = "SELECT * FROM tasks WHERE id = $task_id";
$result = mysqli_query($conn, $query);
$task = mysqli_fetch_assoc($result);

if (!$task) {
    echo "<script>alert('❌ ئەركەکە بوونی نیە!'); window.location.href='pending_tasks.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_name = mysqli_real_escape_string($conn, $_POST['task_name']);
    $task_number = mysqli_real_escape_string($conn, $_POST['task_number']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $employee = mysqli_real_escape_string($conn, $_POST['employee']);
    $mobile_number = mysqli_real_escape_string($conn, $_POST['mobile_number']);
    $team = mysqli_real_escape_string($conn, $_POST['team']);
    $status = $task['status']; // Keep the original status
    $date = $task['date']; // Keep the original date
    $cost = $task['cost']; // Keep the original cost
    $completion_date = ($status === 'Completed') ? "'" . date('Y-m-d') . "'" : 'NULL';

    $update = "UPDATE tasks SET 
        task_name='$task_name',
        task_number='$task_number',
        location='$location',
        employee='$employee',
        mobile_number='$mobile_number',
        team='$team',
        cost='$cost',
        status='$status',
        date='$date',
        completion_date=$completion_date
        WHERE id=$task_id";

    if (mysqli_query($conn, $update)) {
        if ($status === 'Completed') {
            echo "<script>alert('✅ ئەرك نوێکرایەوە!'); window.location.href='completed_tasks.php';</script>";
        } else {
            echo "<script>alert('✅ ئەرك نوێکرایەوە!'); window.location.href='pending_tasks.php';</script>";
        }
        exit();
    } else {
        echo "<script>alert('❌ هەڵە لە نوێکردنەوە!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="ku" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>✏️ دەستکاریکردنی ئەرك</title>

    <!-- Tailwind + Bootstrap + Font -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @font-face {
            font-family: 'Zain';
            src: url('../../fonts/Zain.ttf');
        }
        body {
            font-family: 'Zain', sans-serif !important;
            background: linear-gradient(135deg, #dee8ff, #f5f7fa);
        }
        .glass {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.1);
        }
        .dashboard-btn {
            background-color: #4F46E5;
            color: white;
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

<body class="flex items-center justify-center min-h-screen p-4">

    <!-- Edit Task Form -->
    <div class="glass w-full max-w-xl p-6">
        <h2 class="text-center text-2xl font-bold text-indigo-700 mb-6">✏️ دەستکاریکردنی ئەرك</h2>

        <form method="POST" class="space-y-4">
            <div>
                <label class="form-label">📝 ناوی ئەرك</label>
                <input type="text" name="task_name" value="<?= htmlspecialchars($task['task_name']) ?>" class="form-control" required>
            </div>
            <div>
                <label class="form-label">📄 ژمارەی ئەرك</label>
                <input type="text" name="task_number" value="<?= htmlspecialchars($task['task_number']) ?>" class="form-control">
            </div>
            <div>
                <label class="form-label">📍 شوێن</label>
                <input type="text" name="location" value="<?= htmlspecialchars($task['location']) ?>" class="form-control">
            </div>
            <div>
                <label class="form-label">👤 کارمەند</label>
                <input type="text" name="employee" value="<?= htmlspecialchars($task['employee']) ?>" class="form-control">
            </div>
            <div>
                <label class="form-label">📱 ژمارەی مۆبایل</label>
                <input type="text" name="mobile_number" value="<?= htmlspecialchars($task['mobile_number']) ?>" class="form-control">
            </div>
            <div>
                <label class="form-label">👥 تیم</label>
                <select name="team" class="form-select" required>
                    <option value="تەکنیکی" <?= $task['team'] == 'تەکنیکی' ? 'selected' : '' ?>>تەکنیکی</option>
                    <option value="دەرەکی" <?= $task['team'] == 'دەرەکی' ? 'selected' : '' ?>>دەرەکی</option>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-4 justify-center mt-6">
                <button type="submit" class="dashboard-btn">💾 نوێکردنەوە</button>
                <a href="pending_tasks.php" class="dashboard-btn bg-red-500 hover:bg-red-600">⬅️ گەڕانەوە</a>
            </div>
        </form>
    </div>

</body>
</html>
