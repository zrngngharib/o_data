<?php
session_start();
include '../includes/db.php'; // ڕێڕەوی دروست بۆ `db.php`

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$order_by = 'date DESC'; // ڕیزبەندی بنەڕەتی بەپێی نوێترین

if (isset($_GET['sort'])) {
    switch ($_GET['sort']) {
        case 'newest':
            $order_by = 'date DESC';
            break;
        case 'oldest':
            $order_by = 'date ASC';
            break;
        case 'pending':
            $order_by = "status = 'Pending' DESC, date DESC";
            break;
        case 'in_progress':
            $order_by = "status = 'In Progress' DESC, date DESC";
            break;
    }
}

// پەیجینەیشن
$tasks_per_page = 20;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $tasks_per_page;

$query_total = "SELECT COUNT(*) as total FROM tasks WHERE status != 'Completed'";
$result_total = mysqli_query($conn, $query_total);
$row_total = mysqli_fetch_assoc($result_total);
$total_tasks = $row_total['total'];
$total_pages = ceil($total_tasks / $tasks_per_page);

$query = "SELECT * FROM tasks WHERE status != 'Completed' ORDER BY $order_by LIMIT $tasks_per_page OFFSET $offset";
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
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        margin: 0;
    }
    table th, table td {
        padding: 3px;
    }
    table tr:hover {
        background-color: #f5f5f5;
    }
    .custom-button {
        color: white;
        background-color: rgb(16, 0, 49);
        hover:bg-blue-800;
        focus:outline-none;
        focus:ring-4;
        focus:ring-blue-300;
        font-weight: 500;
        border-radius: 9999px;
        font-size: 0.875rem;
        padding: 0.625rem 1.25rem;
        text-align: center;
        margin-bottom: 0.5rem;
        margin-right: 0.5rem;
        dark:bg-blue-600;
        dark:hover:bg-blue-700;
        dark:focus:ring-blue-800;
    }
    .header-container {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }
    .header-title {
        margin-right: 10px;
        font-size: 1.5rem;
    }
</style>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ئەركەكانی ڕۆژانە</title>
    <link rel="stylesheet" href="../styles.css">
    <script>
        function updateSort() {
            const sort = document.getElementById('sort').value;
            window.location.href = `tasks.php?sort=${sort}`;
        }

        function confirmAction(action) {
            const message = action === 'delete' ? 'دڵنیای لە ئەنجامدانی ئەم کارە؟' : 'دڵنیای لە ئەنجامدانی ئەم کارە؟';
            return confirm(message);
        }

        function searchTasks() {
            const input = document.getElementById('search').value.toLowerCase();
            const table = document.querySelector('table');
            const rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) {
                const cells = rows[i].getElementsByTagName('td');
                let match = false;

                for (let j = 1; j < cells.length; j++) {
                    if (cells[j].innerText.toLowerCase().includes(input)) {
                        match = true;
                        break;
                    }
                }

                rows[i].style.display = match ? '' : 'none';
            }
        }
    </script>
