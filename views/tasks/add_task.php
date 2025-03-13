<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include '../../includes/db.php'; // ڕێڕەوی دروست بۆ db.php

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}


require __DIR__ . '/../../vendor/autoload.php';



use Google\Client;
use Google\Service\Drive;

// Configure Google Drive API
function getClient() {
    $client = new Client();
    $client->setAuthConfig('../../credentials.json');
    $client->addScope(Drive::DRIVE_FILE);
    $client->setAccessType('offline');
    return $client;
}

function uploadToGoogleDrive($filePath, $fileName) {
    $client = getClient();
    $service = new Drive($client);

    $fileMetadata = new Drive\DriveFile(array(
        'name' => $fileName
    ));
    $content = file_get_contents($filePath);

    $file = $service->files->create($fileMetadata, array(
        'data' => $content,
        'mimeType' => mime_content_type($filePath),
        'uploadType' => 'multipart',
        'fields' => 'id'
    ));

    return $file->id;
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $task_name     = $_POST['task_name'] ?? '';
    $task_number   = $_POST['task_number'] ?? '';
    $location      = $_POST['location'] ?? '';
    $employee      = implode(',', $_POST['employee'] ?? []);
    $mobile_number = $_POST['mobile_number'] ?? '';
    $team          = $_POST['team'] ?? 'تەکنیکی';
    $status        = $_POST['status'] ?? 'Pending';
    $cost          = $_POST['cost'] ?? '';
    $currency      = $_POST['currency'] ?? 'IQD';
    $date          = $_POST['date'] ?? date('Y-m-d H:i:s');
    $date          = date('Y-m-d H:i:s', strtotime($date));

    $completion_date = null;
    if ($status === 'Completed') {
        $completion_date = date('Y-m-d H:i:s');
    }

    // Receive uploaded files URLs from frontend
    $uploadedFiles = $_POST['uploaded_files'] ?? '';

    // Prepare SQL Insert
    $query = "INSERT INTO tasks (task_name, task_number, location, employee, mobile_number, team, status, cost, currency, date, files, completion_date)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($db, $query);

    mysqli_stmt_bind_param(
        $stmt,
        'ssssssssssss',
        $task_name,
        $task_number,
        $location,
        $employee,
        $mobile_number,
        $team,
        $status,
        $cost,
        $currency,
        $date,
        $uploadedFiles,
        $completion_date
    );

    if (mysqli_stmt_execute($stmt)) {
        // Telegram Bot Notification
        $telegram_api = "https://api.telegram.org/botXXXXXXXXXXX";
        $telegram_chat_id = "-1002256776178";

        $message = "🔹 ئەرکی نوێ زیاد کرا 🔹\n";
        $message .= "📌 ئەرک: $task_name\n";
        $message .= "🔢 ژمارە:  $task_number\n";
        $message .= "📍 شوێن:  $location\n";
        $message .= "👥 کارمەند: $employee\n";
        $message .= "📞 ژمارە مۆبایل:  $mobile_number\n";
        $message .= "👥 تیم: $team\n";
        $message .= "📊 حاڵەت: $status\n";
        $message .= "📅 بەروار:  $date\n";
        $message .= "📂 هاوپێچ: $uploadedFiles\n";

        file_get_contents("$telegram_api/sendMessage?chat_id=$telegram_chat_id&text=" . urlencode($message));

        echo json_encode(['success' => true]);
        exit();
    } else {
        echo json_encode(['success' => false, 'error' => mysqli_error($db)]);
        exit();
    }

    mysqli_stmt_close($stmt);
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
            backdrop-filter: blur(12px);
            border-radius: 1.5rem;
            box-shadow: 0 12px 32px rgba(31, 38, 135, 0.1);
        }

        .dashboard-btn {
            background-color: rgb(67, 56, 202);
            color: #fff;
            padding: 0.75rem 1.75rem;
            border-radius: 1rem;
            transition: transform 0.3s, box-shadow 0.3s;
            font-size: 1.1rem;
        }

        .dashboard-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 12px 20px rgba(67, 56, 202, 0.4);
        }

        #progressPopup, #successPopup {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: rgba(0, 0, 0, 0.6);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        #progressPopup div, #successPopup div {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        #dropArea {
            border: 2px dashed rgb(67, 56, 202);
            padding: 40px;
            border-radius: 12px;
            text-align: center;
            cursor: pointer;
            transition: 0.3s;
        }

        #dropArea.dragover {
            background-color: rgba(67, 56, 202, 0.1);
        }

        #progressText {
            font-size: 1.2rem;
            color: #333;
            margin-top: 10px;
        }
    </style>
