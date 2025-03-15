<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$username = $_SESSION['username'];

// API بۆ ناردنی پەیام بۆ گروپی تێلەگرام
$telegram_api = "https://api.telegram.org/bot7286061251:AAEjEI8uhp0K8yw0Gg_ooq2NYA9J4Z1tJJ8";
$telegram_chat_id = "-1002256776178";

$message_status = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Send pending tasks to Telegram group
    if (isset($_POST['send_pending_tasks'])) {
        $query = "SELECT * FROM tasks WHERE status IN ('چاوەڕوانی', 'دەستپێکراوە')";
        $result = mysqli_query($conn, $query);

        $message = "📌 **ئەرکە تەواو نەکراوەکان:**\n\n";
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $message .= "\n🔹 ئەرک: **" . $row['task_name'] . "**\n📍 شوێن: " . $row['location'] . "\n🔢 ژمارە: " . $row['task_number'] . "\n📅 بەروار: " . $row['date'] . "\n👥 تیم: " . $row['team'] . "\n📂 هاوپێچ: " . $row['files'];
            }

            $response = file_get_contents("$telegram_api/sendMessage?chat_id=$telegram_chat_id&text=" . urlencode($message));
            $response_data = json_decode($response, true);

            if ($response === false || !$response_data['ok']) {
                $message_status = "❌ هەڵەی ناردنی پەیام بۆ تێلەگرام: " . $response_data['description'];
            } else {
                $message_status = "✅ پەیامەکان بۆ گروپ نێردرا.";
            }

        } else {
            $message_status = "✅ هیچ ئەرکە تەواو نەکراو نییە.";
        }
    }

    // Send custom message with optional file
    if (isset($_POST['send_custom_message'])) {
        $name = $_POST['name'];
        $message = $_POST['message'];

        $text = "👤 نێوی ناردەر: $name\n\n✉️ پەیام: $message";

        $response = file_get_contents("$telegram_api/sendMessage?chat_id=$telegram_chat_id&text=" . urlencode($text));
        $response_data = json_decode($response, true);

        if ($response === false || !$response_data['ok']) {
            $message_status = "❌ هەڵەی ناردنی پەیام بۆ تێلەگرام: " . $response_data['description'];
        } else {
            $message_status = "✅ پەیامی تایبەتی نێردرا.";
        }

        if (!empty($_FILES['file']['name'])) {
            $file_tmp = $_FILES['file']['tmp_name'];
            $file_name = basename($_FILES['file']['name']);
            $upload_path = "uploads/$file_name";

            if (!is_dir("uploads")) {
                mkdir("uploads", 0777, true);
            }

            if (move_uploaded_file($file_tmp, $upload_path)) {
                $document = new CURLFile(realpath($upload_path));
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "$telegram_api/sendDocument");
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, [
                    "chat_id" => $telegram_chat_id,
                    "document" => $document
                ]);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $file_result = curl_exec($ch);
                $file_response_data = json_decode($file_result, true);
                curl_close($ch);

                if ($file_result === false || !$file_response_data['ok']) {
                    $message_status .= "\n❌ نەتوانرا فایل نێردرێت: " . $file_response_data['description'];
                } else {
                    $message_status .= "\n✅ فایلەکە نێردرا.";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ku" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🤖 بۆتی تیلیگرام - O_Data</title>

    <!-- Bootstrap RTL & TailwindCSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- فۆنتی Zain -->
    <style>
        @font-face {
            font-family: 'Zain';
            src: url('../fonts/Zain.ttf') format('truetype');
        }

        body {
            font-family: 'Zain', sans-serif !important;
            background: linear-gradient(135deg, #dee8ff, #f5f7fa);
            direction: rtl;
        }

        .glass {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 1.5rem;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.2);
            padding: 1.5rem;
        }

        .btn-telegram {
            background-color: #4F46E5;
            color: #fff;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .btn-telegram:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.4);
        }
    </style>
</head>
<body class="flex flex-col min-h-screen p-4">

    <!-- Header -->
    <header class="glass max-w-4xl w-full mx-auto mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-indigo-700 animate-pulse">🤖 بۆتی تیلیگرام</h1>
        <a href="../views/dashboard.php" class="btn btn-danger text-white rounded-pill">🏠 گەڕانەوە</a>
    </header>

    <!-- Status Message -->
    <?php if (!empty($message_status)) : ?>
        <div class="alert alert-info text-center glass"><?= $message_status; ?></div>
    <?php endif; ?>

    <!-- Pending Tasks Sender -->
    <section class="glass max-w-4xl w-full mx-auto mb-6 text-center space-y-3">
        <h2 class="text-xl font-bold text-indigo-600">📋 ناردنی ئەرکە تەواو نەکراوەکان</h2>
        <form method="POST">
            <button type="submit" name="send_pending_tasks" class="btn-telegram px-6 py-3 rounded-pill">📤 ناردن بۆ گروپ</button>
        </form>
    </section>

    <!-- Custom Message Sender -->
    <section class="glass max-w-4xl w-full mx-auto mb-6 text-center space-y-3">
        <h2 class="text-xl font-bold text-indigo-600">✉️ ناردنی پەیامی تایبەتی</h2>
        <form method="POST" enctype="multipart/form-data" class="space-y-4">
            <input type="text" name="name" class="form-control rounded-pill text-center" placeholder="👤 بۆ" required>
            <textarea name="message" class="form-control rounded-2xl text-center" rows="4" placeholder="✉️ پەیام" required></textarea>
            <input type="file" name="file" class="form-control rounded-pill text-center">
            <button type="submit" name="send_custom_message" class="btn-telegram px-6 py-3 rounded-pill">📤 ناردنی پەیام</button>
        </form>
    </section>

    <!-- Footer -->
    <footer class="text-center mt-12 text-gray-600 text-sm">
        &copy; <?= date('Y'); ?> O_Data - هەموو مافەکان پارێزراون
    </footer>

</body>
</html>