<?php
session_start();
include '../includes/db.php'; // Include database connection

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['id'])) {
    $task_id = (int)$_GET['id'];

    // Fetch the task details
    $query = "SELECT * FROM tasks WHERE id = $task_id";
    $result = mysqli_query($conn, $query);
    $task = mysqli_fetch_assoc($result);

    if ($task) {
        // Prepare to insert a copy of the task
        $task_name = $task['task_name'];
        $task_number = $task['task_number'];
        $location = $task['location'];
        $employee = $task['employee'];
        $mobile_number = $task['mobile_number'];
        $team = $task['team'];
        $status = $task['status'];
        $cost = $task['cost'];
        $currency = $task['currency'];
        $date = $task['date'];

        // Insert the copied task into the database
        $insert_query = "INSERT INTO tasks (task_name, task_number, location, employee, mobile_number, team, status, cost, currency, date) 
                         VALUES ('$task_name', '$task_number', '$location', '$employee', '$mobile_number', '$team', '$status', '$cost', '$currency', '$date')";
        
        if (mysqli_query($conn, $insert_query)) {
            header('Location: tasks.php?message=Task copied successfully');
            exit();
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } else {
        echo "Task not found.";
    }
} else {
    echo "No task ID provided.";
}
?>