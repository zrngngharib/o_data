<?php
session_start();
include '../includes/db.php'; // ڕێڕەوی دروست بۆ `db.php`

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$order_by = 'id DESC'; // ڕیزبەندی بنەڕەتی بەپێی نوێترین ID

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
$tasks_per_page = 20;
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

        /* جیاکردنەوەی دوگمەکان بەرەوپێش */
        .action-buttons {
            display: flex;
            gap: 10px;
        }

        /* شێوەی تایبەتی بۆ دوگمەکان */
        .custom-button {
            display: inline-block;
            padding: 10px 16px;
            border-radius: 50px;
            font-size: 14px;
            color: white;
            text-align: center;
            color: #000;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .custom-button:hover {
            background-color: #4f36c7;
            color: white;
        }

        /* شێوەی خشتە */
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
            padding: 5px;
            text-align: right;
            border-bottom: 1px solid #ddd;
            justify-content: center;
            color:rgb(125, 125, 125);
        }
        th {
            background: #007bff;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .button-container {
            display: flex;
            justify-content: center; /* Center the buttons */
            gap: 10px; /* Add some space between buttons */
            padding: 0 0 10px 0px;
        }
        /* باگراوند بۆ حاڵەت */
        .bg-yellow-500 {
            background-color: #f59e0b;
        }
        .bg-blue-500 {
            background-color: #3b82f6;
        }
        .bg-gray-500 {
            background-color: #6b7280;
        }
        .pagination a {
            padding: 4px 6px 4px 6px;
            border: 1px solid #ddd;
            border-radius: 8px;
            text-decoration: none;
            color: #2563eb;
            transition: all 0.3s ease;
        }

        .fab {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #007bff;
            color: white;
            padding: 15px;
            border-radius: 50%;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            font-size: 24px;
        }

        .fab:hover {
            background:rgb(0, 164, 179);
            color: white;
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

        <div class="mb-2">
            <input type="text" id="search" class="form-control" placeholder="🔍 گەڕان بپێیID و بەروار و ئەرك، ژمارە، شوێن، كارمەند..." onkeyup="searchTasks()">
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
        <script>
            function updateSort() {
                const sort = document.getElementById('sort').value;
                const searchParams = new URLSearchParams(window.location.search);
                searchParams.set('sort', sort);
                window.location.search = searchParams.toString();
            }
        </script>
        <div class="d-flex justify-content-between mb-2">
            <div class="d-flex align-items-center">
                <label class="me-2">ڕیزبەندی:</label>
                <select id="sort" class="pagination a  w-auto " onchange="updateSort()">
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
        <form method="POST" action="tasks/bulk_action.php" onsubmit="return confirmAction(this.action.value)">
            <div class="table-container overflow-x-auto bg-white shadow-lg rounded-lg">
                <table id="tasksTable" class="w-full border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-200 text-gray-700 text-right">
                            <th class="p-2">🎯</th>
                            <th class="p-2">ID</th>
                            <th class="p-4">ئەرك</th>
                            <th class="p-2">ژمارە</th>
                            <th class="p-2">شوێن</th>
                            <th class="p-2">کارمەند</th>
                            <th class="p-2">ژمارە مۆبایل</th>
                            <th class="p-2">تیم</th>
                            <th class="p-4">حاڵەت</th>
                            <th class="p-2">نرخ</th>
                            <th class="p-2">بەروار</th>
                            <th class="p-2">کردارەکان</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr class="border-t text-right hover:bg-gray-100">
                            <td class="p-1"><input type="checkbox" name="selected_tasks[]" value="<?= $row['id'] ?>"></td>
                            <td class="p-1"><?= $row['id'] ?></td>
                            <td class="p-1"><?= $row['task_name'] ?></td>
                            <td class="p-2"><?= $row['task_number'] ?></td>
                            <td class="p-1"><?= $row['location'] ?></td>
                            <td class="p-1"><?= $row['employee'] ?></td>
                            <td class="p-1"><?= $row['mobile_number'] ?></td>
                            <td class="p-1"><?= $row['team'] ?></td>
                            <td class="p-0">
                                <span class="px-1 py-1 rounded-5 text-white text-xs 
                                    <?php 
                                        if ($row['status'] == 'Pending') {
                                            echo 'bg-yellow-500';
                                        } elseif ($row['status'] == 'In Progress') {
                                            echo 'bg-blue-500';
                                        } else {
                                            echo 'bg-gray-500';
                                        }
                                    ?>">
                                    <?= htmlspecialchars($row['status']) ?>
                                </span>
                            </td>
                            <td class="p-1"><?= $row['cost'] ?> <?= $row['currency'] ?></td>
                            <td class="p-1"><?= $row['date'] ?></td>
                            <td class="p-1 flex justify-center gap-2">
                                <a href="tasks/edit_task.php?id=<?= $row['id'] ?>" class="px-2 py-1 text-white rounded-lg hover:bg-blue-700">✏️</a>
                                <a href="tasks/copy_task.php?id=<?= $row['id'] ?>" class="px-2 py-1 text-white rounded-lg hover:bg-green-700">📋</a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between gap-2 mt-4">
                <button type="submit" name="action" value="delete" class="btn btn-danger">❌ سڕینەوە</button>
                <button type="submit" name="action" value="complete" class="btn btn-success">✅ تەواوکردن</button>
            </div>
        </form>
    </div>

    <!-- Bootstrap Bundle JS (including Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <div class="fab" onclick="window.location.href='tasks/add_task.php'">
        <i class="fas fa-plus"></i>
    </div>


    
</body>
</html>