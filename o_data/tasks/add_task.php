<?php
session_start();
include '../includes/db.php'; // Include database connection

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $task_name = $_POST['task_name'];
    $task_number = $_POST['task_number'];
    $location = $_POST['location'];
    $employee = $_POST['employee'];
    $mobile_number = $_POST['mobile_number'];
    $team = $_POST['team'];
    $cost = $_POST['cost'];
    $currency = $_POST['currency'];
    $date = $_POST['date'];

    // Validate input
    if (empty($task_name) || empty($task_number) || empty($location) || empty($employee) || empty($mobile_number) || empty($team) || empty($cost) || empty($currency) || empty($date)) {
        $error = "All fields are required.";
    } else {
        // Insert task into database
        $query = "INSERT INTO tasks (task_name, task_number, location, employee, mobile_number, team, cost, currency, date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssssssss", $task_name, $task_number, $location, $employee, $mobile_number, $team, $cost, $currency, $date);
        
        if ($stmt->execute()) {
            $success = "Task added successfully.";
        } else {
            $error = "Error adding task: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="ku">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>زیادکردنی ئەرك</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <div class="container">
        <h1>زیادکردنی ئەرك</h1>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <form method="POST" action="add_task.php">
            <label for="task_name">ئەرك 📋:</label>
            <input type="text" id="task_name" name="task_name" required>

            <label for="task_number">ژمارە 🔢:</label>
            <input type="text" id="task_number" name="task_number" required>

            <label for="location">شوێن 📍:</label>
            <input type="text" id="location" name="location" required>

            <label for="employee">کارمەند 👤:</label>
            <input type="text" id="employee" name="employee" required>

            <label for="mobile_number">ژمارە مۆبایل 📞:</label>
            <input type="text" id="mobile_number" name="mobile_number" required>

            <label for="team">تیم 👥:</label>
            <input type="text" id="team" name="team" required>

            <label for="cost">نرخ 💰:</label>
            <input type="text" id="cost" name="cost" required>

            <label for="currency">بەروار 📅:</label>
            <input type="date" id="date" name="date" required>

            <button type="submit" class="custom-button">زیادکردن</button>
        </form>
        <button class="custom-button" onclick="window.location.href='tasks.php'">بەرگرتن</button>
    </div>
</body>
</html>