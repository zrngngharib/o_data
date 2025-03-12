<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include_once('../../includes/db.php');


if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "Form submitted"; // Debugging line
    $username = trim(mysqli_real_escape_string($conn, $_POST['username']));
    $mobile = trim(mysqli_real_escape_string($conn, $_POST['mobile']));
    $password = $_POST['password'];
    $role = $_POST['role'];
    $status = $_POST['status'];

    echo "Form data: $username, $mobile, $password, $role, $status"; // Debugging line

    $check = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
    if (mysqli_num_rows($check) > 0) {
        echo "<script>alert('❌ Username already exists!'); window.location.href='users.php';</script>";
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $query = "INSERT INTO users (username, mobile, password, role, status)
              VALUES ('$username', '$mobile', '$hashed_password', '$role', '$status')";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('✅ User added successfully!'); window.location.href='users.php';</script>";
    } else {
        echo "<script>alert('❌ Failed to add user!'); window.location.href='users.php';</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="ku" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>زیادکردنی بەکارهێنەر - O_Data</title>

    <!-- TailwindCSS & Bootstrap RTL -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Zain Font -->
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
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(12px);
            border-radius: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            padding: 2rem;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">

    <!-- Container -->
    <div class="glass max-w-md w-full space-y-6">

        <!-- Header -->
        <div class="text-center">
            <h1 class="text-3xl font-bold text-indigo-700 animate-pulse">➕ زیادکردنی بەکارهێنەر</h1>
            <p class="text-sm mt-2">زانیاری بەکارهێنەرەکە پڕبکەرەوە</p>
        </div>

        <!-- Form -->
        <form action="users_add.php" method="POST" class="space-y-4">

            <!-- Username -->
            <div>
                <label class="form-label text-sm">ناوی بەکارهێنەر</label>
                <input type="text" name="username" class="form-control rounded-pill py-2 px-3" required>
            </div>

            <!-- Password -->
            <div>
                <label class="form-label text-sm">وشەی نهێنی</label>
                <input type="password" name="password" class="form-control rounded-pill py-2 px-3" required>
            </div>

            <!-- Role -->
            <div>
                <label class="form-label text-sm">ڕۆڵ</label>
                <select name="role" class="form-select rounded-pill py-2 px-3" required>
                    <option value="User">User</option>
                    <option value="Admin">Admin</option>
                </select>
            </div>

            <!-- Submit Button -->
            <button type="submit" name="add_user" class="btn w-100 text-white rounded-pill shadow-md transition-transform hover:scale-105" style="background-color: #4F46E5;">
                💾 پاشەکەوتکردن
            </button>

        </form>

        <!-- Back Button -->
        <div class="text-center mt-4">
            <a href="users.php" class="btn btn-secondary rounded-pill">⬅️ گەڕانەوە بۆ لیستی بەکارهێنەران</a>
        </div>

    </div>

</body>
</html>