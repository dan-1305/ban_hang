<?php
// SỬA LẠI THÀNH THÔNG SỐ LOCAL ĐỂ CHẠY TRÊN XAMPP
$host = 'localhost'; 
$db_name = 'quan_ly_ban_hang'; // Tên DB trong phpMyAdmin máy ông
$username = 'root'; 
$password = ''; // XAMPP mặc định mật khẩu trống

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // Trả về JSON thay vì echo chữ để tránh lỗi "Unexpected token L"
    header('Content-Type: application/json');
    die(json_encode(["error" => "Kết nối thất bại: " . $e->getMessage()]));
}
?>