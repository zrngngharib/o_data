<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$username = $_SESSION['username'];

// Sorting
$order_by = 'id DESC';
if (isset($_GET['sort'])) {
    switch ($_GET['sort']) {
        case 'newest': $order_by = 'id DESC'; break;
        case 'oldest': $order_by = 'id ASC'; break;
        case 'pending': $order_by = "status = 'Pending' DESC, id DESC"; break;
        case 'in_progress': $order_by = "status = 'In Progress' DESC, id DESC"; break;
    }
}

// Pagination
$tasks_per_page = 20;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $tasks_per_page;

// Search
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$where_clause = "status != 'Completed'";
if ($search) {
    $where_clause .= " AND (task_name LIKE '%$search%' OR task_number LIKE '%$search%' OR location LIKE '%$search%' OR employee LIKE '%$search%' OR mobile_number LIKE '%$search%' OR team LIKE '%$search%')";
}

// Counts
$query_total = "SELECT COUNT(*) as total FROM tasks WHERE $where_clause";
$result_total = mysqli_query($conn, $query_total);
$total_tasks = mysqli_fetch_assoc($result_total)['total'];
$total_pages = ceil($total_tasks / $tasks_per_page);

// Tasks Query
$query = "SELECT * FROM tasks WHERE $where_clause ORDER BY $order_by LIMIT $tasks_per_page OFFSET $offset";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="ku" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>📌 ئەركەكانی ڕۆژانە</title>

    <!-- Fonts + Tailwind + Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Style -->
    <style>
        @font-face {
            font-family: 'Zain';
            src: url('../fonts/Zain.ttf');
        }
        body {
            font-family: 'Zain', sans-serif !important;
            background: linear-gradient(135deg, #dee8ff, #f5f7fa);
        }
        .glass {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.1);
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

    </style>
</head>

<body class="p-4">

    <!-- Header -->
    <header class="glass max-w-7xl mx-auto mb-6 flex justify-between items-center p-4">
        <h1 class="text-3xl font-bold text-indigo-700">📌 ئەركەكانی ڕۆژانە</h1>
        <div class="flex gap-3 items-center">
            <span>👤 <?= htmlspecialchars($username); ?></span>
            <a href="../logout.php" class="btn btn-danger">🚪 دەرچوون</a>
        </div>
    </header>

    <!-- Actions Buttons -->
    <div class="glass max-w-7xl mx-auto mb-4 p-4 flex flex-wrap gap-2 justify-between items-center">
        <div class="flex gap-2">
            <a href="tasks/add_task.php" class="dashboard-btn">➕ زیادکردن</a>
            <a href="tasks/pending_tasks.php" class="dashboard-btn">⏳ چاوەڕوانەکان</a>
            <a href="tasks/completed_tasks.php" class="dashboard-btn">✅ تەواوبووەکان</a>
            <a href="tasks/report.php" class="dashboard-btn">📊 ڕاپۆرت</a>
        </div>
        <form method="GET" action="tasks.php" class="flex gap-2">
            <input type="text" name="search" class="form-control" placeholder="🔍 گەڕان..." value="<?= htmlspecialchars($search); ?>">
            <select name="sort" class="form-select w-auto">
                <option value="newest">نوێترین</option>
                <option value="oldest">کۆنترین</option>
                <option value="pending">⏳ Pending</option>
                <option value="in_progress">🚧 In Progress</option>
            </select>
            <button type="submit" class="dashboard-btn">📤 گەڕان</button>
        </form>
    </div>

    <form method="POST" action="tasks/bulk_action.php" onsubmit="return confirm('دڵنیایت بە ئەم کردارانە؟');">

    <!-- خشتەی کارەکان -->
    <div class="glass max-w-7xl mx-auto p-4 overflow-x-auto">
        <table class="w-full text-sm text-right text-gray-700 bg-white rounded-xl shadow-md border border-gray-200">
            <thead class="text-l uppercase bg-indigo-600 text-white">
                <tr class="text-center">
                    <th class="p-3 border-b-4 border-indigo-500">🎯</th>
                    <th class="p-3 border-b-4 border-indigo-500">ID</th>
                    <th class="p-2 border-b-4 border-indigo-500">ئەرك</th>
                    <th class="p-1 border-b-4 border-indigo-500">ژمارە</th>
                    <th class="p-1 border-b-4 border-indigo-500">شوێن</th>
                    <th class="p-1 border-b-4 border-indigo-500">کارمەند</th>
                    <th class="p-1 border-b-4 border-indigo-500">مۆبایل</th>
                    <th class="p-1 border-b-4 border-indigo-500">تیم</th>
                    <th class="p-1 border-b-4 border-indigo-500"> حاڵەت</th>
                    <th class="p-1 border-b-4 border-indigo-500">نرخ</th>
                    <th class="p-2 border-b-4 border-indigo-500">بەروار</th>
                    <th class="p-2 border-b-4 border-indigo-500">کردارەکان</th>
                </tr>
            </thead>

            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr class="bg-white border-b hover:bg-indigo-50 transition-all duration-300 text-center">
                    <td class="px-3 py-2 break-words whitespace-normal"><input type="checkbox" name="selected_tasks[]" value="<?= $row['id'] ?>"></td>
                    <td class="px-3 py-2 break-words whitespace-normal"><?= $row['id'] ?></td>
                    <td class="px-1 py-2 break-words whitespace-normal"><?= htmlspecialchars($row['task_name']) ?></td>
                    <td class="px-1 py-2 break-words whitespace-normal"><?= htmlspecialchars($row['task_number']) ?></td>
                    <td class="px-1 py-2 break-words whitespace-normal"><?= htmlspecialchars($row['location']) ?></td>
                    <td class="px-1 py-2 break-words whitespace-normal"><?= htmlspecialchars($row['employee']) ?></td>
                    <td class="px-1 py-2 break-words whitespace-normal"><?= htmlspecialchars($row['mobile_number']) ?></td>
                    <td class="px-1 py-2 break-words whitespace-normal">
                        <span class="inline-block px-2 py-1 rounded-full text-xs font-medium 
                            <?= $row['team'] === 'Internal' ? 'bg-blue-200 text-blue-800' : 'bg-green-200 text-green-800' ?>">
                            <?= htmlspecialchars($row['team']) ?>
                        </span>
                    </td>
                    <td class="px-3 py-2 break-words whitespace-normal">
                        <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold
                            <?= $row['status'] == 'Pending' ? 'bg-yellow-100 text-yellow-800' : 
                                ($row['status'] == 'In Progress' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800') ?>">
                            <?= htmlspecialchars($row['status']) ?>
                        </span>
                    </td>
                    <td class="p-2 break-words whitespace-normal"><?= htmlspecialchars($row['cost']) ?> <?= htmlspecialchars($row['currency']) ?></td>
                    <td class="p-2 break-words whitespace-normal"><?= htmlspecialchars($row['date']) ?></td>
                    <td class="p-2 flex justify-center gap-2">
                        <a href="tasks/edit_task.php?id=<?= $row['id'] ?>" class="px-3 py-1 text-white bg-yellow-500 rounded-md hover:bg-yellow-600 transition">✏️</a>
                        <a href="tasks/copy_task.php?id=<?= $row['id'] ?>" class="px-3 py-1 text-white bg-green-500 rounded-md hover:bg-green-600 transition">📋</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- دوگمەی بەکۆمەڵەوە -->
    <div class="flex gap-4 justify-center mt-4">
        <button type="submit" name="action" value="delete" class="dashboard-btn bg-red-600 hover:bg-red-700">✖ سڕینەوە</button>
        <button type="submit" name="action" value="complete" class="dashboard-btn bg-green-600 hover:bg-green-700">✔ تەواوکردن</button>
    </div>
    </form>

    <!-- Pagination -->
    <nav class="max-w-7xl mx-auto mt-4 flex justify-center">
        <ul class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?>&sort=<?= htmlspecialchars($_GET['sort'] ?? 'newest') ?>&search=<?= htmlspecialchars($search) ?>"><?= $i ?></a>
            </li>
            <?php endfor; ?>
        </ul>
    </nav>

    <!--Floating Add Task -->
    <!--<div class="fab" onclick="window.location.href='tasks/add_task.php'">➕</div> -->

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>