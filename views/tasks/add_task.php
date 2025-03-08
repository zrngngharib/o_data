<?php
session_start();
include '../../includes/db.php'; // ڕێڕەوی دروست بۆ `db.php`

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // تاقیکردنەوەی ئەوەی کە فۆڕمێک نێردراوە
    if (!isset($_POST['task_name']) || empty($_POST['task_name'])) {
        echo "<p style='color: red;'>هەڵە: ناوی ئەرك پێویستە!</p>";
    } else {
        $task_name = $_POST['task_name'] ?? '';
        $task_number = $_POST['task_number'] ?? '';
        $location = $_POST['location'] ?? '';
        $employees = implode(',', $_POST['employees'] ?? []);
        $mobile_number = $_POST['mobile_number'] ?? '';
        $team = $_POST['team'] ?? 'تەکنیکی';
        $status = $_POST['status'] ?? 'Pending';
        $cost = $_POST['cost'] ?? '';
        $currency = $_POST['currency'] ?? 'IQD';
        $date = $_POST['date'] ?? date('Y-m-d H:i:s');

        // هەڵەکان دیاریبکەوە
        $query = "INSERT INTO tasks (task_name, task_number, location, employee, mobile_number, team, status, cost, currency, date) 
                  VALUES ('$task_name', '$task_number', '$location', '$employees', '$mobile_number', '$team', '$status', '$cost', '$currency', '$date')";

        if (mysqli_query($conn, $query)) {
            echo "<p style='color: green;'>کارەکە زیاد کرا</p>";
            header('refresh:1;url=../tasks.php'); // دوای ٢1 چرکە بەرگەردە `tasks.php`
            exit();
        } else {
            echo "<p style='color: red;'>هەڵە لە زیادکردنی داتا: " . mysqli_error($conn) . "</p>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ku">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>زیادکردنی ئەرك</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Zain:wght@200;300;400;700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'Zain', sans-serif;
            background-color: #f9f9f9;
            direction: rtl;
        }
        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #4f36c7;
        }
        .form-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            max-width: 600px;
            margin: 0 auto;
        }
        .form-input, .form-select {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            width: 100%;
            border: 1px solid #ddd;
        }
        .form-input:focus, .form-select:focus {
            border-color: #4f36c7;
            outline: none;
        }
        .custom-button {
            background-color: #4f36c7;
            color: white;
            padding: 12px 20px;
            border-radius: 30px;
            width: 100%;
            font-size: 16px;
            margin-top: 20px;
            transition: background-color 0.3s ease;
        }
        .custom-button:hover {
            background-color: #3c2b9a;
        }
        .btn-secondary {
            background-color: #e74c3c;
            border: none;
            padding: 12px 20px;
            border-radius: 30px;
            font-size: 16px;
            width: 100%;
            margin-top: 10px;
            transition: background-color 0.3s ease;
        }
        .btn-secondary:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>

    <div class="container mt-5">
        <h1 class="text-3xl text-blue-700 text-center font-bold mb-6">زیادکردنی ئەرك📝</h1>
        <div class="form-container">
            <form method="POST" action="add_task.php">
                <div class="mb-4">
                    <label for="task_name" class="text-lg font-bold">ناوی ئەرك:</label>
                    <input type="text" name="task_name" class="form-input" required>
                </div>
                <div class="mb-4">
                    <label for="task_number" class="text-lg font-bold">ژمارە:</label>
                    <input type="text" name="task_number" class="form-input">
                </div>
                <div class="mb-4">
                    <label for="location" class="text-lg font-bold">شوێن:</label>
                    <input type="text" name="location" class="form-input">
                </div>
                <div class="mb-4">
                    <label for="employees" class="text-lg font-bold">کارمەند:</label>
                    <div id="employee_fields">
                        <input type="text" name="employees[]" class="form-input">
                    </div>
                    <button type="button" class="btn btn-primary mt-2" onclick="addEmployeeField()">+ کارمەندی زیاتری</button>
                </div>
                <div class="mb-4">
                    <label for="mobile_number" class="text-lg font-bold">ژمارە مۆبایل:</label>
                    <input type="number" name="mobile_number" class="form-input">
                </div>
                <div class="mb-4">
                    <label for="team" class="text-lg font-bold">تیم:</label>
                    <div class="flex items-center">
                        <input type="radio" name="team" value="تەکنیکی" id="team_technical" class="form-radio text-indigo-600">
                        <label for="team_technical" class="ml-2">تەکنیکی</label>
                    </div>
                    <div class="flex items-center mt-2">
                        <input type="radio" name="team" value="دەرەکی" id="team_external" class="form-radio text-indigo-600">
                        <label for="team_external" class="ml-2">دەرەکی</label>
                    </div>
                </div>
                <div class="mb-4">
                    <label for="status" class="text-lg font-bold">حاڵەت:</label>
                    <div class="flex items-center">
                        <input type="radio" name="status" value="Pending" id="status_pending" class="form-radio text-indigo-600">
                        <label for="status_pending" class="ml-2">Pending</label>
                    </div>
                    <div class="flex items-center mt-2">
                        <input type="radio" name="status" value="In Progress" id="status_in_progress" class="form-radio text-indigo-600">
                        <label for="status_in_progress" class="ml-2">In Progress</label>
                    </div>
                    <div class="flex items-center mt-2">
                        <input type="radio" name="status" value="Completed" id="status_completed" class="form-radio text-indigo-600">
                        <label for="status_completed" class="ml-2">Completed</label>
                    </div>
                </div>
                <div class="mb-4">
                    <label for="cost" class="text-lg font-bold">نرخ:</label>
                    <input type="text" name="cost" class="form-input">
                </div>
                <div class="mb-4">
                    <label for="currency" class="text-lg font-bold">دراو:</label>
                    <div class="flex items-center">
                        <input type="radio" name="currency" value="IQD" id="currency_iqd" class="form-radio text-indigo-600">
                        <label for="currency_iqd" class="ml-2">دینار</label>
                    </div>
                    <div class="flex items-center mt-2">
                        <input type="radio" name="currency" value="USD" id="currency_usd" class="form-radio text-indigo-600">
                        <label for="currency_usd" class="ml-2">دۆلار</label>
                    </div>
                </div>
                <div class="mb-4">
                    <label for="date" class="text-lg font-bold">بەروار:</label>
                    <input type="datetime-local" name="date" class="form-input" value="<?= date('Y-m-d\\TH:i') ?>">
                </div>
                <button type="submit" class="custom-button">زیادکردن ➕</button>
            </form>
        </div>
    </div>

    <script>
        function addEmployeeField() {
            var div = document.createElement("div");
            div.innerHTML = '<input type="text" name="employees[]" class="form-input">';
            document.getElementById("employee_fields").appendChild(div);
        }
    </script>

</body>
</html>
