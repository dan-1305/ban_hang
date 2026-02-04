<?php
include 'db.php';

$id = $_POST['id'] ?? '';
$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$role_id = $_POST['role_id'] ?? 2;

try {
    if (!empty($password)) {
        // Nếu có nhập mật khẩu mới -> Cập nhật cả pass
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET username = :username, email = :email, password = :password, role_id = :role_id WHERE id = :id";
        $params = [
            ':username' => $username,
            ':email' => $email,
            ':password' => $hashed_password,
            ':role_id' => $role_id,
            ':id' => $id
        ];
    } else {
        // Nếu không nhập pass -> Giữ nguyên pass cũ
        $sql = "UPDATE users SET username = :username, email = :email, role_id = :role_id WHERE id = :id";
        $params = [
            ':username' => $username,
            ':email' => $email,
            ':role_id' => $role_id,
            ':id' => $id
        ];
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    echo json_encode(["status" => "success", "message" => "Cập nhật thành công!"]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>