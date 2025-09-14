<?php
require 'db.php';

$conversationId = (int)($_GET['conversation_id'] ?? 0);
$afterId = (int)($_GET['after_id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM messages WHERE conversation_id = ? AND id > ? ORDER BY id ASC");
$stmt->execute([$conversationId, $afterId]);
$messages = $stmt->fetchAll();

header('Content-Type: application/json');
echo json_encode(["messages" => $messages]);
