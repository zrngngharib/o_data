<?php
session_start();
include_once('../includes/db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="report.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['Status', 'Total']);

$query_all = "SELECT status, COUNT(*) as total FROM tasks GROUP BY status";
$result_all = mysqli_query($db, $query_all);

while ($row = mysqli_fetch_assoc($result_all)) {
    fputcsv($output, [$row['status'], $row['total']]);
}

fclose($output);
exit();
?>