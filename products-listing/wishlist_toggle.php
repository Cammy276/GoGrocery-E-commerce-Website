<?php
session_start();
include('../connect_db.php');

$user_id = $_SESSION['user_id'] ?? null;
if(!$user_id){
    echo json_encode(['success'=>false,'status'=>'not_logged_in']);
    exit;
}

// Read JSON input
$input = json_decode(file_get_contents('php://input'), true);
$product_id = intval($input['product_id'] ?? 0);
$action = $input['action'] ?? '';

if(!$product_id){
    echo json_encode(['success'=>false,'status'=>'invalid_product']);
    exit;
}

// Check if already in wishlist
$stmt = $conn->prepare("SELECT 1 FROM wishlist WHERE user_id=? AND product_id=?");
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows){
    // remove
    $del = $conn->prepare("DELETE FROM wishlist WHERE user_id=? AND product_id=?");
    $del->bind_param("ii", $user_id, $product_id);
    $del->execute();
    echo json_encode(['success'=>true, 'status'=>'removed']);
}else{
    // add
    $ins = $conn->prepare("INSERT INTO wishlist(user_id,product_id) VALUES (?,?)");
    $ins->bind_param("ii", $user_id, $product_id);
    $ins->execute();
    echo json_encode(['success'=>true, 'status'=>'added']);
}
