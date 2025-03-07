<?php
session_start();
include '../../includes/db.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

// ڕوونکردنەوەی هەڵەکان
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ڕیزبەندی بەپێی بەروار
$order = isset($_GET['order']) && $_GET['order'] == 'asc' ? 'ASC' : 'DESC';
$query = "SELECT * FROM tasks WHERE status IN ('Pending', 'In Progress') ORDER BY date $order";
$result = mysqli_query($conn, $query);
if (!$result) {
    die("کێشە لە ناردنی داتا: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="ku" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لیستی چاوەڕوانی کارەکان  ⏳</title>

    <!-- فۆنتی Zain لە گۆگڵ فۆنتس -->
    <link href="https://fonts.googleapis.com/css2?family=Zain:ital,wght@0,200;0,300;0,400;0,700;0,800;0,900;1,300;1,400&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-[Zain] text-right">

    <div class="container mx-auto p-6">
        <h1 class="text-3xl text-blue-700 text-center font-bold mb-6">لیستی چاوەڕوانی کارەکان ⏳</h1>

        <!-- Dropdown بۆ ڕیزبەندی -->
        <div class="relative inline-block text-left mb-4">
            <button onclick="toggleDropdown()" class="bg-blue-500 text-white px-4 py-2 rounded-lg flex items-center">
                ڕیزبەندی بەپێی بەروار <i class="mr-2 fas fa-chevron-down"></i>
            </button>
            <div id="dropdownMenu" class="hidden absolute mt-2 w-48 bg-white border rounded-lg shadow-lg">
                <label class="block px-4 py-2 cursor-pointer">
                    <input type="radio" name="sort" value="desc" onclick="sortTable('desc')" class="ml-2">
                    نوێترین
                </label>
                <label class="block px-4 py-2 cursor-pointer">
                    <input type="radio" name="sort" value="asc" onclick="sortTable('asc')" class="ml-2">
                    کۆنترین
                </label>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-300 shadow-lg rounded-lg">
                <thead>
                    <tr class="bg-blue-600 text-white">
                        <th class="px-4 py-2">ID</th>
                        <th class="px-4 py-2">ئەرك 📝</th>
                        <th class="px-4 py-2">ژمارە 🔢</th>
                        <th class="px-4 py-2">شوێن 📍</th>
                        <th class="px-4 py-2">کارمەند 👤</th>
                        <th class="px-4 py-2">ژمارە مۆبایل 📱</th>
                        <th class="px-4 py-2">تیم 👥</th>
                        <th class="px-4 py-2">حاڵەت 📊</th>
                        <th class="px-4 py-2">نرخ 💰</th>
                        <th class="px-4 py-2">بەروار 📅</th>
                        <th class="px-4 py-2">کردار ⚙️</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                        <tr class="border-t">
                            <td class="px-4 py-2"><?= htmlspecialchars($row['id']) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($row['task_name']) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($row['task_number']) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($row['location']) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($row['employee']) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($row['mobile_number']) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($row['team']) ?></td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-1 rounded-full text-white text-xs 
                                    <?= $row['status'] == 'Pending' ? 'bg-yellow-500' : 'bg-blue-500' ?>">
                                    <?= htmlspecialchars($row['status']) ?>
                                </span>
                            </td>
                            <td class="px-4 py-2"><?= htmlspecialchars($row['cost']) ?> <?= htmlspecialchars($row['currency']) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($row['date']) ?></td>
                            <td class="px-4 py-2">
                                <a href="complete_task.php?id=<?= $row['id'] ?>" class="bg-green-500 text-white px-3 py-1 rounded-lg">
                                    <i class="fas fa-check-circle"></i> تەواوکردن
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- JavaScript بۆ کارکردنی Dropdown -->
    <script>
        function toggleDropdown() {
            document.getElementById('dropdownMenu').classList.toggle('hidden');
        }

        function sortTable(order) {
            window.location.href = "?order=" + order;
        }
    </script>

    <!-- FontAwesome بۆ Icon -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>

</body>
</html>
