<?php
session_start();
include '../../includes/db.php';

// Session Protection
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit();
}

// Admin access only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Check if the user is an admin
$user_id = $_SESSION['user_id'];
$query = "SELECT role FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

if ($user['role'] !== 'admin') {
    header("Location: ../../index.php");
    exit();
}

// گرتنی هەموو بەکارهێنەران
$query = "SELECT * FROM users ORDER BY id DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="ku" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Management - O_Data</title>

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
        }
        .glass {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            border-radius: 1.5rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
        }
    </style>
</head>
<body class="min-h-screen p-4">

    <!-- Header -->
    <div class="glass container max-w-5xl mx-auto mt-6">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-4xl font-bold text-indigo-700">بەڕێوەبردنی بەکارهێنەران</h2>
            <a href="users_add.php" class="btn btn-success rounded-pill"> زیادکردنی بەکارهێنەر</a>

            <a href="roles.php" class="btn text-white rounded-pill shadow-md transition-transform hover:scale-105" style="background-color: #4F46E5;">
                 ڕۆڵەکان
            </a>
            <a href="logs.php" class="btn text-white rounded-pill shadow-md transition-transform hover:scale-105" style="background-color: #4F46E5;">
                 تۆمارەکان (Logs)
            </a>
        </div>

        <!-- Users Table -->
        <div class="table-responsive">
            <table class="table table-striped text-center align-middle bg-white rounded shadow-sm">
                <thead class="table-primary">
                    <tr>
                        <th>#</th>
                        <th>ناو</th>
                        <th>ڕۆڵ</th>
                        <th>چالاکیەکان</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($result) > 0):
                        while ($user = mysqli_fetch_assoc($result)):
                    ?>
                    <tr>
                        <td><?= $user['id']; ?></td>
                        <td><?= htmlspecialchars($user['username']); ?></td>
                        <td><?= htmlspecialchars($user['role']); ?></td>
                        <td>
                            <a href="users_edit.php?id=<?= $user['id']; ?>" class="btn btn-warning btn-sm rounded-pill">✏️ دەستکاری</a>
                            <a href="users_delete.php?id=<?= $user['id']; ?>" onclick="return confirm('دڵنیایت؟');" class="btn btn-danger btn-sm rounded-pill">❌ سڕینەوە</a>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr>
                        <td colspan="4">هیچ بەکارهێنەرێک نەدۆزرایەوە!</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Back to Dashboard -->
        <div class="text-center mt-4">
            <a href="../dashboard.php" class="btn btn-secondary rounded-pill">⬅️ گەڕانەوە بۆ داشبۆرد</a>
        </div>
    </div>

</body>
</html>
