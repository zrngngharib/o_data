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
        case 'چاوەڕوانی': $order_by = "status = 'چاوەڕوانی' DESC, id DESC"; break;
        case 'دەستپێکراوە': $order_by = "status = 'دەستپێکراوە' DESC, id DESC"; break;
    }
}

$query = "SELECT * FROM tasks WHERE $where ORDER BY $order_by LIMIT $limit OFFSET $offset";

// Pagination
$tasks_per_page = 20;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $tasks_per_page;

// Search
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$where_clause = "status != 'تەواوکراوە'";
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
    <title> ئەركەكانی ڕۆژانە</title>
    
    <!-- TailwindCSS + Bootstrap RTL -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- فۆنت و ستایلەکان -->
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
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            box-shadow: 0 8px 24px rgba(31, 38, 135, 0.1);
        }
        .dashboard-btn {
            background-color: #4F46E5;
            color: #fff;
            padding: 0.2rem 0.5rem;
            border-radius: 1rem;
            transition: all 0.3s ease;
        }
        .dashboard-btn:hover {
            background-color: #6366F1;
            transform: scale(1.05);
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
            padding: 12px 5px;
            text-align: center;
        }
        .table-actions button {
            transition: all 0.2s ease-in-out;
        }
        .table-actions button:hover {
            transform: scale(1.1);
        }

        /* Lightbox Styles */
        #lightboxOverlay {
            backdrop-filter: blur(5px);
        }
        #lightboxImage {
            width: auto;
            height: 500px;
            border-radius: 12px;
            transition: transform 0.3s;
        }
        #lightboxOverlay:hover #lightboxImage {
            transform: scale(1.02);
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
                font-weight: bold;
            }
        }
    </style>
</head>

