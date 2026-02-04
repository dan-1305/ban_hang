<?php
include 'db.php'; // Đảm bảo db.php của ông đã có thông số host thật nhé

// Chỉ nhận dữ liệu qua phương thức POST để bảo mật
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'] ?? null;
    $rating = $_POST['rating'] ?? null;

    if ($product_id && $rating) {
        try {
            $sql = "INSERT INTO product_ratings (product_id, rating) VALUES (:pid, :rat)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':pid' => $product_id,
                ':rat' => $rating
            ]);

            echo json_encode(["status" => "success", "message" => "Đã lưu $rating sao cho ông Danh!"]);
        } catch (PDOException $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Thiếu dữ liệu rồi ông ơi!"]);
    }
}
?>