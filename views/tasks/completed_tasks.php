<?php
session_start();
include_once dirname(__DIR__, 2) . '/includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$username = $_SESSION['username'] ?? 'میوان';

// ڕوونکردنەوەی هەڵەکان
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ڕادەی فلتەر بۆ بەروار
$from_date = $_GET['from_date'] ?? '';
$to_date = $_GET['to_date'] ?? '';

if (empty($from_date) || empty($to_date)) {
    $current_year = date('Y');
    $current_month = date('m');
    $from_date = "$current_year-$current_month-01";
    $to_date = date("Y-m-t", strtotime($from_date));
}
// ❗ کوێری داتابەیس
$query = "
    SELECT 
        id,
        task_name,
        task_number,
        location,
        employee,
        mobile_number,
        team,
        status,
        cost,
        currency,
        date,
        completion_date,
        DATEDIFF(completion_date, date) AS days_to_complete,
        image_url,
        files
    FROM 
        tasks
    WHERE 
        status = 'تەواوکراوە'
";

if (!empty($from_date) && !empty($to_date)) {
    $query .= " AND date BETWEEN '$from_date' AND '$to_date'";
}

$query .= " ORDER BY completion_date DESC";

$result = mysqli_query($conn, $query);
if (!$result) {
    die("❌ کێشە لە ناردنی داتا: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="ku" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>✅ کارە تەواوبووەکان</title>

    <!-- Bootstrap RTL + TailwindCSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- FontAwesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>

    <!-- Custom Style -->
    <style>
        @font-face {
            font-family: 'Zain';
            src: url('../../fonts/Zain.ttf');
        }

        body {
            font-family: 'Zain', sans-serif;
            background: linear-gradient(135deg, #dee8ff, #f5f7fa);
            color: #333;
        }

        .glass {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.1);
            transition: all 0.4s ease;
        }

        .glass:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px rgba(31, 38, 135, 0.15);
        }
        .dashboard-btn {
            background-color: #4F46E5;
            color: #fff;
            padding: 0.5rem 1rem;
            border-radius: 1rem;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .dashboard-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.4);
        }

        .btn-custom {
            background-color: #4F46E5;
            color: #fff;
            padding: 0.5rem 1rem;
            border-radius: 1rem;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .btn-custom:hover {
            background-color: #6366F1;
            transform: scale(1.05);
        }
        .task-compalte {
            background-color:rgb(0, 167, 61);
            color: #fff;
            padding: 0.2rem 0.5rem;
            border-radius: 1rem;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        /* Table Styles */
        table {
            border-spacing: 0 10px;
            width: 100%;

        }
        thead tr {
            background-color: #4F46E5;
            color: white;
        }
        tbody tr {
            background-color: #fff;
            border-radius: 12px;
            transition: all 0.3s;
        }
        tbody tr:hover {
            background-color: #f0f4ff;
        }
        td, th {
            padding: 10px 4px;
            text-align: center;
        }
        .table-actions button {
            transition: all 0.2s ease-in-out;
        }
        .table-actions button:hover {
            transform: scale(1.1);
        }

        /* Responsive */
        @media (max-width: 768px) {
            table, thead, tbody, th, td, tr {
                display: block;
            }
            tbody tr {
                margin-bottom: 10px;
            }
            td {
                padding: 10px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                text-align: right;
                border-bottom: 1px solid #eee;
            }
            td:before {
                content: attr(data-label);
            }
        }

        /* Lightbox style */
        #lightboxOverlay {
            backdrop-filter: blur(5px);
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        #lightboxImage {
            transition: transform 0.3s ease-in-out;
        }

        #lightboxOverlay:hover #lightboxImage {
            transform: scale(1.03);
        }
    </style>
</head>

