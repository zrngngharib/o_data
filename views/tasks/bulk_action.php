
<?php
session_start();
include_once('../../includes/db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['selected_tasks'])) {
    $selected_tasks = $_POST['selected_tasks'];
    $action = $_POST['action'];

    if ($action == 'delete') {
        $ids = implode(',', $selected_tasks);
        $query = "DELETE FROM tasks WHERE id IN ($ids)";
        mysqli_query($conn, $query);
    } elseif ($action == 'complete') {
        $ids = implode(',', $selected_tasks);
        $query = "UPDATE tasks SET status='Completed', completion_date=NOW() WHERE id IN ($ids)";
        mysqli_query($conn, $query);
    }
}

header('Location: ../tasks.php');
exit();
?>