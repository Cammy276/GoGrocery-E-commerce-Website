<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>GoGrocery Live Chat</title>
<link rel="stylesheet" href="../css/livechat_styles.css">
</head>
<body>

<!-- Sticky Chat Icon -->
<div id="chat-icon" title="Chat with us!">💬</div>
<!-- Chat Window -->
<div id="chat-window">
  <div id="chat-header">
    <span>GoGrocery Assistant</span>
    <button id="close-chat">✖</button>
  </div>
  <div id="chat-messages"></div>
  <div id="chat-input">
    <input type="text" id="chat-message" placeholder="Type a message...">
    <button id="send-chat">Send</button>
  </div>
</div>

<script>
// Dynamic user ID from PHP session
const userId = <?= $user_id ?>;

// Elements
const chatIcon = document.getElementById('chat-icon');
const chatWindow = document.getElementById('chat-window');
const closeBtn = document.getElementById('close-chat');
const sendBtn = document.getElementById('send-chat');
const chatMessages = document.getElementById('chat-messages');
const chatInput = document.getElementById('chat-message');

// Toggle chat window
chatIcon.addEventListener('click', () => {
    chatWindow.style.display = chatWindow.style.display === 'flex' ? 'none' : 'flex';
});

// Close button
closeBtn.addEventListener('click', () => {
    chatWindow.style.display = 'none';
});

// WebSocket connection
const ws = new WebSocket(`ws://localhost:8080/chat?user_id=${userId}`);

// Function to add message to chat window
function addMessage(content, isUser) {
    const msgDiv = document.createElement('div');
    msgDiv.classList.add('message');
    msgDiv.classList.add(isUser ? 'user-message' : 'bot-message');
    msgDiv.textContent = content;
    chatMessages.appendChild(msgDiv);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

// Receive messages from server
ws.onmessage = (event) => {
    const data = JSON.parse(event.data);
    if(data.type === 'message') {
        // Only display bot messages (sender_id = 0) or messages from other users if needed
        if (data.message.sender_id !== userId) {
            addMessage(data.message.content, false); // bot message
        }
    }
};

// Send message function
function sendMessage() {
    const message = chatInput.value.trim();
    if(message === '') return;

    ws.send(JSON.stringify({
        type: 'message',
        conversation_id: userId, // conversation per user
        sender_id: userId,
        content: message
    }));

    addMessage(message, true); // user message on right
    chatInput.value = '';
}

// Send on button click
sendBtn.addEventListener('click', sendMessage);

// Send on Enter key
chatInput.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') sendMessage();
});
</script>

</body>
</html>
