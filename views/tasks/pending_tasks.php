<?php
session_start();
include '../../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
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
    <title>کارە چاوەڕوانەکان ⏳</title>

    <!-- فۆنتی Zain لە گۆگڵ فۆنتس -->
    <link href="https://fonts.googleapis.com/css2?family=Zain:ital,wght@0,200;0,300;0,400;0,700;0,800;0,900;1,300;1,400&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-[Zain] text-right">

    <div class="container mx-auto p-6">
        <h1 class="text-3xl text-blue-700 text-center font-bold mb-6">کارە چاوەڕوانەکان ⏳</h1>
        
        <div class="flex justify-between mb-4">
            <!-- Back Button -->
            <a href="../tasks.php" class="bg-gray-700 hover:bg-gray-600 text-white font-semibold px-4 py-2 rounded-lg transition duration-300 ease-in-out">
                گەڕانەوە
            </a>

            <!-- Dropdown بۆ ڕیزبەندی -->
            <div class="relative inline-block text-left">
                <button onclick="toggleDropdown()" class="bg-blue-700 hover:bg-blue-600 text-white font-semibold px-4 py-2 rounded-lg flex items-center transition duration-300 ease-in-out">
                    ڕیزبەندی بەپێی بەروار <i class="mr-2 fas fa-chevron-down"></i>
                </button>
                <div id="dropdownMenu" class="hidden absolute mt-2 w-48 bg-white border rounded-lg shadow-lg">
                    <label class="block px-4 py-2 cursor-pointer hover:bg-gray-100 transition duration-300 ease-in-out">
                        <input type="radio" name="sort" value="desc" onclick="sortTable('desc')" class="ml-2">
                        نوێترین
                    </label>
                    <label class="block px-4 py-2 cursor-pointer hover:bg-gray-100 transition duration-300 ease-in-out">
                        <input type="radio" name="sort" value="asc" onclick="sortTable('asc')" class="ml-2">
                        کۆنترین
                    </label>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-300 shadow-lg rounded-lg">
                <thead>
                    <tr class="bg-blue-600 text-white">
                        <th class="px-1 py-2">ID</th>
                        <th class="px-2 py-2">ئەرك 📝</th>
                        <th class="px-1 py-2">ژمارە 🔢</th>
                        <th class="px-2 py-2">شوێن 📍</th>
                        <th class="px-2 py-2">کارمەند 👤</th>
                        <th class="px-2 py-2">تیم 👥</th>
                        <th class="px-2 py-2">حاڵەت 📊</th>
                        <th class="px-2 py-2">بەروار 📅</th>
                        <th class="px-2 py-2">تێپەڕبوون</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)) { 
                        $task_date = new DateTime($row['date']);
                        $current_date = new DateTime();
                        $interval = $current_date->diff($task_date);
                        $days_passed = $interval->days;
                    ?>
                        <tr class="border-t" data-days="<?= $days_passed ?>">
                            <td class="px-1 py-2"><?= htmlspecialchars($row['id']) ?></td>
                            <td class="px-1 py-2"><?= htmlspecialchars($row['task_name']) ?></td>
                            <td class="px-1 py-2"><?= htmlspecialchars($row['task_number']) ?></td>
                            <td class="px-1 py-2"><?= htmlspecialchars($row['location']) ?></td>
                            <td class="px-1 py-2"><?= htmlspecialchars($row['employee']) ?></td>
                            <td class="px-1"><?= $row['team'] ?></td>
                            <td class="px-1 py-2">
                                <span class="px-2 py-1 rounded-full text-white text-xs 
                                    <?= $row['status'] == 'Pending' ? 'bg-yellow-500' : 'bg-blue-500' ?>">
                                    <?= htmlspecialchars($row['status']) ?>
                                </span>
                            </td>
                            <td class="px-1 py-2"><?= htmlspecialchars($row['date']) ?></td>
                            <td class="px-1 py-2"><?= $days_passed ?> ڕۆژ</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- FontAwesome بۆ Icon -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>

    <script>
    function toggleDropdown() {
        var dropdownMenu = document.getElementById('dropdownMenu');
        dropdownMenu.classList.toggle('hidden');
    }

    function sortTable(order) {
        var currentUrl = window.location.href;
        var newUrl = new URL(currentUrl);
        newUrl.searchParams.set('order', order);
        window.location.href = newUrl.toString();
    }
    </script>

</body>
</html>
