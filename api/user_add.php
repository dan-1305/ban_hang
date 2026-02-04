<?php
include 'db.php';

$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '123456'; // Mặc định nếu trống
$role_id = $_POST['role_id'] ?? 2;

// Băm mật khẩu bằng thuật toán BCRYPT
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

try {
    $sql = "INSERT INTO users (username, email, password, role_id) VALUES (:username, :email, :password, :role_id)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':username' => $username,
        ':email' => $email,
        ':password' => $hashed_password,
        ':role_id' => $role_id
    ]);
    echo json_encode(["status" => "success", "message" => "Thêm người dùng thành công!"]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>