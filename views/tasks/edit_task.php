<?php
session_start();
include '../../includes/db.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$task_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_name = $_POST['task_name'];
    $task_number = $_POST['task_number'];
    $location = $_POST['location'];
    $employee = $_POST['employee'];
    $mobile_number = $_POST['mobile_number'];
    $team = $_POST['team'];
    $status = $_POST['status'];
    $cost = $_POST['cost'];
    $currency = $_POST['currency'];
    $date = $_POST['date'];

    $query = "UPDATE tasks SET 
                task_name = '$task_name', 
                task_number = '$task_number', 
                location = '$location', 
                employee = '$employee', 
                mobile_number = '$mobile_number', 
                team = '$team', 
                status = '$status', 
                cost = '$cost', 
                currency = '$currency', 
                date = '$date' 
              WHERE id = $task_id";

    if (mysqli_query($conn, $query)) {
        $success_message = "گۆڕانکاریەکان سەرکەوتوو بوون.";
    } else {
        echo "کێشە لە نوێکردنەوەی داتا: " . mysqli_error($conn);
    }
} else {
    $query = "SELECT * FROM tasks WHERE id = $task_id";
    $result = mysqli_query($conn, $query);
    $task = mysqli_fetch_assoc($result);
}
?>
<!DOCTYPE html>
<html lang="ku">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>دەستکاری ئەرك</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <h1>دەستکاری ئەرك</h1>
    <?php if ($success_message): ?>
        <p style="color: green;"><?= $success_message ?></p>
    <?php endif; ?>
    <form method="POST" action="">
        <label>ئەرك:</label>
        <input type="text" name="task_name" value="<?= htmlspecialchars($task['task_name']) ?>">
        <label>ژمارە:</label>
        <input type="text" name="task_number" value="<?= htmlspecialchars($task['task_number']) ?>">
        <label>شوێن:</label>
        <input type="text" name="location" value="<?= htmlspecialchars($task['location']) ?>">
        <label>کارمەند:</label>
        <input type="text" name="employee" value="<?= htmlspecialchars($task['employee']) ?>">
        <label>ژمارە مۆبایل:</label>
        <input type="number" name="mobile_number" value="<?= htmlspecialchars($task['mobile_number']) ?>" required>
        <label>تیم:</label>
        <select name="team" required>
            <option value="داخلی" <?= $task['team'] == 'داخلی' ? 'selected' : '' ?>>داخلی</option>
            <option value="دەرەکی" <?= $task['team'] == 'دەرەکی' ? 'selected' : '' ?>>دەرەکی</option>
        </select>
        <label>حاڵەت:</label>
        <select name="status" required>
            <option value="Pending" <?= $task['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
            <option value="In Progress" <?= $task['status'] == 'In Progress' ? 'selected' : '' ?>>In Progress</option>
            <option value="Completed" <?= $task['status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
        </select>
        <label>نرخ:</label>
        <input type="text" name="cost" value="<?= htmlspecialchars($task['cost']) ?>">
        <label>دراو:</label>
        <select name="currency" required>
            <option value="دینار" <?= $task['currency'] == 'دینار' ? 'selected' : '' ?>>دینار</option>
            <option value="دۆلار" <?= $task['currency'] == 'دۆلار' ? 'selected' : '' ?>>دۆلار</option>
        </select>
        <label>بەروار:</label>
        <input type="datetime-local" name="date" value="<?= date('Y-m-d\TH:i', strtotime($task['date'])) ?>" required>
        <button type="submit">نوێکردنەوە</button>
        <button type="button" onclick="window.location.href='../tasks.php'">گەڕانەوە</button>
    </form>
</body>
</html>