<body class="p-4">

    <!-- Lightbox -->
    <div id="lightboxOverlay" class="fixed inset-0 bg-black bg-opacity-75 flex justify-center items-center hidden z-50">
        <div class="relative">
            <img id="lightboxImage" src="" alt="Task Image" />
            <button onclick="closeLightbox()" class="absolute top-2 left-2 text-white text-2xl bg-red-600 px-4 py-2 rounded-full">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <!-- Header -->
    <header class="glass max-w-7xl mx-auto mb-6 flex justify-between items-center p-4">
        <h1 class="text-3xl font-bold text-indigo-700"><i class="fas fa-tasks"></i> ئەركەكانی ڕۆژانە</h1>
        <div class="flex gap-3 items-center">
            <span><i class="fas fa-user"></i> <?= htmlspecialchars($username); ?></span>
            <a href="../views/ctrluser/logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i> دەرچوون</a>
        </div>
    </header>

    <!-- Actions -->
    <div class="glass max-w-7xl mx-auto mb-6 p-4 flex flex-wrap gap-2 justify-between items-center">
        <div class="flex gap-2 flex-wrap">
            <a href="tasks/add_task.php" class="dashboard-btn"><i class="fas fa-plus"></i> زیادکردن</a>
            <a href="tasks/pending_tasks.php" class="dashboard-btn"><i class="fas fa-sync-alt"></i> چاوەڕوانی</a>
            <a href="tasks/completed_tasks.php" class="dashboard-btn"><i class="fas fa-check"></i> تەواوبوو</a>
            <a href="tasks/report.php" class="dashboard-btn"><i class="fas fa-chart-bar"></i> ڕاپۆرت</a>
            <a href="dashboard.php" class="dashboard-btn"><i class="fas fa-arrow-left"></i> گەڕانەوە</a>
        </div>
        <form method="GET" action="tasks.php" class="flex gap-2">
            <input type="text" name="search" class="form-control" placeholder="🔍 گەڕان..." value="<?= htmlspecialchars($search); ?>">
            <select name="sort" class="form-select w-auto">
                <option value="newest">نوێترین</option>
                <option value="oldest">کۆنترین</option>
                <option value="چاوەڕوانی">چاوەڕوانی</option>
                <option value="دەستپێکراوە">دەستپێکراوە</option>
            </select>
            <button type="submit" class="dashboard-btn"><i class="fas fa-search"></i> گەڕان</button>
        </form>
    </div>

    <!-- Table -->
    <form method="POST" action="tasks/bulk_action.php" onsubmit="return confirm('دڵنیایت؟');">
    <div class="glass max-w-7xl mx-auto p-4 overflow-x-auto rounded-20">
        <table>
            <thead>
                <tr>
                    <th><i class="fas fa-bullseye"></i></th><th>ID</th><th>ئەرك</th><th>ژمارە</th><th>شوێن</th><th>کارمەند</th><th>مۆبایل</th>
                    <th>تیم</th><th>حاڵەت</th><th>نرخ</th><th>بەروار</th><th>کردارەکان</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td data-label="🎯"><input type="checkbox" name="selected_tasks[]" value="<?= $row['id'] ?>"></td>
                    <td data-label="ID"><?= $row['id'] ?></td>
                    <td data-label="ئەرك"><?= htmlspecialchars($row['task_name']) ?></td>
                    <td data-label="ژمارە"><?= htmlspecialchars($row['task_number']) ?></td>
                    <td data-label="شوێن"><?= htmlspecialchars($row['location']) ?></td>
                    <td data-label="کارمەند"><?= htmlspecialchars($row['employee']) ?></td>
                    <td data-label="مۆبایل"><?= htmlspecialchars($row['mobile_number']) ?></td>
                    <td data-label="تیم">
                        <span class="px-2 py-1 rounded-full text-xs font-medium
                            <?= $row['team'] === 'Internal' ? 'bg-blue-200 text-blue-800' : 'bg-green-200 text-green-800' ?>">
                            <?= htmlspecialchars($row['team']) ?>
                        </span>
                    </td>
                    <td data-label="حاڵەت">
                        <span class="px-2 py-1 rounded-full text-xs font-semibold
                            <?= $row['status'] === 'چاوەڕوانی' ? 'bg-yellow-100 text-yellow-800' :
                                ($row['status'] === 'دەستپێکراوە' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800') ?>">
                            <?= htmlspecialchars($row['status']) ?>
                        </span>
                    </td>
                    <td data-label="نرخ"><?= htmlspecialchars($row['cost']) ?> <?= htmlspecialchars($row['currency']) ?></td>
                    <td data-label="بەروار"><?= htmlspecialchars($row['date']) ?></td>
                    <td data-label="کردارەکان" class="table-actions flex justify-center gap-2">
                        <a href="tasks/edit_task.php?id=<?= $row['id'] ?>" class="dashboard-btn bg-yellow-500 hover:bg-yellow-600"><i class="fas fa-edit"></i></a>
                        <a href="tasks/copy_task.php?id=<?= $row['id'] ?>" class="dashboard-btn bg-green-500 hover:bg-green-600"><i class="fas fa-copy"></i></a>
                        <?php if (!empty($row['files'])): ?>
                            <button type="button" onclick="openLightbox('<?= htmlspecialchars($row['files']) ?>')" class="dashboard-btn bg-blue-600 hover:bg-blue-700"><i class="fas fa-eye"></i></button>
                        <?php else: ?>
                            <button type="button" disabled class="dashboard-btn bg-gray-400"><i class="fas fa-eye-slash"></i></button>
                        <?php endif; ?>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div class="flex gap-4 justify-center mt-4">
        <button type="submit" name="action" value="delete" class="dashboard-btn bg-red-600 hover:bg-red-700"><i class="fas fa-trash-alt"></i> سڕینەوە</button>
        <button type="submit" name="action" value="complete" class="dashboard-btn bg-green-600 hover:bg-green-700"><i class="fas fa-check"></i> تەواوکردن</button>
    </div>
    </form>

    <!-- Pagination -->
    <nav class="max-w-7xl mx-auto mt-4 flex justify-center">
        <ul class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?>&sort=<?= htmlspecialchars($sort) ?>&search=<?= htmlspecialchars($search) ?>"><?= $i ?></a>
            </li>
            <?php endfor; ?>
        </ul>
    </nav>

    <!-- Scripts -->
    <script>
        function openLightbox(url) {
            const lightbox = document.getElementById('lightboxOverlay');
            const img = document.getElementById('lightboxImage');
            img.src = url;
            lightbox.classList.remove('hidden');
        }

        function closeLightbox() {
            const lightbox = document.getElementById('lightboxOverlay');
            lightbox.classList.add('hidden');
            document.getElementById('lightboxImage').src = '';
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>