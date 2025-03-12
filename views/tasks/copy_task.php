<?php
session_start();
include '../../includes/db.php'; // ڕێڕەوی دروست بۆ `db.php`

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

if (isset($_GET['id'])) {
    $task_id = $_GET['id'];

    // گەڕانەوەی زانیاریەکانی کارەکە
    $query = "SELECT * FROM tasks WHERE id = $task_id";
    $result = mysqli_query($conn, $query);
    if ($row = mysqli_fetch_assoc($result)) {
        $task_name = $row['task_name'];
        $task_number = $row['task_number'];
        $location = $row['location'];
        $employee = $row['employee'];
        $mobile_number = $row['mobile_number'];
        $team = $row['team'];
        $status = $row['status'];
        $cost = $row['cost'];
        $currency = $row['currency'];
        $date = date('Y-m-d H:i:s'); // بەرواری نوێ

        // زیادکردنی کارە نوێیەکە
        $query = "INSERT INTO tasks (task_name, task_number, location, employee, mobile_number, team, status, cost, currency, date) 
                  VALUES ('$task_name', '$task_number', '$location', '$employee', '$mobile_number', '$team', '$status', '$cost', '$currency', '$date')";
        mysqli_query($conn, $query);
    }
}

header('Location: ../tasks.php');
exit();
?>
<!DOCTYPE html>
<html lang="ku">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>زیادکردنی ئەرك</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>زیادکردنی ئەرك</h1>
    <form action="add_task.php" method="POST">
        <label>ئەرك:</label>
        <input type="text" name="task" required>
        <br>
        <label>ژمارە:</label>
        <input type="text" name="number">
        <br>
        <label>شوێن:</label>
        <input type="text" name="location">
        <br>
        <label>کارمەند:</label>
        <input type="text" name="employee">
        <br>
        <label>ژمارە مۆبایل:</label>
        <input type="number" name="mobile">
        <br>
        <label>تیم:</label>
        <select name="team">
            <option value="داخلی">داخلی</option>
            <option value="دەرەکی">دەرەکی</option>
        </select>
        <br>
        <label>حاڵەت:</label>
        <select name="status">
            <option value="Pending">Pending</option>
            <option value="In Progress">In Progress</option>
            <option value="Completed">Completed</option>
        </select>
        <br>
        <label>نرخ:</label>
        <input type="text" name="cost">
        <select name="currency">
            <option value="دینار">دینار</option>
            <option value="دۆلار">دۆلار</option>
        </select>
        <br>
        <label>بەروار:</label>
        <input type="datetime-local" name="date" required>
        <br>
        <button type="submit">زیادکردن</button>
        <button type="button" onclick="window.location.href='tasks.php'">پاشگەزبوونەوە</button>
    </form>
</body>
</html>
