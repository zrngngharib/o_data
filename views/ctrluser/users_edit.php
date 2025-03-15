<?php
session_start();
include '../../includes/db.php';

// Protection
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit();
}

// Admin access only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "<script>alert('ID دیاری نەکرا!'); window.location.href='users.php';</script>";
    exit();
}

$id = intval($_GET['id']);
$query = "SELECT * FROM users WHERE id = $id";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) != 1) {
    echo "<script>alert('بەکارهێنەر نەدۆزرایەوە!'); window.location.href='users.php';</script>";
    exit();
}

$user = mysqli_fetch_assoc($result);

// Process update
if (isset($_POST['update_user'])) {
    $username = strtolower(trim($_POST['username']));
    $role = trim($_POST['role']);
    $password = trim($_POST['password']);
    $status = trim($_POST['status']);

    if (empty($username) || empty($role) || empty($status)) {
        echo "<script>alert('تکایە ناو، ڕۆڵ و حاڵەت پڕبکەرەوە!');</script>";
    } else {
        $update_query = "UPDATE users SET username = '$username', role = '$role', status = '$status'";

        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $update_query .= ", password = '$hashed_password'";
        }

        $update_query .= " WHERE id = $id";

        if (mysqli_query($conn, $update_query)) {
            echo "<script>alert('بەکارهێنەر نوێکرایەوە!'); window.location.href='users.php';</script>";
            exit();
        } else {
            echo "<script>alert('هەڵە ڕوویدا!');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ku" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>دەستکاری بەکارهێنەر - O_Data</title>

    <!-- Bootstrap RTL & Tailwind -->
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
            background-color: #dee8ff;
            background-image: linear-gradient(135deg, #dee8ff, #f5f7fa);
        }

        .glass {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 2rem;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.2);
            padding: 2rem;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">

    <!-- Main Card -->
    <div class="glass max-w-md w-full animate-fade-in space-y-6">

        <!-- Header -->
        <div class="text-center">
            <h1 class="text-3xl font-bold text-indigo-700 animate-pulse"> دەستکاری بەکارهێنەر</h1>
            <p class="text-sm mt-2 text-gray-700">زانیاری بەکارهێنەر نوێ بکەوە</p>
        </div>

        <!-- Form -->
        <form method="POST" class="space-y-4">

            <!-- Username -->
            <div>
                <label class="form-label text-sm">ناوی بەکارهێنەر</label>
                <input type="text" name="username" value="<?= htmlspecialchars($user['username']); ?>" class="form-control rounded-pill py-2 px-3" required>
            </div>

            <!-- Role -->
            <div>
                <label class="form-label text-sm">ڕۆڵ</label>
                <select name="role" class="form-select rounded-pill py-2 px-3" required>
                    <option value="User" <?= ($user['role'] == 'User') ? 'selected' : ''; ?>>User</option>
                    <option value="Admin" <?= ($user['role'] == 'Admin') ? 'selected' : ''; ?>>Admin</option>
                </select>
            </div>

            <!-- Status -->
            <div>
                <label class="form-label text-sm">حاڵەت</label>
                <select name="status" class="form-select rounded-pill py-2 px-3" required>
                    <option value="active" <?= ($user['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?= ($user['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                    <option value="banned" <?= ($user['status'] == 'banned') ? 'selected' : ''; ?>>Banned</option>
                </select>
            </div>

            <!-- Password -->
            <div class="relative">
                <label class="form-label text-sm">وشەی نهێنی (ئەگەر دەتەوێت نوێبکەیتەوە)</label>
                <input type="password" id="password" name="password" class="form-control rounded-pill py-2 px-3">
                <button type="button" onclick="togglePassword()" class="absolute top-50 end-0 translate-middle-y px-3 py-1 text-indigo-700 hover:text-indigo-900 text-sm">👁</button>
            </div>

            <!-- Submit Button -->
            <button type="submit" name="update_user" class="btn w-100 text-white rounded-pill shadow-md transition-transform hover:scale-105" style="background-color: #4F46E5;">
                💾 نوێکردنەوە
            </button>

        </form>

        <!-- Back Button -->
        <div class="text-center mt-4">
            <a href="users.php" class="btn btn-secondary rounded-pill">⬅️ گەڕانەوە بۆ بەکارهێنەران</a>
        </div>
    </div>

    <!-- Show Password Toggle -->
    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
            } else {
                passwordField.type = 'password';
            }
        }
    </script>

</body>
</html>
