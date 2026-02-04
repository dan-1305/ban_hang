<?php
include 'db.php';

$id = $_GET['id'] ?? 0;

try {
    // 1. Lấy thông tin chi tiết sản phẩm và danh mục
    $sql = "SELECT p.*, c.category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':id' => $id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        echo json_encode(["error" => "Không tìm thấy sản phẩm này ông Danh ơi!"]);
        exit;
    }

    // 2. Lấy TẤT CẢ hình ảnh của sản phẩm này (Gallery)
    $imgSql = "SELECT image_url, is_main FROM product_images WHERE product_id = :id";
    $imgStmt = $conn->prepare($imgSql);
    $imgStmt->execute([':id' => $id]);
    $images = $imgStmt->fetchAll(PDO::FETCH_ASSOC);

    // 3. Lấy sản phẩm tương tự (cùng Category, khác ID hiện tại)
    $similarSql = "SELECT p.*, i.image_url 
                   FROM products p 
                   LEFT JOIN product_images i ON p.id = i.product_id AND i.is_main = 1 
                   WHERE p.category_id = :cat_id AND p.id != :id 
                   LIMIT 4";
    $simStmt = $conn->prepare($similarSql);
    $simStmt->execute([':cat_id' => $product['category_id'], ':id' => $id]);
    $similar = $simStmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "product" => $product,
        "images" => $images,
        "similar" => $similar
    ]);
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>