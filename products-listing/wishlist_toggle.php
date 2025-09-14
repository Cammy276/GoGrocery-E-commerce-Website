<?php
session_start();
include('../connect_db.php');

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(['success' => false, 'status' => 'not_logged_in']);
    exit;
}

// Read JSON input
$input = json_decode(file_get_contents('php://input'), true);
$product_id = intval($input['product_id'] ?? 0);
$action = $input['action'] ?? '';

if (!$product_id) {
    echo json_encode(['success' => false, 'status' => 'invalid_product']);
    exit;
}

$status = '';

// Perform action
if ($action === 'add') {
    $ins = $conn->prepare("INSERT IGNORE INTO wishlist(user_id, product_id) VALUES (?,?)");
    $ins->bind_param("ii", $user_id, $product_id);
    $ins->execute();
    $status = 'added';
} elseif ($action === 'remove') {
    $del = $conn->prepare("DELETE FROM wishlist WHERE user_id=? AND product_id=?");
    $del->bind_param("ii", $user_id, $product_id);
    $del->execute();
    $status = 'removed';
} else {
    // If action not recognized, toggle by checking existing
    $stmt = $conn->prepare("SELECT 1 FROM wishlist WHERE user_id=? AND product_id=?");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows) {
        $del = $conn->prepare("DELETE FROM wishlist WHERE user_id=? AND product_id=?");
        $del->bind_param("ii", $user_id, $product_id);
        $del->execute();
        $status = 'removed';
    } else {
        $ins = $conn->prepare("INSERT INTO wishlist(user_id,product_id) VALUES (?,?)");
        $ins->bind_param("ii", $user_id, $product_id);
        $ins->execute();
        $status = 'added';
    }
}

// --- Get updated wishlist count ---
$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM wishlist WHERE user_id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$wishlist_count = $stmt->get_result()->fetch_assoc()['total'] ?? 0;

// --- Get updated cart info ---
$stmt = $conn->prepare("
    SELECT 
        COALESCE(SUM(quantity),0) AS total_qty, 
        COALESCE(SUM((unit_price - line_discount) * quantity),0) AS total_price 
    FROM cart_items 
    WHERE user_id=?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cart_data = $stmt->get_result()->fetch_assoc();
$cart_count = (int)($cart_data['total_qty'] ?? 0);
$cart_total = (float)($cart_data['total_price'] ?? 0.00);

// Return full JSON including header counts
echo json_encode([
    'success' => true,
    'status' => $status,
    'wishlist_count' => $wishlist_count,
    'cart_count' => $cart_count,
    'cart_total' => $cart_total
]);
exit;
