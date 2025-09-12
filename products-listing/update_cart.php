<?php
session_start();
include(__DIR__ . '/../connect_db.php');

header('Content-Type: application/json');

// Require login
$user_id = $_SESSION['user_id'] ?? 0;
if ($user_id <= 0) {
    echo json_encode(["success" => false, "message" => "Login required"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$product_id = intval($data['product_id'] ?? 0);
$action     = $data['action'] ?? '';
$qty        = intval($data['quantity'] ?? 1);

if ($product_id <= 0 || !$action) {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
    exit;
}

// fetch product (for price, sku, name)
$stmt = $conn->prepare("SELECT product_id, product_name, sku, unit_price FROM products WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    echo json_encode(["success" => false, "message" => "Product not found"]);
    exit;
}

// check existing cart row
$stmt = $conn->prepare("SELECT quantity FROM cart_items WHERE user_id=? AND product_id=?");
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();
$res = $stmt->get_result();
$current_qty = ($r = $res->fetch_assoc()) ? intval($r['quantity']) : 0;

$new_qty = $current_qty;
$message = "";

// ================= APPLY ACTION =================
if ($action === 'add') {
    if ($current_qty === 0) {
        $new_qty = max(1, $qty);
        $message = "Added to cart";
    } else {
        $new_qty = $current_qty + max(1, $qty);
        $message = "Quantity updated";
    }

} elseif ($action === 'increase') {
    $new_qty = $current_qty + 1;
    $message = "Quantity updated";

} elseif ($action === 'decrease') {
    $new_qty = $current_qty - 1;
    $message = ($new_qty > 0) ? "Quantity updated" : "Removed from cart";

} elseif ($action === 'set') {
    if ($qty <= 0) {
        $new_qty = 0;
        $message = "Removed from cart";
    } elseif ($current_qty === 0) {
        $new_qty = $qty;
        $message = "Added to cart";
    } else {
        $new_qty = $qty;
        $message = "Quantity updated";
    }

} elseif ($action === 'remove') {
    $new_qty = 0;
    $message = "Removed from cart";

} else {
    echo json_encode(["success" => false, "message" => "Unknown action"]);
    exit;
}

// ================= UPDATE SESSION + DB =================
if ($new_qty > 0) {
    // Session
    $_SESSION['cart'][$product_id] = $new_qty;

    // DB: insert or update
    $stmt = $conn->prepare("INSERT INTO cart_items 
        (user_id, product_id, product_name, sku, unit_price, quantity, line_discount) 
        VALUES (?, ?, 
            (SELECT product_name FROM products WHERE product_id = ?), 
            (SELECT sku FROM products WHERE product_id = ?), 
            (SELECT unit_price FROM products WHERE product_id = ?), 
            ?, 0.00
        )
        ON DUPLICATE KEY UPDATE quantity = VALUES(quantity), updated_at = CURRENT_TIMESTAMP");

    $stmt->bind_param("iiiiii", $user_id, $product_id, $product_id, $product_id, $product_id, $new_qty);
    $stmt->execute();

} else {
    // Remove from session
    unset($_SESSION['cart'][$product_id]);

    // Remove from DB
    $stmt = $conn->prepare("DELETE FROM cart_items WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
}

// recalc totals
$stmt = $conn->prepare("
    SELECT 
      COALESCE(SUM(quantity),0) AS total_qty,
      COALESCE(SUM((unit_price - line_discount) * quantity),0) AS total_price
    FROM cart_items
    WHERE user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$totals = $stmt->get_result()->fetch_assoc();

echo json_encode([
    "success"     => true,
    "message"     => $message,
    "new_qty"     => $new_qty,
    "total_qty"   => intval($totals['total_qty'] ?? 0),
    "total_price" => floatval($totals['total_price'] ?? 0)
]);
