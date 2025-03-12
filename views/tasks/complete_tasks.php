
<?php
session_start();
include_once('../../includes/db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}


if (isset($_GET['id'])) {
    $task_id = $_GET['id'];
    $completion_date = date('Y-m-d H:i:s'); // داتەی ئێستا

    $query = "UPDATE tasks SET status = 'Completed', completion_date = '$completion_date' WHERE id = $task_id";
    mysqli_query($conn, $query);

    header('Location: ../views/completed_tasks.php');
    exit();
}
