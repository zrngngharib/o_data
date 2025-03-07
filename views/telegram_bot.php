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

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Zain:wght@200;300;400;700;800;900&display=swap" rel="stylesheet">

<!-- Tailwind CSS -->
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.2.4/dist/tailwind.min.css" rel="stylesheet">

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- RTL Support -->
<style>
    body {
        direction: rtl;
        font-family: 'Zain', sans-serif;
    }
</style>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بۆتی تیلیگرام</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <h2>بۆتی تیلیگرام</h2>
    
    <form method="POST">
        <button type="submit" name="send_pending_tasks">📢 ناردنی ئەرکە تەواو نەکراوەکان</button>
    </form>

    <form method="POST" enctype="multipart/form-data">
        <h3>📨 ناردنی پەیامی تایبەتی</h3>
        <label>👤 ناو:</label>
        <input type="text" name="name" required>

        <label>💬 پەیام:</label>
        <textarea name="message" required></textarea>

        <label>📎 بارکردنی فایل:</label>
        <input type="file" name="file">

        <button type="submit" name="send_custom_message">📤 ناردنی پەیام</button>
    </form>

<!-- Bootstrap Bundle JS (including Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
