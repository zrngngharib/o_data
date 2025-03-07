<?php
session_start();
include '../../includes/db.php';

// Adding completion_date column to tasks table
$alter_table_query = "ALTER TABLE tasks ADD COLUMN completion_date DATETIME";
mysqli_query($conn, $alter_table_query);
?>