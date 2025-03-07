<?php
session_start();
include '../includes/db.php'; // Include database connection

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['id'])) {
    $task_id = (int)$_GET['id'];
    $query = "SELECT * FROM tasks WHERE id = $task_id";
    $result = mysqli_query($conn, $query);
    $task = mysqli_fetch_assoc($result);

    if (!$task) {
        echo "Task not found.";
        exit();
    }
}

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

    $update_query = "UPDATE tasks SET task_name = '$task_name', task_number = '$task_number', location = '$location', employee = '$employee', mobile_number = '$mobile_number', team = '$team', status = '$status', cost = '$cost', currency = '$currency', date = '$date' WHERE id = $task_id";

    if (mysqli_query($conn, $update_query)) {
        header('Location: ../views/tasks.php');
        exit();
    } else {
        echo "Error updating task: " . mysqli_error($conn);
    }
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
    <div class="container">
        <h1>دەستکاری ئەرك</h1>
        <form method="POST">
            <label for="task_name">ئەرك 📋:</label>
            <input type="text" id="task_name" name="task_name" value="<?php echo htmlspecialchars($task['task_name']); ?>" required>

            <label for="task_number">ژمارە 🔢:</label>
            <input type="text" id="task_number" name="task_number" value="<?php echo htmlspecialchars($task['task_number']); ?>" required>

            <label for="location">شوێن 📍:</label>
            <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($task['location']); ?>" required>

            <label for="employee">کارمەند 👤:</label>
            <input type="text" id="employee" name="employee" value="<?php echo htmlspecialchars($task['employee']); ?>" required>

            <label for="mobile_number">ژمارە مۆبایل 📞:</label>
            <input type="text" id="mobile_number" name="mobile_number" value="<?php echo htmlspecialchars($task['mobile_number']); ?>" required>

            <label for="team">تیم 👥:</label>
            <input type="text" id="team" name="team" value="<?php echo htmlspecialchars($task['team']); ?>" required>

            <label for="status">حاڵەت 📊:</label>
            <select id="status" name="status" required>
                <option value="Pending" <?php if ($task['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                <option value="In Progress" <?php if ($task['status'] == 'In Progress') echo 'selected'; ?>>In Progress</option>
                <option value="Completed" <?php if ($task['status'] == 'Completed') echo 'selected'; ?>>Completed</option>
            </select>

            <label for="cost">نرخ 💰:</label>
            <input type="text" id="cost" name="cost" value="<?php echo htmlspecialchars($task['cost']); ?>" required>

            <label for="currency">پەیوەندیدانی نرخی:</label>
            <input type="text" id="currency" name="currency" value="<?php echo htmlspecialchars($task['currency']); ?>" required>

            <label for="date">بەروار 📅:</label>
            <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($task['date']); ?>" required>

            <button type="submit">نوێکردنەوە</button>
        </form>
        <a href="../views/tasks.php">بەرەو پەیجەکە</a>
    </div>
</body>
</html>