<?php
header('Content-Type: application/json');

$mysqli = require __DIR__ . "/../connect_db.php";

$email = $_POST['email'] ?? '';

if (!$email) {
    echo json_encode(["exists" => false]);
    exit;
}

$sql = "SELECT 1 FROM users WHERE email = ? LIMIT 1";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(["exists" => true]);
} else {
    echo json_encode(["exists" => false]);
}
?>
