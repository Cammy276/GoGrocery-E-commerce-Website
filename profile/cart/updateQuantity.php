<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

include(__DIR__ . '/../../connect_db.php');

if (isset($_POST['cart_id'], $_POST['quantity'])) {
    $cart_id = intval($_POST['cart_id']);
    $quantity = max(1, intval($_POST['quantity']));

    $stmt = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE cart_item_id = ? AND user_id = ?");
    $stmt->bind_param("iii", $quantity, $cart_id, $user_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Missing parameters']);
}

$conn->close();
?>
