<?php
session_start();
include '../../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $task_name = $_POST['task_name'] ?? '';
    $task_number = $_POST['task_number'] ?? '';
    $location = $_POST['location'] ?? '';
    $employee = implode(',', $_POST['employee'] ?? []);
    $mobile_number = $_POST['mobile_number'] ?? '';
    $team = $_POST['team'] ?? 'تەکنیکی';
    $status = $_POST['status'] ?? 'چاوەڕوانی';
    $cost = $_POST['cost'] ?? '';
    $currency = $_POST['currency'] ?? 'IQD';
    $date = $_POST['date'] ?? date('Y-m-d H:i:s');
    $date = date('Y-m-d H:i:s', strtotime($date));

    $uploadedFiles = $_POST['uploaded_files'] ?? ''; // URLs from Cloudinary!

    $completion_date = null;
    if ($status === 'تەواوکراوە') {
        $completion_date = date('Y-m-d H:i:s');
    }

    $query = "INSERT INTO tasks (task_name, task_number, location, employee, mobile_number, team, status, cost, currency, date, files, completion_date)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($db, $query);

    mysqli_stmt_bind_param($stmt, 'ssssssssssss', 
        $task_name, $task_number, $location, $employee, $mobile_number,
        $team, $status, $cost, $currency, $date, $uploadedFiles, $completion_date
    );

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => mysqli_error($db)
        ]);
    }

    mysqli_stmt_close($stmt);
}
?>