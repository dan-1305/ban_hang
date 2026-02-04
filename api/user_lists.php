<?php
include 'db.php';
$page = $_GET['page'] ?? 1;
$limit = 5; // Số user trên mỗi trang
$offset = ($page - 1) * $limit;
$search = $_GET['search'] ?? '';

// 1. Tính tổng số user để biết có bao nhiêu trang
$countSql = "SELECT COUNT(*) FROM users";
if ($search) $countSql .= " WHERE username LIKE :s OR email LIKE :s";
$stmt = $conn->prepare($countSql);
if ($search) $stmt->execute([':s' => "%$search%"]); else $stmt->execute();
$totalRows = $stmt->fetchColumn();
$totalPages = ceil($totalRows / $limit);

// 2. Lấy dữ liệu trang hiện tại
$sql = "SELECT u.*, r.role_name FROM users u LEFT JOIN roles r ON u.role_id = r.id";
if ($search) $sql .= " WHERE u.username LIKE :s OR u.email LIKE :s";
$sql .= " LIMIT $limit OFFSET $offset";

$stmt = $conn->prepare($sql);
if ($search) $stmt->execute([':s' => "%$search%"]); else $stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['users' => $users, 'totalPages' => $totalPages]);