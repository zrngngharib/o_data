<?php
session_start();
include '../includes/db.php'; // Include database connection

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['selected_tasks']) && is_array($_POST['selected_tasks'])) {
        $selected_tasks = $_POST['selected_tasks'];
        $action = $_POST['action'];

        if ($action === 'delete') {
            $ids = implode(',', array_map('intval', $selected_tasks));
            $query = "DELETE FROM tasks WHERE id IN ($ids)";
            mysqli_query($conn, $query);
            header('Location: ../views/tasks.php?message=Tasks deleted successfully');
            exit();
        } elseif ($action === 'complete') {
            $ids = implode(',', array_map('intval', $selected_tasks));
            $query = "UPDATE tasks SET status = 'Completed' WHERE id IN ($ids)";
            mysqli_query($conn, $query);
            header('Location: ../views/tasks.php?message=Tasks marked as completed');
            exit();
        }
    }
}

header('Location: ../views/tasks.php?error=No tasks selected');
exit();
?>