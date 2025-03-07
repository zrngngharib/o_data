<?php
session_start();
include '../../includes/db.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

// ڕوونکردنەوەی هەڵەکان
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$query = "SELECT * FROM tasks WHERE status IN ('Pending', 'In Progress')";
$result = mysqli_query($conn, $query);
if (!$result) {
    die("کێشە لە ناردنی داتا: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="ku">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>کارە چاوەڕوانەکان ⏳</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <h1>کارە چاوەڕوانەکان ⏳</h1>
    <form>
        <table border="1">
            <tr>
                <th>ID</th>
                <th>ئەرك 📝</th>
                <th>ژمارە 🔢</th>
                <th>شوێن 📍</th>
                <th>کارمەند 👤</th>
                <th>ژمارە مۆبایل 📱</th>
                <th>تیم 👥</th>
                <th>حاڵەت 📊</th>
                <th>نرخ 💰</th>
                <th>بەروار 📅</th>
                <th>کردار ⚙️</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['task_name']) ?></td>
                    <td><?= htmlspecialchars($row['task_number']) ?></td>
                    <td><?= htmlspecialchars($row['location']) ?></td>
                    <td><?= htmlspecialchars($row['employee']) ?></td>
                    <td><?= htmlspecialchars($row['mobile_number']) ?></td>
                    <td><?= htmlspecialchars($row['team']) ?></td>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                    <td><?= htmlspecialchars($row['cost']) ?> <?= htmlspecialchars($row['currency']) ?></td>
                    <td><?= htmlspecialchars($row['date']) ?></td>
                    <td>
                        <a href="complete_task.php?id=<?= $row['id'] ?>">تەواوکردن</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </form>
</body>
</html>