<?php
session_start();
include(__DIR__ . '/../../connect_db.php');

$response = ['email' => false, 'phone' => false];

if (!isset($_SESSION['user_id'])) {
    echo json_encode($response);
    exit;
}

$user_id = $_SESSION['user_id'];
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';

//email duplicate
if ($email) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = ? AND user_id != ?");
    $stmt->bind_param("si", $email, $user_id);
    $stmt->execute();
    $stmt->bind_result($emailCount);
    $stmt->fetch();
    if ($emailCount > 0) $response['email'] = true;
    $stmt->close();
}

//phone duplicate
if ($phone) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE phone = ? AND user_id != ?");
    $stmt->bind_param("si", $phone, $user_id);
    $stmt->execute();
    $stmt->bind_result($phoneCount);
    $stmt->fetch();
    if ($phoneCount > 0) $response['phone'] = true;
    $stmt->close();
}

echo json_encode($response);
?>