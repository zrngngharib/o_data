<?php
// وشەی نهێنی بە هەڵبژاردەی خۆت
$password = 'Zrngn@wr0z1670228';

// دروستکردنی hash
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// بینینی hash بۆ paste کردنی ناو داتابەیس
echo "Hashed Password: " . $hashed_password;
?>