</head>
<body>
    <div class="header-container">
        <h1 class="header-title">ئەركەكانی ڕۆژانە</h1>
        <button class="custom-button" onclick="window.location.href='tasks/add_task.php'">➕ زیاد كردن</button>
        <button class="custom-button" onclick="window.location.href='tasks/pending_tasks.php'">⏳ کارە چاوەڕوانەکان</button>
        <button class="custom-button" onclick="window.location.href='tasks/completed_tasks.php'">✅ کارە تەواوبووەکان</button>
        <button class="custom-button" onclick="window.location.href='tasks/report.php'">📊 ڕاپۆرتی گشتی</button>
    </div>
    <input type="text" id="search" placeholder="🔍 گەڕان بپێی ئەرك، ژمارە، شوێن، كارمەند..." onkeyup="searchTasks()">
    <br><br>
    <label>ڕیزبەندی:</label>
    <select id="sort" onchange="updateSort()">
        <option value="newest" <?php if (isset($_GET['sort']) && $_GET['sort'] == 'newest') echo 'selected'; ?>>نوێترین</option>
        <option value="oldest" <?php if (isset($_GET['sort']) && $_GET['sort'] == 'oldest') echo 'selected'; ?>>کۆنترین</option>
        <option value="pending" <?php if (isset($_GET['sort']) && $_GET['sort'] == 'pending') echo 'selected'; ?>>Pending</option>
        <option value="in_progress" <?php if (isset($_GET['sort']) && $_GET['sort'] == 'in_progress') echo 'selected'; ?>>In Progress</option>
    </select>
    <span> | </span>
    <div style="display: inline;">
    <span>بڕۆ بۆ لاپەڕەی: </span>
        <?php
        for ($i = 1; $i <= $total_pages; $i++) {
            echo "<a href='tasks.php?page=$i&sort=" . (isset($_GET['sort']) ? $_GET['sort'] : 'newest') . "'>$i</a> ";
        }
        ?>
    </div>
    <br><br>
    <div>
        <p>چاوەڕوانی: <?php echo $total_pending; ?></p>
        <p>کارکردن بەردەوامە: <?php echo $total_in_progress; ?></p>
        <p>تەواوبووەکان: <?php echo $total_completed; ?></p>
    </div>
    <form method="POST" action="tasks/bulk_action.php" onsubmit="return confirmAction(this.action.value)">
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">🎯</th>
                        <th scope="col" class="px-6 py-3">ID</th>
                        <th scope="col" class="px-6 py-3">ئەرك 📋</th>
                        <th scope="col" class="px-6 py-3">ژمارە 🔢</th>
                        <th scope="col" class="px-6 py-3">شوێن 📍</th>
                        <th scope="col" class="px-6 py-3">کارمەند 👤</th>
                        <th scope="col" class="px-6 py-3">ژمارە مۆبایل 📞</th>
                        <th scope="col" class="px-6 py-3">تیم 👥</th>
                        <th scope="col" class="px-6 py-3">حاڵەت 📊</th>
                        <th scope="col" class="px-6 py-3">نرخ 💰</th>
                        <th scope="col" class="px-6 py-3">بەروار 📅</th>
                        <th scope="col" class="px-6 py-3">کردار ⚙️</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr class='odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200'>";
                        echo "<td class='px-6 py-4 whitespace-nowrap'><input type='checkbox' name='selected_tasks[]' value='{$row['id']}'></td>";
                        echo "<td class='px-6 py-4 whitespace-nowrap'>{$row['id']}</td>";
                        echo "<td class='px-6 py-4 whitespace-nowrap'>{$row['task_name']}</td>";
                        echo "<td class='px-6 py-4 whitespace-nowrap'>{$row['task_number']}</td>";
                        echo "<td class='px-6 py-4 whitespace-nowrap'>{$row['location']}</td>";
                        echo "<td class='px-6 py-4 whitespace-nowrap'>{$row['employee']}</td>";
                        echo "<td class='px-6 py-4 whitespace-nowrap'>{$row['mobile_number']}</td>";
                        echo "<td class='px-6 py-4 whitespace-nowrap'>{$row['team']}</td>";
                        echo "<td class='px-6 py-4 whitespace-nowrap'>{$row['status']}</td>";
                        echo "<td class='px-6 py-4 whitespace-nowrap'>{$row['cost']} {$row['currency']}</td>";
                        echo "<td class='px-6 py-4 whitespace-nowrap'>{$row['date']}</td>";
                        echo "<td class='px-6 py-4 whitespace-nowrap'>
                                <a href='tasks/edit_task.php?id={$row['id']}' class='font-medium text-blue-600 dark:text-blue-500 hover:underline'>✏️ دەستکاری</a> |
                                <a href='tasks/copy_task.php?id={$row['id']}' class='font-medium text-blue-600 dark:text-blue-500 hover:underline'>📋 کۆپی</a> |
                                <a href='tasks/delete_task.php?id={$row['id']}' class='font-medium text-blue-600 dark:text-blue-500 hover:underline'>❌ سڕینەوە</a>
                              </td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <br>
        <button type="submit" name="action" value="delete" class="custom-button">❌ سڕینەوەی بەکۆمەڵ</button>
        <button type="submit" name="action" value="complete" class="custom-button">✅ تەواوکردنی بەکۆمەڵ</button>
    </form>
    <br>
    <div class="flex justify-center">
        <nav aria-label="Page navigation example">
            <ul class="inline-flex items-center -space-x-px">
                <?php if ($page > 1): ?>
                    <li>
                        <a href="tasks.php?page=<?php echo $page - 1; ?>&sort=<?php echo isset($_GET['sort']) ? $_GET['sort'] : 'newest'; ?>" class="px-3 py-2 ml-0 leading-tight text-gray-500 bg-white border border-gray-300 rounded-l-lg hover:bg-gray-100 hover:text-gray-700">Previous</a>
                    </li>
                <?php endif; ?>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li>
                        <a href="tasks.php?page=<?php echo $i; ?>&sort=<?php echo isset($_GET['sort']) ? $_GET['sort'] : 'newest'; ?>" class="px-3 py-2 leading-tight <?php echo $i == $page ? 'text-blue-600 bg-blue-50 border border-blue-300' : 'text-gray-500 bg-white border border-gray-300'; ?> hover:bg-gray-100 hover:text-gray-700"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                <?php if ($page < $total_pages): ?>
                    <li>
                        <a href="tasks.php?page=<?php echo $page + 1; ?>&sort=<?php echo isset($_GET['sort']) ? $_GET['sort'] : 'newest'; ?>" class="px-3 py-2 leading-tight text-gray-500 bg-white border border-gray-300 rounded-r-lg hover:bg-gray-100 hover:text-gray-700">Next</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
<!-- Bootstrap Bundle JS (including Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>