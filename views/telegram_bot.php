<?php
// telegram_bot.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once "../includes/db.php"; // پەیوەندی بە داتابەیس

// API بۆ ناردنی پەیام بۆ گروپی تێلەگرام
$telegram_api = "https://api.telegram.org/bot7286061251:AAEjEI8uhp0K8yw0Gg_ooq2NYA9J4Z1tJJ8";
$telegram_chat_id = "-1002256776178";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['send_pending_tasks'])) {
        // ناردنی ئەرکەکان بۆ گروپ
        $query = "SELECT * FROM tasks WHERE status IN ('Pending', 'In Progress')";
        $result = mysqli_query($conn, $query);
        
        $message = "📌 **ئەرکە تەواو نەکراوەکان:**\n";
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $message .= "🔹 **" . $row['task_name'] . "**\n📍 شوێن: " . $row['location'] . "\n📅 بەروار: " . $row['date'] . "\n\n";
            }

            $response = file_get_contents("$telegram_api/sendMessage?chat_id=$telegram_chat_id&text=" . urlencode($message));
            if ($response === false) {
                echo "❌ هەڵەی ناردنی پەیام بۆ تێلەگرام.";
            }
        } else {
            echo "✅ هیچ ئەرکە تەواو نەکراو نییە.";
        }
    }

    if (isset($_POST['send_custom_message'])) {
        // ناردنی پەیامی تایبەتی بۆ گروپ
        $name = $_POST['name'];
        $message = $_POST['message'];

        if (!is_dir("uploads")) {
            mkdir("uploads", 0777, true);
        }

        if (!empty($_FILES['file']['name'])) {
            // بارکردنی فایل
            $file_tmp = $_FILES['file']['tmp_name'];
            $file_name = basename($_FILES['file']['name']); // ڕێگری لە هەڵەکان
            
            $upload_path = "uploads/$file_name";
            if (move_uploaded_file($file_tmp, $upload_path)) {
                if (file_exists($upload_path)) {
                    $document = new CURLFile(realpath($upload_path));
                    $post_fields = [
                        'chat_id' => $telegram_chat_id,
                        'document' => $document,
                        'caption' => "📨 پەیامی نوێ\n👤 ناو: $name\n💬 $message"
                    ];

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type:multipart/form-data"]);
                    curl_setopt($ch, CURLOPT_URL, "$telegram_api/sendDocument");
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
                    $response = curl_exec($ch);
                    curl_close($ch);
                    
                    if ($response === false) {
                        echo "❌ هەڵەی ناردنی فایل بۆ تێلەگرام.";
                    }
                } else {
                    echo "❌ هەڵە: فایل نەدۆزرایەوە.";
                }
            } else {
                echo "❌ هەڵە: نەتوانی فایلی بارکراو بگوازی.";
            }
        } else {
            $response = file_get_contents("$telegram_api/sendMessage?chat_id=$telegram_chat_id&text=" . urlencode("📨 پەیامی نوێ\n👤 ناو: $name\n💬 $message"));
            if ($response === false) {
                echo "❌ هەڵەی ناردنی پەیام بۆ تێلەگرام.";
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="ku">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بۆتی تیلیگرام</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Zain:wght@200;300;400;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        body {
            direction: rtl;
            font-family: 'Zain', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-6">
    <div class="max-w-2xl w-full bg-white shadow-lg rounded-lg p-8">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-6">بۆتی تیلیگرام</h2>
        
        <form method="POST" class="mb-6">
            <button type="submit" name="send_pending_tasks" class="w-full bg-blue-500 text-white py-3 px-6 rounded-lg hover:bg-blue-600 transition" onclick="showPopup('pending')">
                📢 ناردنی ئەرکە تەواو نەکراوەکان
            </button>
        </form>
        
        <form method="POST" enctype="multipart/form-data" class="space-y-4">
            <h3 class="text-xl font-semibold text-gray-700">📨 ناردنی پەیامی تایبەتی</h3>
            
            <div>
                <label class="block text-gray-700 font-medium">👤 ناو:</label>
                <input type="text" name="name" required class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label class="block text-gray-700 font-medium">💬 پەیام:</label>
                <textarea name="message" required class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            </div>
            
            <div>
                <label class="block text-gray-700 font-medium">📎 بارکردنی فایل:</label>
                <input type="file" name="file" class="w-full border border-gray-300 rounded-lg p-3">
            </div>
            
            <button type="submit" name="send_custom_message" class="w-full bg-green-500 text-white py-3 px-6 rounded-lg hover:bg-green-600 transition" onclick="showPopup('custom')">
                📤 ناردنی پەیام
            </button>
        </form>
    </div>

    <!-- Popup Notification -->
    <div id="popup" class="fixed top-0 left-0 w-full h-full flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg text-center">
            <p id="popup-message" class="text-lg font-semibold text-gray-800">✅ پەیامەکەت بە سەرکەوتوویی نێردرا!</p>
        </div>
    </div>

    <script>
        function showPopup(type) {
            const popup = document.getElementById("popup");
            const message = document.getElementById("popup-message");
            
            if (type === 'pending') {
                message.textContent = "✅ ئەرکەکان بە سەرکەوتوویی نێردران!";
            } else {
                message.textContent = "✅ پەیامەکەت بە سەرکەوتوویی نێردرا!";
            }
            
            popup.classList.remove("hidden");
            
            setTimeout(() => {
                popup.classList.add("hidden");
            }, 2000);
        }
    </script>
</body>
</html>