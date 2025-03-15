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
        $status = $_POST['status'] ?? 'چاوەڕوانی';
        $cost = $_POST['cost'] ?? '';
        $currency = $_POST['currency'] ?? 'IQD';
        $date = $_POST['date'] ?? date('Y-m-d H:i:s');
        $date = date('Y-m-d H:i:s', strtotime($date));

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
        if ($status == 'تەواوکراوە') {
            $completion_date = date('Y-m-d H:i:s');
        }
        // Prepare the SQL query
        $query = "INSERT INTO tasks (task_name, task_number, location, employee, mobile_number, team, status, cost, currency, date, files, completion_date)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($db, $query);
        $files = implode(',', $uploadedFiles);
        mysqli_stmt_bind_param($stmt, 'ssssssssssss', $task_name, $task_number, $location, $employee, $mobile_number, $team, $status, $cost, $currency, $date, $files, $completion_date);

        // Execute the query
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('کارەکە زیاد کرا'); setTimeout(function(){ window.location.href = '../../views/tasks.php'; }, 1000);</script>";
        } else {
            echo "<p style='color: red;'>هەڵە: ناتوانرا فایلی نوێ بنووسرێت.</p>";
            echo "<p style='color: red;'>MySQL Error: " . mysqli_error($db) . "</p>";
        }

        // Telegram API بۆ ناردنی پەیام بۆ گروپی تێلەگرام
        $telegram_api = "https://api.telegram.org/bot7286061251:AAEjEI8uhp0K8yw0Gg_ooq2NYA9J4Z1tJJ8";
        $telegram_chat_id = "-1002256776178";

        // Prepare the message
        $message = "ئەرکی نوێ زیاد کرا\n";
        $message .= "ئەرک: $task_name\n";
        $message .= "ژمارە:  $task_number\n";
        $message .= "شوێن:  $location\n";
        $message .= "کارمەند: $employee\n";
        $message .= "ژمارە مۆبایل:  $mobile_number\n";
        $message .= "تیم: $team\n";
        $message .= "حاڵەت: $status\n";
        $message .= "بەروار:  $date\n";
        $message .= "هاوپێچ " . implode(', ', $uploadedFiles) . "\n";

        // Send the message to Telegram
        $response = file_get_contents("$telegram_api/sendMessage?chat_id=$telegram_chat_id&text=" . urlencode($message));
        if ($response === false) {
            echo "<p style='color: red;'>هەڵەی ناردنی پەیام بۆ تێلەگرام.</p>";
        }

        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="ku" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>زیادکردنی ئەرك</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>

    <style>
        @font-face {
            font-family: 'Zain';
            src: url('../../fonts/Zain.ttf');
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
            font-family: 'Zain';
        }
        .dashboard-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.4);
        }
        /* Drag & Drop Styles */
        #dropArea {
            border: 2px dashed #4F46E5;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            cursor: pointer;
            margin-top: 1rem;
            transition: 0.3s;
        }
        #dropArea.dragover {
            background-color: rgba(79, 70, 229, 0.1);
        }

                /* ✅ Popups */
                .popup-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        .popup-content {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        }
        .popup-progress {
            width: 300px;
            margin-top: 15px;
        }
    </style>
    </head>
    <body class="flex items-center justify-center min-h-screen p-4">

    <!-- ✅ Progress Popup -->
    <div id="progressPopup" class="popup-overlay">
        <div class="popup-content">
            <h3 class="text-lg font-bold text-indigo-600 mb-3"><i class="fas fa-upload"></i> فایلەکان باردەکرێت, تکایە چاوەڕێبە...</h3>
            <progress id="progressBar" value="0" max="100" class="popup-progress"></progress>
            <p id="progressText" class="mt-2 text-sm">0%</p>
        </div>
    </div>

    <!-- ✅ Success Popup -->
    <div id="successPopup" class="popup-overlay">
        <div class="popup-content">
            <h3 class="text-lg font-bold text-green-600"><i class="fas fa-check-circle"></i> ئەرکەکە بە سەرکەوتووی زیادکرا!</h3>
        </div>
    </div>


