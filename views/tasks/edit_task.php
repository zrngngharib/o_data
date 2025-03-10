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
<html lang="ku" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>دەستکاری ئەرك ✏️</title>

    <!-- Import Zain Font -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Zain:ital,wght@0,400;0,700;1,400&display=swap');

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Zain', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #e6f0fa;
            color: #333;
            direction: rtl;
        }

        .container {
            max-width: 700px;
            margin: 50px auto;
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 123, 255, 0.2);
        }

        h1 {
            text-align: center;
            color: #007bff;
            margin-bottom: 30px;
            font-weight: 700;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-weight: 600;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="number"],
        input[type="datetime-local"],
        select {
            padding: 12px 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            font-family: 'Zain', sans-serif;
            width: 100%;
        }

        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Zain', sans-serif;
        }

        button:hover {
            background-color: #0056b3;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: #007bff;
            text-decoration: none;
            font-weight: 600;
        }

        .back-link a:hover {
            text-decoration: underline;
        }

    </style>
</head>
<body>
    <div class="container">
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
        </form>
        <div class="back-link">
            <a href="../tasks.php">گەڕانەوە</a>
        </div>
    </div>
</body>
</html>