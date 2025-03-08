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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            direction: rtl;
            font-family: 'Zain', sans-serif;
            background-color: #f9fafb;
        }
        .container {
            width: 100%;
            margin: auto;
            text-align: center;
        }
        h1 {
            color: #007bff;
        }
        .filter-form {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            direction: rtl;
        }
        th, td {
            padding: 12px;
            text-align: right;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #007bff;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .btn {
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            margin: 5px;
            font-size: 16px;
        }
        .btn-filter {
            background-color: #0d6efd;
            color: white;
        }
        .btn-reset {
            background-color: #dc3545;
            color: white;
        }
        .btn-export {
            background-color: #0d6efd;
            color: white;
        }
        .btn:hover {
            opacity: 0.8;
        }
        .fab {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #0d6efd;
            color: white;
            padding: 15px;
            border-radius: 50%;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            font-size: 24px;
        }
        .fab:hover {
            background: #0d6efd;
        }
        @media screen and (max-width: 768px) {
            table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h1>کارە تەواوبووەکان ✅</h1>

    <form method="GET" action="" class="filter-form">
        <label>بەروار لە 📅:</label>
        <input type="date" name="from_date" value="<?= htmlspecialchars($from_date) ?>">
        <label>بەروار بۆ 📅:</label>
        <input type="date" name="to_date" value="<?= htmlspecialchars($to_date) ?>">
        <button type="submit" class="btn btn-filter">فلتەرکردن 🔍</button>
        <button type="button" class="btn btn-reset" onclick="window.location.href='completed_tasks.php'">هەڵوەشاندنەوەی فلتەر 🔄</button>
    </form>

    <table>
        <tr>
            <th>ID </th>
            <th>ئەرك </th>
            <th>ژمارە </th>
            <th>شوێن </th>
            <th>کارمەند </th>
            <th>ژمارە مۆبایل </th>
            <th>تیم </th>
            <th>حاڵەت </th>
            <th>نرخ </th>
            <th>بەروار </th>
            <th>بەرواری تەواو کردن </th>
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

    <br>
    <button onclick="window.location.href='export_completed_tasks.php?from_date=<?= htmlspecialchars($from_date) ?>&to_date=<?= htmlspecialchars($to_date) ?>'" class="btn btn-export">ئێکسپۆرتکردن بۆ ئێکسڵ 📤</button>
</div>

<div class="fab" onclick="window.location.href='add_task.php'">
    <i class="fas fa-plus"></i>
</div>

</body>
</html>
