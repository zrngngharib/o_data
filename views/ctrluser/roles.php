<?php
session_start();
include_once('../../includes/db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit();
}

// Process update role (Optional, Processing logic - OK)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id']);
    $role_id = intval($_POST['role_id']);

    $update = "UPDATE users SET role_id = $role_id WHERE id = $user_id";
    mysqli_query($conn, $update);

    echo "<script>alert('✅ ڕۆڵ نوێکرایەوە!'); window.location.href='roles.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="ku" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ڕۆڵەکان - O_Data</title>

    <!-- Bootstrap RTL & TailwindCSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- فۆنتی Zain -->
    <style>
        @font-face {
            font-family: 'Zain';
            src: url('../../fonts/Zain.ttf') format('truetype');
        }

        body {
            font-family: 'Zain', sans-serif;
            background: linear-gradient(135deg, #dee8ff, #f5f7fa);
        }

        .glass {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 2rem;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.2);
            padding: 2rem;
        }

        label {
            font-size: 0.9rem;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">

    <div class="glass max-w-md w-full animate-fade-in space-y-6">

        <!-- Title -->
        <div class="text-center">
            <h1 class="text-3xl font-bold text-indigo-700 animate-pulse">🛡️ بەڕێوەبردنی ڕۆڵەکان</h1>
            <p class="text-sm mt-2 text-gray-600">بەکارهێنەران و ڕۆڵەکانیان نوێ بکەوە</p>
        </div>

        <!-- Form Start -->
        <form method="POST" class="space-y-4">

            <!-- Select User -->
            <div>
                <label class="form-label text-sm">دیاری بکە بەکارهێنەر</label>
                <select name="user_id" class="form-select rounded-pill py-2 px-3" required>
                    <option value="">-- هەلبژێرە --</option>
                    <?php
                    $users = mysqli_query($conn, "SELECT * FROM users");
                    while ($user = mysqli_fetch_assoc($users)) {
                        echo "<option value='{$user['id']}'>{$user['username']}</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Select Role -->
            <div>
                <label class="form-label text-sm">دیاری بکە ڕۆڵ</label>
                <select name="role_id" class="form-select rounded-pill py-2 px-3" required>
                    <option value="">-- هەلبژێرە --</option>
                    <?php
                    $roles = mysqli_query($conn, "SELECT * FROM roles");
                    while ($role = mysqli_fetch_assoc($roles)) {
                        echo "<option value='{$role['id']}'>{$role['role_name']}</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn w-100 text-white rounded-pill shadow-md transition-transform hover:scale-105" style="background-color: #4F46E5;">
                💾 نوێکردنەوەی ڕۆڵ
            </button>

        </form>

        <!-- Back Button -->
        <div class="text-center mt-4 flex justify-center gap-2">
            <a href="users.php" class="btn btn-secondary rounded-pill">⬅️ گەڕانەوە بۆ بەکارهێنەران</a>
        </div>

    </div>

</body>
</html>
