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

// فلتەرکردنی بەروار
$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : '';
$to_date = isset($_GET['to_date']) ? $_GET['to_date'] : '';

$query = "SELECT * FROM tasks WHERE status = 'Completed'";

if (!empty($from_date) && !empty($to_date)) {
    $query .= " AND date BETWEEN '$from_date' AND '$to_date'";
}

$query .= " ORDER BY date DESC"; // ڕیزبەندی نوێترین بۆ کۆنترین

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
    <title>کارە تەواوبووەکان ✅</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <h1>کارە تەواوبووەکان ✅</h1>
    <form method="GET" action="">
        <label>بەروار لە 📅:</label>
        <input type="date" name="from_date" value="<?= htmlspecialchars($from_date) ?>">
        <label>بەروار بۆ 📅:</label>
        <input type="date" name="to_date" value="<?= htmlspecialchars($to_date) ?>">
        <button type="submit">فلتەرکردن 🔍</button>
        <button type="button" onclick="window.location.href='completed_tasks.php'">هەڵوەشاندنەوەی فلتەر 🔄</button>
    </form>
    <br>
    <form>
        <table border="1">
            <tr>
                <th>ID </th>
                <th>ئەرك 📋</th>
                <th>ژمارە 🔢</th>
                <th>شوێن 📍</th>
                <th>کارمەند 👤</th>
                <th>ژمارە مۆبایل 📞</th>
                <th>تیم 👥</th>
                <th>حاڵەت 📊</th>
                <th>نرخ 💰</th>
                <th>بەروار 📅</th>
                <th>بەرواری تەواو کردن 📅</th>
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
                    <td><?= htmlspecialchars($row['completion_date']) ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </form>
    <br>
    <button onclick="window.location.href='export_completed_tasks.php?from_date=<?= htmlspecialchars($from_date) ?>&to_date=<?= htmlspecialchars($to_date) ?>'">ئێکسپۆرتکردن بۆ ئێکسڵ 📤</button>
</body>
</html>