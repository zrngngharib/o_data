<?php
session_start();
include_once dirname(__DIR__, 2) . '/includes/db.php';

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

$query = "SELECT *, DATEDIFF(completion_date, date) AS days_to_complete FROM tasks WHERE status = 'Completed'";

if (!empty($from_date) && !empty($to_date)) {
    $query .= " AND date BETWEEN '$from_date' AND '$to_date'";
}

$query .= " ORDER BY date DESC"; // ڕیزبەندی نوێترین بۆ کۆنترین

$result = mysqli_query($conn, $query);
if (!$result) {
    die("کێشە لە ناردنی داتا: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="ku">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>کارە تەواوبووەکان ✅</title>
    <link rel="stylesheet" href="../styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            direction: rtl;
            font-family: 'Zain', sans-serif;
            background-color: #f9fafb;
        }
        .container {
            width: 100%;
            text-align: center;
            padding: 10px 15px;
            margin-bottom: 200px;
        }
        h1 {
            color: #007bff;
        }
        .filter-form {
            width: 100%;
            justify-content: center;
            gap: 20px;
            padding: 1px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            direction: rtl;
        }
        th, td {
            padding: 12px;
            text-align: right;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #007bff;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .btn {
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            margin: 5px;
            font-size: 16px;
        }
        .btn-filter {
            background-color: #0d6efd;
            color: white;
        }
        .btn-reset {
            background-color: #dc3545;
            color: white;
        }
        .btn-export {
            background-color: #0d6efd;
            color: white;
        }
        .btn:hover {
            opacity: 0.8;
        }
        .fab {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #0d6efd;
            color: white;
            padding: 15px;
            border-radius: 50%;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            font-size: 24px;
        }
        .fab:hover {
            background: #0d6efd;
        }
        @media screen and (max-width: 768px) {
            table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
        }
        /* بەپێی بۆ پشتگیری Light & Dark Mode */
        :root {
            --popup-bg: white;
            --popup-text: #333;
            --popup-shadow: rgba(0, 0, 0, 0.2);
            --popup-hover: #0056b3;
        }

        @media (prefers-color-scheme: dark) {
        :root {
                --popup-bg: #222;
                --popup-text: white;
                --popup-shadow: rgba(255, 255, 255, 0.2);
                --popup-hover: #00b3ff;
            }
        }

        /* پۆپ‌ئەپ (Modal) */
        .popup {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            animation: fadeIn 0.3s ease-in-out;
        }

        /* بۆکسەکە */
        .popup-content {
            background: var(--popup-bg);
            color: var(--popup-text);
            padding: 20px;
            border-radius: 12px;
            width: 90%;
            max-width: 450px;
            text-align: right;
            position: relative;
            box-shadow: 0 4px 12px var(--popup-shadow);
            animation: scaleUp 0.3s ease-in-out;
        }

        /* ئەنیمەیشنی خستنە ناو */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes scaleUp {
            from { transform: scale(0.9); }
            to { transform: scale(1); }
        }

        /* دوگمەی داخستن */
        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 22px;
            cursor: pointer;
            color: var(--popup-text);
            background: transparent;
            border: none;
            transition: color 0.2s ease-in-out;
        }

        .close-btn:hover {
            color: var(--popup-hover);
        }

        /* ستایلەکانی نیشانی کار */
        .task-info {        
            padding: 8px 0;
            border-bottom: 1px solid #ddd;
            font-size: 16px;
        }
        .task-info:last-child {
            border-bottom: none;
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 24px;
            cursor: pointer;
            color: #333;
        }

        .btn-view {
            background-color: #0d6efd;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-view:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>کارە تەواوبووەکان ✅</h1>

    <form method="GET" action="" class="filter-form">
        <label>بەروار لە :</label>
        <input type="date" name="from_date" value="<?= htmlspecialchars($from_date) ?>">
        <label> بۆ :</label>
        <input type="date" name="to_date" value="<?= htmlspecialchars($to_date) ?>">
        <button type="submit" class="btn btn-filter">فلتەرکردن 🔍</button>
        <button type="button" class="btn btn-reset" onclick="window.location.href='completed_tasks.php'">هەڵوەشاندنەوەی فلتەر 🔄</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>ئەرك</th>
                <th>ژمارە</th>
                <th>شوێن</th>
                <th>کارمەند</th>
                <th>ژمارە مۆبایل </th>
                <th>تیم</th>
                <th>حاڵەت</th>
                <th>نرخ</th>
                <th>بەروار</th>
                <th>تەواوکردن</th>
                <th>(ڕۆژ)</th>
                <th>بینین</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td><?= htmlspecialchars($row['task_name']) ?></td>
                <td><?= htmlspecialchars($row['task_number']) ?></td>
                <td><?= htmlspecialchars($row['location']) ?></td>
                <td><?= htmlspecialchars($row['employee']) ?></td>
                <td><?= htmlspecialchars($row['mobile_number']) ?></td>
                <td><?= htmlspecialchars($row['team']) ?></td>
                <td><?= htmlspecialchars($row['status']) ?></td>
                <td><?= htmlspecialchars($row['cost']) ?> <?= htmlspecialchars($row['currency']) ?></td>
                <td><?= htmlspecialchars($row['date']) ?></td>
                <td><?= htmlspecialchars($row['completion_date']) ?></td>
                <td><?= htmlspecialchars($row['days_to_complete']) ?></td>
                <td><button class="btn-view" onclick="showDetails(<?= htmlspecialchars(json_encode($row)) ?>)">👁️</button></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <br>
    <button onclick="window.location.href='export_completed_tasks.php?from_date=<?= htmlspecialchars($from_date) ?>&to_date=<?= htmlspecialchars($to_date) ?>'" class="btn btn-export">ئێکسپۆرتکردن بۆ ئێکسڵ 📤</button>
</div>

<div class="fab" onclick="window.location.href='add_task.php'">
    <i class="fas fa-plus"></i>
</div>

<script>
function showDetails(task) {
    const details = `
        <div class="popup">
            <div class="popup-content">
                <span class="close-btn" onclick="closePopup()">&times;</span>
                <h2>زانیارییەکانی ئەرک</h2>
                <p><strong>ID:</strong> ${task.id}</p>
                <p><strong>ئەرك:</strong> ${task.task_name}</p>
                <p><strong>ژمارە:</strong> ${task.task_number}</p>
                <p><strong>شوێن:</strong> ${task.location}</p>
                <p><strong>کارمەند:</strong> ${task.employee}</p>
                <p><strong>ژمارە مۆبایل:</strong> ${task.mobile_number}</p>
                <p><strong>تیم:</strong> ${task.team}</p>
                <p><strong>حاڵەت:</strong> ${task.status}</p>
                <p><strong>نرخ:</strong> ${task.cost} ${task.currency}</p>
                <p><strong>بەروار:</strong> ${task.date}</p>
                <p><strong>بەرواری تەواو کردن:</strong> ${task.completion_date}</p>
                <p><strong>(ڕۆژ):</strong> ${task.days_to_complete}</p>
            </div>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', details);
}

function closePopup() {
    const popup = document.querySelector('.popup');
    if (popup) {
        popup.remove();
    }
}
</script>

</body>
</html>
