<?php
session_start();
include '../includes/db.php'; // Include database connection

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['id'])) {
    $task_id = (int)$_GET['id'];

    // Prepare and execute the delete statement
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ?");
    $stmt->bind_param("i", $task_id);

    if ($stmt->execute()) {
        // Redirect to tasks page with success message
        header('Location: ../views/tasks.php?message=Task deleted successfully');
    } else {
        // Redirect to tasks page with error message
        header('Location: ../views/tasks.php?message=Error deleting task');
    }

    $stmt->close();
} else {
    // Redirect to tasks page if no ID is provided
    header('Location: ../views/tasks.php');
}
?>