<!-- فۆڕمی ئەرکەکان -->
<div class="glass w-full max-w-2xl p-8">
    <h2 class="text-center text-2xl font-bold text-indigo-700 mb-6"><i class="fas fa-plus-circle"></i> زیادکردنی ئەرك</h2>

    <form id="taskForm" method="POST" enctype="multipart/form-data" class="space-y-4">

        <div class="flex flex-row gap-1">
            <div class="flex-1">
                <label class="form-label"><i class="fas fa-tasks"></i> ناوی ئەرك</label>
                <input type="text" name="task_name" class="form-control rounded-lg border-2 border-indigo-300" required>
            </div>

            <div class="flex-1">
                <label class="form-label"><i class="fas fa-hashtag"></i> ژمارە </label>
                <input type="text" name="task_number" class="form-control rounded-lg border-2 border-indigo-300">
            </div>
        </div>

        <div class="flex flex-row gap-1">
            <div class="flex-1">
                <label class="form-label"><i class="fas fa-map-marker-alt"></i> شوێن</label>
                <input type="text" name="location" class="form-control rounded-lg border-2 border-indigo-300">
            </div>

            <div class="flex-1">
                <label class="form-label"><i class="fas fa-phone"></i> ژمارەی مۆبایل</label>
                <input type="text" name="mobile_number" class="form-control rounded-lg border-2 border-indigo-300">
            </div>
        </div>

        <div>
            <label class="form-label"><i class="fas fa-users"></i> کارمەندەکان</label>
            <div id="employee_fields">
                <input type="text" name="employee[]" class="form-control rounded-lg border-2 border-indigo-300 mb-2">
            </div>
            <button type="button" onclick="addEmployeeField()" class="color-black"><i class="fas fa-plus"></i> زیادکردنی کارمەند</button>
        </div>

        <div class="flex flex-row gap-1">
            <div class="flex-1">
                <label class="form-label"><i class="fas fa-users-cog"></i> تیم</label>
                <select name="team" class="form-select rounded-lg border-2 border-indigo-300" required>
                    <option value="تەکنیکی">تەکنیکی</option>
                    <option value="دەرەکی">دەرەکی</option>
                </select>
            </div>

            <div class="flex-1">
                <label class="form-label"><i class="fas fa-tasks"></i> حاڵەت</label>
                <select name="status" class="form-select rounded-lg border-2 border-indigo-300" required>
                    <option value="چاوەڕوانی">چاوەڕوانی</option>
                    <option value="دەستپێکراوە">دەستپێکراوە</option>
                    <option value="تەواوکراوە">تەواوکراوە</option>
                </select>
            </div>
        </div>

        <div class="flex flex-col md:flex-row gap-1">
            <div class="flex-1">
                <label class="form-label"><i class="fas fa-dollar-sign"></i> نرخ</label>
                <input type="number" name="cost" step="0.01" class="form-control rounded-lg border-2 border-indigo-300">
            </div>

            <div class="flex-1">
                <label class="form-label"><i class="fas fa-money-bill-wave"></i> دراو</label>
                <select name="currency" class="form-select rounded-lg border-2 border-indigo-300">
                    <option value="IQD">IQD - دینار</option>
                    <option value="USD">USD - دۆلار</option>
                </select>
            </div>
        </div>

        <div>
            <label class="form-label"><i class="fas fa-calendar-alt"></i> بەروار</label>
            <input type="datetime-local" name="date"
                value="<?= date('Y-m-d\TH:i') ?>"
                class="form-control rounded-lg border-2 border-indigo-300" required>
        </div>

        <!-- ✅ Drag & Drop Upload Section -->
        <div>
            <label class="form-label"><i class="fas fa-file-upload"></i> وێنەکان</label>
            <div id="dropArea" onclick="document.getElementById('fileInput').click();">
                <i class="fas fa-mouse-pointer"></i> فایلەکان ڕابکێشە یان کلیک بکە بۆ هەڵبژاردن!
                <input type="file" id="fileInput" name="files[]" multiple class="hidden" />
            </div>
        </div>

        <div class="flex justify-center gap-4 mt-6">
            <button type="button" onclick="submitTaskForm()" class="dashboard-btn bg-green-600 hover:bg-green-700"><i class="fas fa-save"></i> زیادکردن</button>
            <a href="../tasks.php" class="dashboard-btn bg-red-500 hover:bg-red-600"><i class="fas fa-arrow-left"></i> گەڕانەوە</a>
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

    // Drag & Drop
    const dropArea = document.getElementById('dropArea');
    const fileInput = document.getElementById('fileInput');

    dropArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropArea.classList.add('dragover');
    });

    dropArea.addEventListener('dragleave', () => {
        dropArea.classList.remove('dragover');
    });

    dropArea.addEventListener('drop', (e) => {
        e.preventDefault();
        dropArea.classList.remove('dragover');
        fileInput.files = e.dataTransfer.files;
    });

    // Submit Task Form
    async function submitTaskForm() {
        const formElement = document.getElementById('taskForm');
        const taskFormData = new FormData(formElement);
        const files = fileInput.files;

        let uploadedUrls = [];
        const progressPopup = document.getElementById('progressPopup');
        const successPopup = document.getElementById('successPopup');
        const progressBar = document.getElementById('progressBar');
        const progressText = document.getElementById('progressText');

        if (files.length > 0) {
            progressPopup.style.display = 'flex';

            for (let i = 0; i < files.length; i++) {
                const formData = new FormData();
                formData.append("file", files[i]);

                await fetch("../../uploads/upload_to_cloudinary.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        uploadedUrls.push(data.url);

                        let percent = Math.round(((i + 1) / files.length) * 100);
                        progressBar.value = percent;
                        progressText.innerText = `${percent}%`;
                    } else {
                        alert("هەڵە لە بارکردنی وێنەکان: " + data.error);
                    }
                })
                .catch(err => {
                    alert("هەڵەی نێتۆرک: " + err);
                });
            }
        }

        taskFormData.append("uploaded_files", uploadedUrls.join(","));

        fetch("../../views/tasks/save_task.php", {
            method: "POST",
            body: taskFormData
        })
        .then(response => response.json())
        .then(data => {
            progressPopup.style.display = 'none';
            if (data.success) {
                successPopup.style.display = 'flex';
                setTimeout(() => {
                    successPopup.style.display = 'none';
                    window.location.href = "../../views/tasks.php";
                }, 2000);
            } else {
                alert("هەڵە لە زیادکردنی ئەرک: " + data.error);
            }
        });
    }
</script>
</body>
</html>