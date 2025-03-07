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

    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

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
        .form-input {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            width: 100%;
            border: 1px solid #ddd;
        }
        .form-input:focus {
            border-color: #4f36c7;
            outline: none;
        }
        .form-select {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            width: 100%;
            border: 1px solid #ddd;
        }
        .form-select:focus {
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
        .icon-input {
            padding: 10px;
            width: calc(100% - 40px);
            border-radius: 5px;
            margin-bottom: 15px;
            display: inline-block;
        }
        .input-icon {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            font-size: 18px;
        }
    </style>
</head>
<body>

    <div class="container mt-5">
        <h1>زیادکردنی ئەرك📝</h1>
        <div class="form-container">
            <form method="POST" action="add_task.php">
                <div class="mb-4 relative">
                    <label for="task_name" class="text-lg font-bold">ناوی ئەرك:</label>
                    <input type="text" name="task_name" class="form-input pl-10" required>
                    <i class="fas fa-pencil-alt input-icon"></i>
                </div>
                <div class="mb-4 relative">
                    <label for="task_number" class="text-lg font-bold">ژمارە:</label>
                    <input type="text" name="task_number" class="form-input pl-10">
                    <i class="fas fa-hashtag input-icon"></i>
                </div>
                <div class="mb-4 relative">
                    <label for="location" class="text-lg font-bold">شوێن:</label>
                    <input type="text" name="location" class="form-input pl-10">
                    <i class="fas fa-map-marker-alt input-icon"></i>
                </div>
                <div class="mb-4 relative">
                    <label for="employees" class="text-lg font-bold">کارمەند:</label>
                    <div id="employee_fields">
                        <input type="text" name="employees[]" class="form-input pl-10">
                    </div>
                    <button type="button" class="btn btn-primary mt-2" onclick="addEmployeeField()">+ کارمەندی زیاتری</button>
                    <i class="fas fa-users input-icon"></i>
                </div>
                <div class="mb-4 relative">
                    <label for="mobile_number" class="text-lg font-bold">ژمارە مۆبایل:</label>
                    <input type="number" name="mobile_number" class="form-input pl-10">
                    <i class="fas fa-phone-alt input-icon"></i>
                </div>
                <div class="mb-4 relative">
                    <label for="team" class="text-lg font-bold">تیم:</label>
                    <select name="team" class="form-select">
                        <option value="تەکنیکی">تەکنیکی</option>
                        <option value="دەرەکی">دەرەکی</option>
                    </select>
                    <i class="fas fa-building input-icon"></i>
                </div>
                <div class="mb-4 relative">
                    <label for="status" class="text-lg font-bold">حاڵەت:</label>
                    <select name="status" class="form-select">
                        <option value="Pending">Pending</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Completed">Completed</option>
                    </select>
                    <i class="fas fa-chart-line input-icon"></i>
                </div>
                <div class="mb-4 relative">
                    <label for="cost" class="text-lg font-bold">نرخ:</label>
                    <input type="text" name="cost" class="form-input pl-10">
                    <i class="fas fa-dollar-sign input-icon"></i>
                </div>
                <div class="mb-4 relative">
                    <label for="currency" class="text-lg font-bold">دەرەنگ:</label>
                    <select name="currency" class="form-select">
                        <option value="IQD">دینار</option>
                        <option value="USD">دۆلار</option>
                    </select>
                    <i class="fas fa-money-bill-alt input-icon"></i>
                </div>
                <div class="mb-4 relative">
                    <label for="date" class="text-lg font-bold">بەروار:</label>
                    <input type="datetime-local" name="date" class="form-input pl-10" value="<?= date('Y-m-d\\TH:i') ?>">
                    <i class="fas fa-calendar-alt input-icon"></i>
                </div>
                <button type="submit" class="custom-button">زیادکردن ➕</button>
            </form>
        </div>
    </div>

    <script>
        function addEmployeeField() {
            var div = document.createElement("div");
            div.innerHTML = '<input type="text" name="employees[]" class="form-input pl-10">';
            document.getElementById("employee_fields").appendChild(div);
        }
    </script>

</body>
</html>
