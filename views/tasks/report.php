<?php
session_start();
include '../../includes/db.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date_from = $_POST['date_from'];
    $date_to = $_POST['date_to'];

    if (empty($date_from) || empty($date_to)) {
        $query = "SELECT * FROM tasks";
    } else {
        $query = "SELECT * FROM tasks WHERE date BETWEEN '$date_from' AND '$date_to'";
    }
    $result = mysqli_query($conn, $query);

    if (isset($_POST['generate_report'])) {
        // Store the result in session
        $_SESSION['report_data'] = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $_SESSION['report_data'][] = $row;
        }
    } elseif (isset($_POST['export_excel'])) {
        if (isset($_SESSION['report_data'])) {
            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=report.xls");
            echo "ID\tئەرك\tژمارە\tشوێن\tکارمەند\tژمارە مۆبایل\tتیم\tحاڵەت\tنرخ\tبەروار\n";
            foreach ($_SESSION['report_data'] as $row) {
                echo "{$row['id']}\t{$row['number']}\t{$row['location']}\t{$row['employee']}\t{$row['mobile']}\t{$row['team']}\t{$row['status']}\t{$row['cost']}\t{$row['currency']}\t{$row['date']}\n";
            }
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>ڕاپۆرت 📊</title>
</head>
<body>
    <form method="POST">
        لە: <input type="date" name="date_from" > 📅
        بۆ: <input type="date" name="date_to" > 📅
        <button type="submit" name="export_excel">داگرن بۆ ئێکسێل 📊</button> <!-- داگرن بۆ ئێکسێل -->
        <button type="submit" name="generate_report">ڕاپۆرت ئامادە بکە 📋</button> <!-- ڕاپۆرت ئامادە بکە -->
    </form>
    <?php
    if (isset($_POST['generate_report'])) {
        // ڕاپۆرت نیشان بدە
        echo "<div style='max-height: 400px; overflow-y: auto;'>";
        echo "<table border='1'>
                <tr>
                    <th>ID</th>
                    <th>ئەرك 📝</th>
                    <th>ژمارە 🔢</th>
                    <th>شوێن 📍</th>
                    <th>کارمەند 👤</th>
                    <th>ژمارە مۆبایل 📱</th>
                    <th>تیم 👥</th>
                    <th>حاڵەت 📊</th>
                    <th>نرخ 💰</th>
                    <th>دراو 💰</th>
                    <th>بەروار 📅</th>
                    <th>بەرواری تەواو کردن 📅</th>
                </tr>";
        foreach ($_SESSION['report_data'] as $row) {
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['task_name']}</td>
                    <td>{$row['task_number']}</td>
                    <td>{$row['location']}</td>
                    <td>{$row['employee']}</td>
                    <td>{$row['mobile_number']}</td>
                    <td>{$row['team']}</td>
                    <td>{$row['status']}</td>
                    <td>{$row['cost']}</td>
                    <td>{$row['currency']}</td>
                    <td>{$row['date']}</td>
                    <td>{$row['completed_date']}</td>
                  </tr>";
        }
        echo "</table>";
        echo "</div>";
    }
    ?>
</body>
</html>