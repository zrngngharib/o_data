<?php
session_start();
include '../includes/db.php'; // ڕێڕەوی دروست بۆ `db.php`

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$order_by = 'id DESC'; // Default sorting by newest ID

if (isset($_GET['sort'])) {
    switch ($_GET['sort']) {
        case 'newest':
            $order_by = 'id DESC';
            break;
        case 'oldest':
            $order_by = 'id ASC';
            break;
        case 'pending':
            $order_by = "status = 'Pending' DESC, id DESC";
            break;
        case 'in_progress':
            $order_by = "status = 'In Progress' DESC, id DESC";
            break;
    }
}

// پەیجینەیشن
$tasks_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $tasks_per_page;

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

$where_clause = "status != 'Completed'";
if ($search) {
    $where_clause .= " AND (task_name LIKE '%$search%' OR task_number LIKE '%$search%' OR location LIKE '%$search%' OR employee LIKE '%$search%' OR mobile_number LIKE '%$search%' OR team LIKE '%$search%')";
}

$query_total = "SELECT COUNT(*) as total FROM tasks WHERE $where_clause";
$result_total = mysqli_query($conn, $query_total);
$row_total = mysqli_fetch_assoc($result_total);
$total_tasks = $row_total['total'];
$total_pages = ceil($total_tasks / $tasks_per_page);

$query = "SELECT * FROM tasks WHERE $where_clause ORDER BY $order_by LIMIT $tasks_per_page OFFSET $offset";
$result = mysqli_query($conn, $query);

// ژمارەی کارەکان بەپێی حاڵەت
$query_pending = "SELECT COUNT(*) as total FROM tasks WHERE status = 'Pending'";
$result_pending = mysqli_query($conn, $query_pending);
$row_pending = mysqli_fetch_assoc($result_pending);
$total_pending = $row_pending['total'];

$query_in_progress = "SELECT COUNT(*) as total FROM tasks WHERE status = 'In Progress'";
$result_in_progress = mysqli_query($conn, $query_in_progress);
$row_in_progress = mysqli_fetch_assoc($result_in_progress);
$total_in_progress = $row_in_progress['total'];

$query_completed = "SELECT COUNT(*) as total FROM tasks WHERE status = 'Completed'";
$result_completed = mysqli_query($conn, $query_completed);
$row_completed = mysqli_fetch_assoc($result_completed);
$total_completed = $row_completed['total'];
?>

