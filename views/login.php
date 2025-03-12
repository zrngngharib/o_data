<?php
session_start();
include_once('../includes/db.php');

session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username = '$username' AND status = 'active'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            if (isset($_POST['remember_me'])) {
                setcookie('remember_me', $user['id'], time() + (30 * 24 * 60 * 60), "/");
            }

            header("Location: dashboard.php");
            exit();
        } else {
            $error = "❌ وشەی نهێنی هەڵە!";
        }
    } else {
        $error = "❌ بەکارهێنەر نەدۆزرایەوە!";
    }
}
?>
<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ku" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>چوونەژوورەوە - O_Data</title>
    
    <!-- TailwindCSS & Bootstrap RTL -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Zain Font -->
    <style>
        @font-face {
            font-family: 'Zain';
            src: url('../fonts/Zain.ttf') format('truetype');
        }
        body {
            font-family: 'Zain', sans-serif;
            background: linear-gradient(135deg, #dee8ff 0%, #f5f7fa 100%);
        }
        .glass {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            border-radius: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            padding: 2rem;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">

    <div class="glass max-w-md w-full space-y-6 animate-fade-in">
        
        <!-- Logo + Header -->
        <div class="text-center">
            <h1 class="text-4xl font-bold text-indigo-700 animate-pulse">📊 O_Data</h1>
            <p class="text-lg mt-2">تکایە چوونەژوورەوە</p>
        </div>
        
        <!-- Login Form -->
        <form action="login.php" method="POST" class="space-y-4">
            
            <!-- Username -->
            <div>
                <label class="form-label text-sm">ناوی بەکارهێنەر</label>
                <input type="text" name="username" class="form-control rounded-pill py-2 px-3" required>
            </div>

            <!-- Password -->
            <div class="relative">
                <label class="form-label text-sm">وشەی نهێنی</label>
                <input type="password" id="password" name="password" class="form-control rounded-pill py-2 px-3" required>
                
                <!-- Show/Hide Password -->
                <button type="button" onclick="togglePassword()" class="absolute top-50 end-0 translate-middle-y px-3 py-1 text-sm text-indigo-700 hover:text-indigo-900">
                    👁
                </button>
            </div>

            <!-- Remember Me -->
            <div class="form-check mb-2">
                <input type="checkbox" class="form-check-input" id="rememberMe" name="remember_me">
                <label class="form-check-label text-sm" for="rememberMe">بیرم بگرەوە</label>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn w-100 text-white rounded-pill shadow-md transition-transform hover:scale-105" style="background-color: #4F46E5;">
                چوونەژوورەوە
            </button>
        </form>

        <!-- Back Link -->
        <div class="text-center mt-3">
            <a href="../index.php" class="text-sm text-indigo-700 hover:underline">گەڕانەوە بۆ پەڕەی سەرەکی</a>
        </div>
    </div>

    <!-- JS Scripts -->
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
            } else {
                passwordInput.type = "password";
            }
        }
    </script>

</body>
</html>