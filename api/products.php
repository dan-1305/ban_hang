<?php
include 'db.php';

// Hứng tham số phân trang và tìm kiếm (để mượt như trang User)
$page = $_GET['page'] ?? 1;
$limit = 6; // Hiện 6 sản phẩm mỗi trang cho đẹp giao diện Card
$offset = ($page - 1) * $limit;
$search = $_GET['search'] ?? '';

try {
    // 1. Đếm tổng số để phân trang
    $countSql = "SELECT COUNT(*) FROM products WHERE product_name LIKE :s";
    $stmt = $conn->prepare($countSql);
    $stmt->execute([':s' => "%$search%"]);
    $totalRows = $stmt->fetchColumn();
    $totalPages = ceil($totalRows / $limit);

    // 2. Query "thần thánh": Kết nối 3 bảng Products, Categories và Product_Images
    $sql = "SELECT p.*, c.category_name, i.image_url 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            LEFT JOIN product_images i ON p.id = i.product_id AND i.is_main = 1
            WHERE p.product_name LIKE :s
            ORDER BY p.id DESC 
            LIMIT $limit OFFSET $offset";

    $stmt = $conn->prepare($sql);
    $stmt->execute([':s' => "%$search%"]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'products' => $products,
        'totalPages' => $totalPages,
        'currentPage' => (int)$page
    ]);
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>