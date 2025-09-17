<?php
// chat-server.php
require __DIR__.'/../vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

class Chat implements MessageComponentInterface {
    protected $clients;
    protected $userConnections;
    protected $pdo;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->userConnections = [];

        // DB connection
        $this->pdo = new PDO(
            'mysql:host=localhost;dbname=gogrocery;charset=utf8mb4',
            'root', '', // <-- change if you have password
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
        echo "✅ Chat server started on ws://localhost:8080\n";
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);

        // Parse ?user_id= in query string
        $query = $conn->httpRequest->getUri()->getQuery();
        parse_str($query, $params);
        $userId = isset($params['user_id']) ? (int)$params['user_id'] : null;
        $conn->userId = $userId;

        if ($userId) {
            $this->userConnections[$userId] = $conn;
            echo "👤 User $userId connected\n";
        }
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg, true);
        if (!$data) return;

        if ($data['type'] === 'message') {
            $conversation_id = (int)$data['conversation_id'];
            $sender_id = $from->userId ?? (int)$data['sender_id'];
            $content = strtolower(trim((string)$data['content']));
            echo "📩 User $sender_id: $content\n";

            // Save user message
            $stmt = $this->pdo->prepare("INSERT INTO messages (conversation_id, sender_id, content) VALUES (?, ?, ?)");
            $stmt->execute([$conversation_id, $sender_id, $content]);
            $messageId = (int)$this->pdo->lastInsertId();

            // Broadcast user message
            $payload = json_encode([
                'type' => 'message',
                'message' => [
                    'id' => $messageId,
                    'conversation_id' => $conversation_id,
                    'sender_id' => $sender_id,
                    'content' => $content,
                    'created_at' => date('c')
                ]
            ]);
            foreach ($this->clients as $client) {
                $client->send($payload);
            }

            // --- Bot Auto Reply ---
            $botReply = null;
            if (in_array($content, ["hi", "hello", "hey"])) {
                $botReply = "Hello 👋 I'm GoGrocery Assistant. How can I help you today?";
            } elseif (strpos($content, "price") !== false) {
                $botReply = "💰 Could you tell me which product you want the price for?";
            } elseif (strpos($content, "delivery") !== false) {
                $botReply = "🚚 Our standard delivery takes 2-3 working days.";
            } elseif (strpos($content, "bye") !== false) {
                $botReply = "Goodbye! 👋 Have a nice day.";
            } else {
                $botReply = "🤖 I'm still learning. Please rephrase or wait for a human agent.";
            }

            // Save + send bot reply
            $stmt = $this->pdo->prepare("INSERT INTO messages (conversation_id, sender_id, content) VALUES (?, ?, ?)");
            $stmt->execute([$conversation_id, 0, $botReply]);
            $botMessageId = (int)$this->pdo->lastInsertId();

            $botPayload = json_encode([
                'type' => 'message',
                'message' => [
                    'id' => $botMessageId,
                    'conversation_id' => $conversation_id,
                    'sender_id' => 0,
                    'content' => $botReply,
                    'created_at' => date('c')
                ]
            ]);
            foreach ($this->clients as $client) {
                $client->send($botPayload);
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        if (!empty($conn->userId) && isset($this->userConnections[$conn->userId])) {
            unset($this->userConnections[$conn->userId]);
            echo "❌ User {$conn->userId} disconnected\n";
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "💥 Error: {$e->getMessage()}\n";
        $conn->close();
    }
}

$server = IoServer::factory(new HttpServer(new WsServer(new Chat())), 8080);
$server->run();
