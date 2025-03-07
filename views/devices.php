<?php
// ڕێچکەی پەڕگە: /c:/xampp/htdocs/o_data/views/devices.php

include '../includes/db.php';

// پشکنینی ئەگەر بەکارهێنەر چوونەژورەوە
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

// زیادکردنی ئامێرەکان
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_device'])) {
    $device_name = $_POST['device_name'];
    $barcode = $_POST['barcode'];
    $unit = $_POST['unit'];
    $additional_labels = json_encode($_POST['additional_labels']);
    $date_added = $_POST['date_added'];

    $sql = "INSERT INTO devices (device_name, barcode, unit, additional_labels, date_added) VALUES ('$device_name', '$barcode', '$unit', '$additional_labels', '$date_added')";
    if ($conn->query($sql) === TRUE) {
        echo "ئامێرەکە بە سەرکەوتوویی زیاد کرا";
    } else {
        echo "هەڵە: " . $sql . "<br>" . $conn->error;
    }
}

// گەڕان بۆ ئامێرەکان
$search = "";
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}
$sql = "SELECT * FROM devices WHERE device_name LIKE '%$search%' OR barcode LIKE '%$search%' OR unit LIKE '%$search%' OR additional_labels LIKE '%$search%'";
$result = $conn->query($sql);
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
    }
</style>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ئامێرەکان</title>
    <link rel="stylesheet" href="../css/styles.css">
    <script>
        function addLabel() {
            var container = document.getElementById("additional_labels_container");
            var labelDiv = document.createElement("div");
            labelDiv.innerHTML = '<input type="text" name="additional_labels[]" placeholder="ناوی لەیبڵ"><input type="text" name="additional_labels[]" placeholder="نرخی لەیبڵ">';
            container.appendChild(labelDiv);
        }
    </script>
</head>
<body>
    <h2>ئامێرەکان</h2>
    <form method="post" action="">
        <label for="device_name">ناوی ئامێر:</label>
        <input type="text" id="device_name" name="device_name">
        <label for="barcode">باركۆد یان سڕیاڵ:</label>
        <input type="text" id="barcode" name="barcode">
        <label for="unit">پێوانە:</label>
        <input type="text" id="unit" name="unit">
        <div id="additional_labels_container">
            <label>لەیبڵی زیادکراو:</label>
            <input type="text" name="additional_labels[]" placeholder="ناوی لەیبڵ">
            <input type="text" name="additional_labels[]" placeholder="نرخی لەیبڵ">
        </div>
        <button type="button" onclick="addLabel()">زیادکردنی لەیبڵی دیكە</button>
        <label for="date_added">بەرواری زیادکردن:</label>
        <input type="datetime-local" id="date_added" name="date_added">
        <button type="submit" name="add_device">زیادکردن</button>
    </form>

    <h2>گەڕان بۆ ئامێرەکان</h2>
    <form method="get" action="">
        <input type="text" name="search" value="<?php echo $search; ?>">
        <button type="submit">گەڕان</button>
    </form>

    <h2>لیستی ئامێرەکان</h2>
    <table>
        <tr>
            <th>ناوی ئامێر</th>
            <th>باركۆد یان سڕیاڵ</th>
            <th>پێوانە</th>
            <th>لەیبڵی زیادکراو</th>
            <th>بەرواری زیادکردن</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['device_name'] . "</td>";
                echo "<td>" . $row['barcode'] . "</td>";
                echo "<td>" . $row['unit'] . "</td>";
                echo "<td>" . implode(", ", json_decode($row['additional_labels'])) . "</td>";
                echo "<td>" . $row['date_added'] . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>هیچ ئامێرێک نەدۆزرایەوە</td></tr>";
        }
        ?>
    </table>

<!-- Bootstrap Bundle JS (including Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
