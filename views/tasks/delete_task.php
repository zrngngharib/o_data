<?php
session_start();
include '../../db.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $task = $_POST['task'];
    $number = $_POST['number'];
    $location = $_POST['location'];
    $employee = $_POST['employee'];
    $mobile = $_POST['mobile'];
    $team = $_POST['team'];
    $status = $_POST['status'];
    $cost = $_POST['cost'];
    $currency = $_POST['currency'];
    $date = $_POST['date'];
    
    $sql = "INSERT INTO tasks (task, number, location, employee, mobile, team, status, cost, currency, date) 
            VALUES ('$task', '$number', '$location', '$employee', '$mobile', '$team', '$status', '$cost', '$currency', '$date')";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: ./tasks.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
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
