<?php
include 'db.php';

$id = $_POST['id'] ?? '';

try {
    $sql = "DELETE FROM users WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':id' => $id]);
    echo json_encode(["status" => "success", "message" => "Đã xóa người dùng!"]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>