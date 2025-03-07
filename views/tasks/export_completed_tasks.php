<?php
session_start();
include '../../includes/db.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

// ڕوونکردنەوەی هەڵەکان
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// فلتەرکردنی بەروار
$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : '';
$to_date = isset($_GET['to_date']) ? $_GET['to_date'] : '';

$query = "SELECT * FROM tasks WHERE status = 'Completed'";

if (!empty($from_date) && !empty($to_date)) {
    $query .= " AND date BETWEEN '$from_date' AND '$to_date'";
}

$query .= " ORDER BY date DESC"; // ڕیزبەندی نوێترین بۆ کۆنترین

$result = mysqli_query($conn, $query);
if (!$result) {
    die("کێشە لە ناردنی داتا: " . mysqli_error($conn));
}

// دروستکردنی فایلی ئێکسڵ
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="completed_tasks.xls"');
header('Cache-Control: max-age=0');

echo "Task Name\tTask Number\tLocation\tEmployee\tMobile Number\tTeam\tStatus\tCost\tCurrency\tDate\n";

while ($row = mysqli_fetch_assoc($result)) {
    echo "{$row['task_name']}\t{$row['task_number']}\t{$row['location']}\t{$row['employee']}\t{$row['mobile_number']}\t{$row['team']}\t{$row['status']}\t{$row['cost']}\t{$row['currency']}\t{$row['date']}\n";
}

exit();
?>