<body class="p-4">

    <!-- Lightbox overlay -->
    <div id="lightboxOverlay" class="fixed inset-0 bg-black bg-opacity-75 flex justify-center items-center hidden z-50">
        <div class="relative">
            <img id="lightboxImage" src="" alt="Task Image"
                 class="rounded-lg shadow-lg object-cover"
                 style="width: 500px; height: 500px;" />
            <button onclick="closeLightbox()" class="absolute top-2 left-2 text-white text-2xl bg-red-600 px-3 py-1 rounded-full">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <!-- Header -->
    <header class="glass max-w-7xl mx-auto mb-6 flex justify-between items-center p-4">
        <h1 class="text-3xl font-bold text-green-600"><i class="fas fa-check-circle"></i> کارە تەواوبووەکان</h1>
        <div class="flex gap-3 items-center">
            <span><i class="fas fa-user"></i> <?= htmlspecialchars($username); ?></span>
            <a href="../tasks.php" class="btn btn-danger"><i class="fas fa-arrow-left"></i> کەڕانەوە</a>
        </div>
    </header>

    <!-- Filter Form -->
    <div class="glass max-w-7xl mx-auto mb-4 p-4 flex flex-wrap gap-2 justify-between items-center">
        <form method="GET" class="flex flex-wrap gap-2 items-center w-full justify-between">
            <div class="flex gap-2">
                <label>لە:</label>
                <input type="date" class="form-control rounded-lg border-2 border-indigo-300" name="from_date" value="<?= htmlspecialchars($from_date) ?>">
                <label>بۆ:</label>
                <input type="date" class="form-control rounded-lg border-2 border-indigo-300" name="to_date" value="<?= htmlspecialchars($to_date) ?>">
                <button type="submit" class="flex btn-custom form-control"><i class="fas fa-search"></i> فلتەر</button>
            </div>
       </form>
    </div>

    <!-- Table -->

    <div class="glass max-w-7xl mx-auto p-4 overflow-x-auto ">
        <table class="w-full text-s text-right text-gray-700 bg-white rounded-xl shadow-md border border-gray-200">
            <thead class="text-s uppercase bg-indigo-600 text-white ">
                <tr class="text-right">
                <tr>
                    <th>ID</th>
                    <th>ئەرك</th>
                    <th>ژمارە</th>
                    <th>شوێن</th>
                    <th>کارمەند</th>
                    <th>ژ.مۆبایل</th>
                    <th>تیم</th>
                    <th>حاڵەت</th>
                    <th>نرخ</th>
                    <th>بەروار</th>
                    <th>تەواوکردن</th>
                    <th>ڕۆژ</th>
                    <th>وێنە</th>
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
                    <td><span class="task-compalte"> <?= htmlspecialchars($row['status']) ?></span></td>
                    <td><?= htmlspecialchars($row['cost']) ?> <?= htmlspecialchars($row['currency']) ?></td>
                    <td><?= htmlspecialchars($row['date']) ?></td>
                    <td><?= htmlspecialchars($row['completion_date']) ?></td>
                    <td><?= htmlspecialchars($row['days_to_complete']) ?></td>
                    <td>
                        <?php if (!empty($row['files'])): ?>
                            <button onclick="openLightbox('<?= htmlspecialchars($row['files']) ?>')" 
                                    class="dashboard-btn bg-blue-600 hover:bg-blue-700 flex justify-right items-right gap-1 px-2 py-1 text-sm rounded-md transition">
                                <i class="fas fa-eye"></i>
                            </button>
                        <?php else: ?>
                            <span class="text-blue-300">نیە</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- Export Button -->
    <div class="flex justify-center mt-6">
        <a href="export_completed_tasks.php?from_date=<?= htmlspecialchars($from_date) ?>&to_date=<?= htmlspecialchars($to_date) ?>"
            class="btn btn-success"><i class="fas fa-file-export"></i> ئێکسپۆرتکردن بۆ Excel</a>
    </div>

    <!-- Lightbox Script -->
    <script>
        function openLightbox(imageUrl) {
            const overlay = document.getElementById('lightboxOverlay');
            const image = document.getElementById('lightboxImage');

            image.src = imageUrl;
            overlay.classList.remove('hidden');
        }

        function closeLightbox() {
            const overlay = document.getElementById('lightboxOverlay');
            const image = document.getElementById('lightboxImage');

            overlay.classList.add('hidden');
            image.src = '';
        }
    </script>

</body>
</html>