</head>

<body class="flex items-center justify-center min-h-screen p-4">

    <!-- Progress Popup -->
    <div id="progressPopup">
        <div>
            <h2 class="text-lg font-bold text-indigo-700">📤 فایلەکان بار دەکرێنەوە...</h2>
            <progress id="progressBar" value="0" max="100" style="width: 300px;"></progress>
            <p id="progressText" class="text-indigo-700 mt-3">0%</p>
        </div>
    </div>

    <!-- Success Popup -->
    <div id="successPopup">
        <div>
            <h2 class="text-lg font-bold text-green-600">✅ ئەرکەکە زیاد بووە!</h2>
        </div>
    </div>

    <div class="glass w-full max-w-3xl p-10">
        <h2 class="text-center text-3xl font-bold text-indigo-700 mb-8">➕ زیادکردنی ئەرك</h2>

        <form method="POST" enctype="multipart/form-data" id="task-form" class="space-y-6">

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

            <div class="grid grid-cols-2 gap-4">
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
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">💲 نرخ</label>
                    <input type="number" name="cost" step="0.01" class="form-control rounded-lg border-2 border-indigo-300">
                </div>

                <div>
                    <label class="form-label">💱 دراو</label>
                    <select name="currency" class="form-select rounded-lg border-2 border-indigo-300">
                        <option value="IQD">IQD - دینار</option>
                        <option value="USD">USD - دۆلار</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="form-label">📅 بەروار</label>
                <input type="datetime-local" name="date" value="<?= date('Y-m-d\TH:i') ?>" class="form-control rounded-lg border-2 border-indigo-300" required>
            </div>

            <div>
                <label class="form-label">📂 هاوپێچەکان</label>
                <div id="dropArea" onclick="document.getElementById('fileInput').click();">
                    🖱️ فایلەکان ڕابکێشە بۆ ناو ئەم قاڵبە یان کلیک بکە
                    <input type="file" id="fileInput" multiple class="hidden" />
                </div>
            </div>

            <div class="flex justify-center gap-4 mt-6">
                <button type="button" onclick="submitTaskForm()" class="dashboard-btn">💾 زیادکردن</button>
                <a href="../tasks.php" class="dashboard-btn bg-red-500 hover:bg-red-600">⬅️ گەڕانەوە</a>
            </div>

        </form>
    </div>

    <script>
        function addEmployeeField() {
            const container = document.getElementById("employee_fields");
            const input = document.createElement("input");
            input.type = "text";
            input.name = "employee[]";
            input.className = "form-control rounded-lg border-2 border-indigo-300 mb-2";
            container.appendChild(input);
        }

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

        async function submitTaskForm() {
            const files = fileInput.files;
            const progressPopup = document.getElementById('progressPopup');
            const progressBar = document.getElementById('progressBar');
            const progressText = document.getElementById('progressText');
            const successPopup = document.getElementById('successPopup');
            let uploadedUrls = [];

            if (files.length > 0) {
                progressPopup.style.display = 'flex';
            }

            for (let i = 0; i < files.length; i++) {
                const formData = new FormData();
                formData.append("file", files[i]);

                await fetch("http://localhost/o_data/uploads/upload_to_cloudinary.php", {
                    method: "POST",
                    body: formData,
                }).then(response => response.json())
                .then(data => {
                    if (data.success) {
                        uploadedUrls.push(data.url);
                        let percent = Math.round(((i + 1) / files.length) * 100);
                        progressBar.value = percent;
                        progressText.innerText = `بارکردن: %${percent}`;
                    } else {
                        progressText.innerText = `❌ هەڵە: ${data.error}`;
                    }
                }).catch(error => {
                    progressText.innerText = `❌ هەڵەی نێتۆرک: ${error}`;
                });
            }

            const formElement = document.getElementById('task-form');
            const taskFormData = new FormData(formElement);
            taskFormData.append("uploaded_files", uploadedUrls.join(","));

            fetch("http://localhost/o_data/views/tasks/save_task.php", {
                method: "POST",
                body: taskFormData,
            }).then(response => response.json())
            .then(data => {
                if (data.success) {
                    progressPopup.style.display = 'none';
                    successPopup.style.display = 'flex';
                    setTimeout(() => {
                        successPopup.style.display = 'none';
                        window.location.href = "../tasks.php";
                    }, 2000);
                } else {
                    alert("❌ هەڵە: " + data.error);
                }
            });
        }
    </script>

</body>
</html>