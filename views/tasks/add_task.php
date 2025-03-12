
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include '../../includes/db.php'; // ڕێڕەوی دروست بۆ db.php

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // تاقیکردنەوەی ئەوەی کە فۆڕمێک نێردراوە
    if (!isset($_POST['task_name']) || empty($_POST['task_name'])) {
        echo "<p style='color: red;'>هەڵە: ناوی ئەرك پێویستە!</p>";
    } else {
        $task_name = $_POST['task_name'] ?? '';
        $task_number = $_POST['task_number'] ?? '';
        $location = $_POST['location'] ?? '';
        $employee = implode(',', $_POST['employee'] ?? []);
        $mobile_number = $_POST['mobile_number'] ?? '';
        $team = $_POST['team'] ?? 'تەکنیکی';
        $status = $_POST['status'] ?? 'Pending';
        $cost = $_POST['cost'] ?? '';
        $currency = $_POST['currency'] ?? 'IQD';
        $date = $_POST['date'] ?? date('Y-m-d H:i:s');
        $date = date('Y-m-d H:i:s', strtotime($date));

        // Handle file uploads
        $uploadDir = 'att/uploads/' . date('Y') . '/' . date('m') . '/' . date('d') . '/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true); // create folder if not exists
        }

        $uploadedFiles = [];
        foreach ($_FILES['files']['name'] as $key => $name) {
            if ($_FILES['files']['error'][$key] == UPLOAD_ERR_OK) {
                $tmpName = $_FILES['files']['tmp_name'][$key];
                $fileExtension = pathinfo($name, PATHINFO_EXTENSION);

                // Use the original filename instead of creating a new one
                $newFileName = date('y-m-d-H-i-s') . '.' . $fileExtension;
                $filePath = $uploadDir . $newFileName;

                if (move_uploaded_file($tmpName, $filePath)) {
                    $uploadedFiles[] = $filePath;
                } else {
                    echo "<p style='color: red;'>هەڵە لە بارکردنی فایل: $name</p>";
                }
            }
        }
        $completion_date = null;
        if ($status == 'Completed') {
            $completion_date = date('Y-m-d H:i:s');
        }
        // Prepare the SQL query
        $query = "INSERT INTO tasks (task_name, task_number, location, employee, mobile_number, team, status, cost, currency, date, files, completion_date)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($db, $query);
        $files = implode(',', $uploadedFiles);
        mysqli_stmt_bind_param($stmt, 'ssssssssssss', $task_name, $task_number, $location, $employee, $mobile_number, $team, $status, $cost, $currency, $date, $files, $completion_date);

        // Telegram API بۆ ناردنی پەیام بۆ گروپی تێلەگرام
        $telegram_api = "https://api.telegram.org/bot7286061251:AAEjEI8uhp0K8yw0Gg_ooq2NYA9J4Z1tJJ8";
        $telegram_chat_id = "-1002256776178";

        // Execute the query
        if (mysqli_stmt_execute($stmt)) {
            // Prepare the message
            $message = "🔹 ئەرکی نوێ زیاد کرا 🔹\n";
            $message .= "📌 ئەرک: $task_name\n";
            $message .= "🔢 ژمارە:  $task_number\n";
            $message .= "📍 شوێن:  $location\n";
            $message .= "👥 کارمەند: $employee\n";
            $message .= "📞 ژمارە مۆبایل:  $mobile_number\n";
            $message .= "👥 تیم: $team\n";
            $message .= "📊 حاڵەت: $status\n";
            $message .= "📅 بەروار:  $date\n";
            $message .= "📂 هاوپێچ " . implode(', ', $uploadedFiles) . "\n";

            // Send the message to Telegram
            $response = file_get_contents("$telegram_api/sendMessage?chat_id=$telegram_chat_id&text=" . urlencode($message));
            if ($response === false) {
                echo "<p style='color: red;'>❌ هەڵەی ناردنی پەیام بۆ تێلەگرام.</p>";
            }

            echo "<script>alert('کارەکە زیاد کرا'); setTimeout(function(){ window.location.href = '../../views/tasks.php'; }, 1000);</script>";
        } else {
            echo "<p style='color: red;'>هەڵە: ناتوانرا فایلی نوێ بنووسرێت.</p>";
            echo "<p style='color: red;'>MySQL Error: " . mysqli_error($db) . "</p>";
        }

        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="ku" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>➕ زیادکردنی ئەرك</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @font-face {
            font-family: 'Zain';
            src: url('../fonts/Zain.ttf');
        }
        body {
            font-family: 'Zain', sans-serif;
            background: linear-gradient(135deg, #dee8ff, #f5f7fa);
        }
        .glass {
            background: rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.1);
        }
        .dashboard-btn {
            background-color: #4F46E5;
            color: #fff;
            padding: 0.5rem 1.5rem;
            border-radius: 1rem;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .dashboard-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.4);
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">

    <div class="glass w-full max-w-2xl p-8">
        <h2 class="text-center text-2xl font-bold text-indigo-700 mb-6">➕ زیادکردنی ئەرك</h2>

        <form method="POST" enctype="multipart/form-data" class="space-y-4">

            <div>
                <label class="form-label">📝 ناوی ئەرك</label>
                <input type="text" name="task_name" class="form-control rounded-lg border-2 border-indigo-300" required>
            </div>

            <div>
                <label class="form-label">📄 ژمارەی ئەرك</label>
                <input type="text" name="task_number" class="form-control rounded-lg border-2 border-indigo-300">
            </div>

            <div>
                <label class="form-label">📍 شوێن</label>
                <input type="text" name="location" class="form-control rounded-lg border-2 border-indigo-300">
            </div>

            <div>
                <label class="form-label">👥 کارمەندەکان</label>
                <div id="employee_fields">
                    <input type="text" name="employee[]" class="form-control rounded-lg border-2 border-indigo-300 mb-2">
                </div>
                <button type="button" onclick="addEmployeeField()" class="dashboard-btn bg-green-600 hover:bg-green-700">➕ زیادکردنی کارمەند</button>
            </div>

            <div>
                <label class="form-label">📱 ژمارەی مۆبایل</label>
                <input type="text" name="mobile_number" class="form-control rounded-lg border-2 border-indigo-300">
            </div>

            <div>
                <label class="form-label">👥 تیم</label>
                <select name="team" class="form-select rounded-lg border-2 border-indigo-300" required>
                    <option value="تەکنیکی">تەکنیکی</option>
                    <option value="دەرەکی">دەرەکی</option>
                </select>
            </div>

            <div>
                <label class="form-label">📌 حاڵەت</label>
                <select name="status" class="form-select rounded-lg border-2 border-indigo-300" required>
                    <option value="Pending">⏳ چاوەڕوانی</option>
                    <option value="In Progress">🚧 دەستیپێکردوە</option>
                    <option value="Completed">✅ تەواوکراوە</option>
                </select>
            </div>

            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <label class="form-label">💲 نرخ</label>
                    <input type="number" name="cost" step="0.01" class="form-control rounded-lg border-2 border-indigo-300">
                </div>

                <div class="flex-1">
                    <label class="form-label">💱 دراو</label>
                    <select name="currency" class="form-select rounded-lg border-2 border-indigo-300">
                        <option value="IQD">IQD - دینار</option>
                        <option value="USD">USD - دۆلار</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="form-label">📅 بەروار</label>
                <input type="datetime-local" name="date"
                       value="<?= date('Y-m-d\TH:i') ?>"
                       class="form-control rounded-lg border-2 border-indigo-300" required>
            </div>

            <div>
                <label class="form-label">📂 هاوپێچەکان</label>
                <input type="file" name="files[]" multiple class="form-control rounded-lg border-2 border-indigo-300">
            </div>

            <div class="flex justify-center gap-4 mt-6">
                <button type="submit" class="dashboard-btn bg-green-600 hover:bg-green-700">💾 زیادکردن</button>
                <a href="../views/tasks.php" class="dashboard-btn bg-red-500 hover:bg-red-600">⬅️ گەڕانەوە</a>
            </div>

        </form>
    </div>

    <script>
        function addEmployeeField() {
            const container = document.getElementById("employee_fields");
            const input = document.createElement("input");
            input.setAttribute("type", "text");
            input.setAttribute("name", "employee[]");
            input.className = "form-control rounded-lg border-2 border-indigo-300 mb-2";
            container.appendChild(input);
        }
    </script>

</body>
</html>
