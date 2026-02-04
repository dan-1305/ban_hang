<?php
$host = "localhost";
$db_name = "quan_ly_ban_hang";
$username = "root";
$password = ""; // XAMPP mặc định để trống
try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Lỗi kết nối: " . $e->getMessage();
}
?>