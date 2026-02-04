<?php
// api/cart.php
session_start(); // Bắt buộc phải có để dùng Session
header('Content-Type: application/json');

// Lấy action từ GET (add, view, delete, update)
$action = $_GET['action'] ?? 'view';

// Khởi tạo giỏ hàng nếu chưa có
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// 1. THÊM SẢN PHẨM
if ($action == 'add' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;
    $name = $_POST['name'] ?? '';
    $price = $_POST['price'] ?? 0;
    $image = $_POST['image'] ?? '';
    $quantity = 1;

    if ($id) {
        // Nếu sản phẩm đã có trong giỏ -> Tăng số lượng
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity'] += 1;
        } else {
            // Nếu chưa có -> Thêm mới
            $_SESSION['cart'][$id] = [
                'id' => $id,
                'name' => $name,
                'price' => $price,
                'image' => $image,
                'quantity' => $quantity
            ];
        }
        echo json_encode(['status' => 'success', 'message' => 'Đã thêm vào giỏ', 'total_items' => count($_SESSION['cart'])]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Thiếu ID sản phẩm']);
    }
}

// 2. XEM GIỎ HÀNG (Dùng để render ra table)
elseif ($action == 'view') {
    echo json_encode(['status' => 'success', 'data' => array_values($_SESSION['cart'])]);
}

// 3. XÓA SẢN PHẨM
elseif ($action == 'remove') {
    $id = $_POST['id'] ?? null;
    if ($id && isset($_SESSION['cart'][$id])) {
        unset($_SESSION['cart'][$id]);
    }
    echo json_encode(['status' => 'success']);
}

// 4. CẬP NHẬT SỐ LƯỢNG (Tăng/Giảm ở trang giỏ hàng)
elseif ($action == 'update') {
    $id = $_POST['id'] ?? null;
    $qty = $_POST['quantity'] ?? 1;
    if ($id && isset($_SESSION['cart'][$id])) {
        if ($qty > 0) {
            $_SESSION['cart'][$id]['quantity'] = $qty;
        } else {
            unset($_SESSION['cart'][$id]); // Nếu chỉnh về 0 thì xóa luôn
        }
    }
    echo json_encode(['status' => 'success']);
}
?>