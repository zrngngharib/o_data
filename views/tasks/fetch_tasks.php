<?php
include '../../includes/db.php';

$tasks_per_page = 20;
$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
$offset = ($page - 1) * $tasks_per_page;
$sort = isset($_POST['sort']) ? $_POST['sort'] : 'newest';
$search = isset($_POST['search']) ? mysqli_real_escape_string($conn, $_POST['search']) : '';

$order_by = 'date DESC';

switch ($sort) {
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

// داواکردنی کارەکان لە داتابەیس
$query = "SELECT * FROM tasks WHERE 
          (task_name LIKE '%$search%' OR 
          location LIKE '%$search%' OR 
          employee LIKE '%$search%' OR
          team LIKE '%$search%' OR
          status LIKE '%$search%' OR           
          mobile_number LIKE '%$search%')          
          ORDER BY $order_by 
          LIMIT $tasks_per_page OFFSET $offset";

$result = mysqli_query($conn, $query);

// ژمارەی گشتی کارەکان بۆ پەیجینەیشن
$query_total = "SELECT COUNT(*) as total FROM tasks WHERE 
                (task_name LIKE '%$search%' OR 
                location LIKE '%$search%' OR 
                employee LIKE '%$search%' OR
                team LIKE '%$search%' OR  
                status LIKE '%$search%' OR               
                mobile_number LIKE '%$search%')";
$result_total = mysqli_query($conn, $query_total);
$row_total = mysqli_fetch_assoc($result_total);
$total_tasks = $row_total['total'];
$total_pages = ceil($total_tasks / $tasks_per_page);

?>

<table border="1">
    <tr>
        <th>دیاریکردن</th>
        <th>ئەرك</th>
        <th>ژمارە</th>
        <th>شوێن</th>
        <th>کارمەند</th>
        <th>حاڵەت</th>
        <th>کردار</th>
    </tr>
    <?php if ($total_tasks > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><input type="checkbox" name="selected_tasks[]" value="<?= $row['id'] ?>"></td>
                <td><?= htmlspecialchars($row['task_name']) ?></td>
                <td><?= htmlspecialchars($row['task_number']) ?></td>
                <td><?= htmlspecialchars($row['location']) ?></td>
                <td><?= htmlspecialchars($row['employee']) ?></td>
                <td><?= htmlspecialchars($row['status']) ?></td>
                <td>
                    <a href='edit_task.php?id=<?= $row['id'] ?>'>دەستکاری</a> |
                    <a href='copy_task.php?id=<?= $row['id'] ?>'>کۆپی</a> |
                    <a href='delete_task.php?id=<?= $row['id'] ?>'>سڕینەوە</a> |
                    <a href='complete_tasks.php?id=<?= $row['id'] ?>'>تەواوکردن</a>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="7" style="text-align: center; color: red;">هیچ کارێک نەدۆزرایەوە</td>
        </tr>
    <?php endif; ?>
</table>

<!-- پەیجینەیشن -->
<div>
    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a href="javascript:void(0)" onclick="loadTasks(<?= $i ?>)"><?= $i ?></a>
    <?php endfor; ?>
</div>
