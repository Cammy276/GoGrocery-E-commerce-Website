<?php
session_start();
require __DIR__ . '/../connect_db.php';

$userId = $_SESSION['user_id'];
$conversationId = (int)($_POST['conversation_id'] ?? 0);
$content = trim($_POST['content'] ?? '');

if ($conversationId && $content) {
    // Save user message
    $stmt = $pdo->prepare("INSERT INTO messages (conversation_id, sender_id, content) VALUES (?, ?, ?)");
    $stmt->execute([$conversationId, $userId, $content]);

    // Auto bot reply
    $reply = null;
    if (in_array(strtolower($content), ["hi","hello"])) {
        $reply = "Hello! How can I help you today?";
    } elseif (strpos(strtolower($content), "bye") !== false) {
        $reply = "Goodbye! Have a nice day!";
    }

    if ($reply) {
        $stmt = $pdo->prepare("INSERT INTO messages (conversation_id, sender_id, content) VALUES (?, ?, ?)");
        $stmt->execute([$conversationId, 0, $reply]); // 0 = bot
    }

    echo json_encode(["status" => "ok"]);
} else {
    echo json_encode(["status" => "error", "msg" => "Invalid input"]);
}
