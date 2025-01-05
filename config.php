<?php
$host = 'localhost';
$dbname = 'jahanan_db';
$username = 'root';
$password = '';

$conn = mysqli_connect($host, $username, $password, $dbname);

if (!$conn) {
    die("❌ خطا در اتصال به پایگاه‌داده: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
?>
