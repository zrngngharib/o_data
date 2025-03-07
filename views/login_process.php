<?php
session_start();
include '../includes/db.php'; // ڕێڕەوی دروست بۆ `db.php`

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        echo "<script>alert('تکایە هەموو خانەکان پڕبکەرەوە!'); window.location.href='../views/login.php';</script>";
        exit();
    }

    // زانیاری کەیسی نەکەرەوە (`username` هەمیشە بچووك دەکەین)
    $username = strtolower($username);

    // لێکچوونی بەکارهێنەر لە داتابەیس
    $query = "SELECT * FROM users WHERE username = ? AND password = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ss", $username, $password);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['user'] = $row['username'];
        $_SESSION['role'] = $row['role'];

        if (isset($_POST['remember_me'])) {
            setcookie('username', $username, time() + (86400 * 30), "/"); // 30 ڕۆژ
            setcookie('password', $password, time() + (86400 * 30), "/"); // 30 ڕۆژ
        }

        header("Location: ../views/dashboard.php");
        exit();
    } else {
        echo "<script>alert('ناوی بەکارهێنەر یان وشەی تێپەڕ هەڵەیە!'); window.location.href='login.php';</script>";
    }
}
?>
