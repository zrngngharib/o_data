<?php
session_start();
include '../../includes/db.php';

// ئەگەر بەکارهێنەر نەچووە ژوورەوە، بۆ login.php بۆنێرە
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
        $_SESSION['report_data'] = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $_SESSION['report_data'][] = $row;
        }
    } elseif (isset($_POST['export_excel'])) {
        if (isset($_SESSION['report_data'])) {
            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=report.xls");

            echo "ID\tئەرك\tژمارە\tشوێن\tکارمەند\tژمارە مۆبایل\tتیم\tحاڵەت\tنرخ\tدراو\tبەروار\tبەرواری تەواو کردن\n";
            foreach ($_SESSION['report_data'] as $row) {
                echo "{$row['id']}\t{$row['task_name']}\t{$row['task_number']}\t{$row['location']}\t{$row['employee']}\t{$row['mobile_number']}\t{$row['team']}\t{$row['status']}\t{$row['cost']}\t{$row['currency']}\t{$row['date']}\t{$row['completed_date']}\n";
            }
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ku" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>ڕاپۆرتی ئەركەکان 📊</title>

    <!-- Importing Zain Font -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Zain:ital,wght@0,400;0,700;1,400&display=swap');

        * {
            box-sizing: border-box;
        }

        body {
            direction: rtl;
            font-family: 'Zain', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #e6f0fa;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 50px auto;
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 123, 255, 0.2);
        }

        h1 {
            text-align: center;
            color: #007bff;
            margin-bottom: 30px;
            font-weight: 700;
        }

        form {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
            margin-bottom: 25px;
        }

        input[type="date"] {
            padding: 12px 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            width: 200px;
            font-family: 'Zain', sans-serif;
        }

        button {
            font-family: 'Zain', sans-serif;
            display: inline-block;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            color: white;
            text-align: center;
            background-color: #007bff;
            transition: background-color 0.3s ease, transform 0.3s ease;
            border: none;
            cursor: pointer;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .button-container {
            display: flex;
            justify-content: center; /* Center the buttons */
            gap: 15px; /* Add some space between buttons */
            margin-bottom: 20px;
        }
        .button:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }

        .table-container {
            overflow-x: auto;
            border-radius: 10px;
            border: 1px solid #ccc;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 900px;
        }

        thead {
            background-color: #007bff;
            color: white;
        }

        th, td {
            padding: 15px 12px;
            text-align: center;
            font-size: 15px;
        }

        th {
            font-weight: 600;
        }

        tbody tr:nth-child(even) {
            background-color: #f4f9ff;
        }

        tbody tr:hover {
            background-color: #e2efff;
        }

        @media screen and (max-width: 768px) {
            input[type="date"], button {
                width: 100%;
            }

            table {
                font-size: 14px;
            }

            th, td {
                padding: 10px 8px;
            }
        }

        .no-data {
            text-align: center;
            color: #dc3545;
            margin-top: 15px;
            font-size: 16px;
        }

    </style>
</head>
<body>

<div class="container">
    <h1>ڕاپۆرتی ئەركەکان 📊</h1>

    <form method="POST">
        <input type="date" name="date_from" required placeholder="لە ڕێکەوت">
        <input type="date" name="date_to" required placeholder="بۆ ڕێکەوت">
        <button type="submit" name="generate_report">ڕاپۆرت ئامادە بکە 📋</button>
        <button type="submit" name="export_excel">داگرتن بۆ ئێکسێل 📊</button>
    </form>

    <?php if (isset($_POST['generate_report']) && !empty($_SESSION['report_data'])): ?>
        <div class="table-container">
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>ئەرك</th>
                    <th>ژمارە</th>
                    <th>شوێن</th>
                    <th>کارمەند</th>
                    <th>ژمارە مۆبایل</th>
                    <th>تیم</th>
                    <th>حاڵەت</th>
                    <th>نرخ</th>
                    <th>دراو</th>
                    <th>بەروار</th>
                    <th>بەرواری تەواو کردن</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($_SESSION['report_data'] as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['task_name']) ?></td>
                        <td><?= htmlspecialchars($row['task_number']) ?></td>
                        <td><?= htmlspecialchars($row['location']) ?></td>
                        <td><?= htmlspecialchars($row['employee']) ?></td>
                        <td><?= htmlspecialchars($row['mobile_number']) ?></td>
                        <td><?= htmlspecialchars($row['team']) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td><?= htmlspecialchars($row['cost']) ?></td>
                        <td><?= htmlspecialchars($row['currency']) ?></td>
                        <td><?= htmlspecialchars($row['date']) ?></td>
                        <td><?= htmlspecialchars($row['completion_date']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php elseif (isset($_POST['generate_report'])): ?>
        <p class="no-data">هیچ داتایەک نەدۆزرایەوە! 😕</p>
    <?php endif; ?>

</div>

</body>
</html>
