<?php
session_start();
include '../../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "<script>alert('ID دیاری نەکرا!'); window.location.href='users.php';</script>";
    exit();
}

$id = intval($_GET['id']);

// DELETE query
$query = "DELETE FROM users WHERE id = $id";
if (mysqli_query($conn, $query)) {
    echo "<script>alert('بەکارهێنەر سڕایەوە!'); window.location.href='users.php';</script>";
} else {
    echo "<script>alert('هەڵە ڕوویدا لە سڕینەوە!'); window.location.href='users.php';</script>";
}
?>