<!DOCTYPE html>
<html lang="ku">
<head>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Zain:wght@200;300;400;700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.2.4/dist/tailwind.min.css" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- RTL Support -->
    <style>
        body {
            direction: rtl;
            font-family: 'Zain', sans-serif;
            background-color: #f9fafb;
        }
        .table-container {
            overflow-x: auto;
            background: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        .page-link {
            color: #007bff;
        }
        .page-link:hover {
            color: #0056b3;
            text-decoration: none;
        }
        .pagination {
            justify-content: center;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        .custom-button {
            color: white;
            background-color: rgb(16, 0, 49);
            border-radius: 50px;
            font-size: 0.75rem;
            padding: 0.5rem 1rem;
            text-align: center;
            transition: all 0.3s ease;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .custom-button:hover {
            background-color: #4f36c7;
        }
        .back-button {
            background-color: #e74c3c;
        }
        .back-button:hover {
            background-color: #c0392b;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            padding: 10px;
            text-align: center;
        }
        table tr:hover {
            background-color: #f5f5f5;
        }
        table thead th {
            background-color: #f0f0f0;
        }
        .button-container {
            display: flex;
            justify-content: center;
            gap: 10px;
            padding: 0 0 10px 0px;
        }
    </style>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ئەركەكانی ڕۆژانە</title>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">ئەركەكانی ڕۆژانە</h1>

        <div class="button-container">
            <button class="custom-button" onclick="window.location.href='tasks/add_task.php'">➕ زیاد كردن</button>
            <button class="custom-button" onclick="window.location.href='tasks/pending_tasks.php'">⏳ کارە چاوەڕوانەکان</button>
            <button class="custom-button" onclick="window.location.href='tasks/completed_tasks.php'">✅ کارە تەواوبووەکان</button>
            <button class="custom-button" onclick="window.location.href='tasks/report.php'">📊 ڕاپۆرتی گشتی</button>
        </div>

        <div class="mb-4">
            <input type="text" id="search" class="form-control" placeholder="🔍 گەڕان بپێی ئەرك، ژمارە، شوێن، كارمەند..." onkeyup="searchTasks()">
        </div>
        <script>
            function searchTasks() {
                const input = document.getElementById('search');
                const filter = input.value.toLowerCase();
                const table = document.getElementById('tasksTable');
                const tr = table.getElementsByTagName('tr');

                for (let i = 1; i < tr.length; i++) {
                    tr[i].style.display = 'none';
                    const td = tr[i].getElementsByTagName('td');
                    for (let j = 1; j < td.length; j++) {
                        if (td[j]) {
                            if (td[j].innerHTML.toLowerCase().indexOf(filter) > -1) {
                                tr[i].style.display = '';
                                break;
                            }
                        }
                    }
                }
            }
        </script>

        <div class="d-flex justify-content-between mb-4">
            <div class="d-flex align-items-center">
                <label class="me-2">ڕیزبەندی:</label>
                <select id="sort" class="form-select w-auto" onchange="updateSort()">
                    <option value="newest" <?php if (isset($_GET['sort']) && $_GET['sort'] == 'newest') echo 'selected'; ?>>نوێترین</option>
                    <option value="oldest" <?php if (isset($_GET['sort']) && $_GET['sort'] == 'oldest') echo 'selected'; ?>>کۆنترین</option>
                    <option value="pending" <?php if (isset($_GET['sort']) && $_GET['sort'] == 'pending') echo 'selected'; ?>>Pending</option>
                    <option value="in_progress" <?php if (isset($_GET['sort']) && $_GET['sort'] == 'in_progress') echo 'selected'; ?>>In Progress</option>
                </select>
            </div>
            <div class="d-flex justify-content-start">
                <span>بڕۆ بۆ لاپەڕەی: </span>
                <nav aria-label="Page navigation">
                    <ul class="pagination">
                        <?php
                        for ($i = 1; $i <= $total_pages; $i++) {
                            $active = ($i == $page) ? 'active' : '';
                            echo "<li class='page-item $active'><a class='page-link' href='tasks.php?page=$i&sort=" . (isset($_GET['sort']) ? $_GET['sort'] : 'newest') . "&search=" . (isset($_GET['search']) ? $_GET['search'] : '') . "'>$i</a></li>";
                        }
                        ?>
                    </ul>
                </nav>
            </div>
        </div>
        <p class="text-center mt-4">
            <i class="fas fa-hourglass-start text-blue-500"></i> چاوەڕوانی: <?php echo $total_pending; ?>، 
            <i class="fas fa-spinner text-yellow-500"></i> کارکردن بەردەوامە: <?php echo $total_in_progress; ?>، 
            <i class="fas fa-check-circle text-green-500"></i> تەواوبووەکان: <?php echo $total_completed; ?>
        </p>
        <form method="POST" action="tasks/bulk_action.php" onsubmit="return confirmAction(this.action.value)">
            <div class="table-container p-4 overflow-x-auto bg-white shadow-lg rounded-lg">
                <table id="tasksTable" class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-200 text-gray-700 text-center">
                            <th class="p-3">🎯</th>
                            <th class="p-3">ID</th>
                            <th class="p-3">ئەرك</th>
                            <th class="p-3">ژمارە</th>
                            <th class="p-3">شوێن</th>
                            <th class="p-3">کارمەند</th>
                            <th class="p-3">ژمارە مۆبایل</th>
                            <th class="p-3">تیم</th>
                            <th class="p-3">حاڵەت</th>
                            <th class="p-3">نرخ</th>
                            <th class="p-3">بەروار</th>
                            <th class="p-3">⚙️</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr class="border-b text-center hover:bg-gray-100">
                            <td class="p-3"><input type="checkbox" name="selected_tasks[]" value="<?= $row['id'] ?>"></td>
                            <td class="p-3"><?= $row['id'] ?></td>
                            <td class="p-3"><?= $row['task_name'] ?></td>
                            <td class="p-3"><?= $row['task_number'] ?></td>
                            <td class="p-3"><?= $row['location'] ?></td>
                            <td class="p-3"><?= $row['employee'] ?></td>
                            <td class="p-3"><?= $row['mobile_number'] ?></td>
                            <td class="p-3"><?= $row['team'] ?></td>
                            <td class="p-3 font-bold <?php echo ($row['status'] == 'Pending') ? 'text-yellow-500' : (($row['status'] == 'In Progress') ? 'text-blue-500' : 'text-green-500'); ?>">
                                <?= $row['status'] ?>
                            </td>
                            <td class="p-3"><?= $row['cost'] ?> <?= $row['currency'] ?></td>
                            <td class="p-3"><?= $row['date'] ?></td>
                            <td class="p-3 flex justify-center gap-2">
                                <a href="tasks/edit_task.php?id=<?= $row['id'] ?>" class="px-3 py-1 bg-blue-500 text-white rounded-lg hover:bg-blue-700">✏️</a>
                                <a href="tasks/copy_task.php?id=<?= $row['id'] ?>" class="px-3 py-1 bg-green-500 text-white rounded-lg hover:bg-green-700">📋</a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between">
                <button type="submit" name="action" value="delete" class="btn btn-danger">❌ سڕینەوە</button>
                <button type="submit" name="action" value="complete" class="btn btn-success">✅ تەواوکردن</button>
            </div>
        </form>
    </div>

    <!-- Bootstrap Bundle JS (including Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
