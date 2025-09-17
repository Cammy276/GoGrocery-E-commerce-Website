<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include(__DIR__ . '/../connect_db.php');

// Check if user is logged in
$loggedIn = isset($_SESSION['user_id']);

if ($loggedIn) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT name, email, phone FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($name, $email, $phone);
    $stmt->fetch();
    $stmt->close();
} else {
    $name = $email = $phone = "";
}

// Capture previous input values and errors from GET
$prevSubject = $_GET['prev_subject'] ?? '';
$prevComment = $_GET['prev_comment'] ?? '';
$subjectErrorMsg = $_GET['subject_error'] ?? '';
$commentErrorMsg = $_GET['comment_error'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Form</title>
  <link rel="stylesheet" href="../css/styles.css">
  <link rel="stylesheet" href="../css/contact_form_styles.css">
  <style>
    /* Overlay styling for login prompt */
    #loginOverlay {
      display: none;
      position: fixed;
      top: 0; left: 0; width: 100%; height: 100%;
      background-color: rgba(0,0,0,0.3);
      backdrop-filter: blur(5px);
      z-index: 9999;
      justify-content: center;
      align-items: center;
    }
    #loginOverlay .message-box {
      background-color: rgba(255,255,255,0.95);
      padding: 30px 40px;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.3);
      text-align: center;
      max-width: 400px;
      animation: fadeIn 1s ease-in-out;
    }
    #loginOverlay .message-box h3 { margin-bottom: 15px; font-size: 1.3rem; }
    #loginOverlay .message-box p { margin-bottom: 10px; font-size: 0.95rem; color: #333; }
    #loginOverlay .message-box a { color: #118997; text-decoration: underline; }
    @keyframes fadeIn { from {opacity:0; transform:translateY(-20px);} to {opacity:1; transform:translateY(0);} }
  </style>
</head>
<body>
<div class="contact-form-container">
  <p>If you have any questions or need assistance, please fill out the form below. Our support team will get back to you as soon as possible.</p>
<!-- Success / Error Messages -->
<?php if (isset($_GET['status']) && $_GET['status'] === "success"): ?>
  <p class="success-message">Message sent successfully!</p>
<?php elseif (isset($_GET['status']) && $_GET['status'] === "error"): ?>
  <p class="server-error">There was an error sending your message. Please try again.</p>
<?php endif; ?>

<!-- Contact Form -->
<form id="contactForm" action="./send_contact_form.php" method="POST" enctype="multipart/form-data">
  <div class="inline-group">
    <div class="field">
      <label for="name">Name:</label>
      <input type="text" id="name" name="name" value="<?= htmlspecialchars($name) ?>" <?= $loggedIn ? 'readonly' : '' ?>>
    </div>
    <div class="field">
      <label for="email">Email:</label>
      <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" <?= $loggedIn ? 'readonly' : '' ?>>
    </div>
    <div class="field">
      <label for="phone">Phone:</label>
      <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($phone) ?>" <?= $loggedIn ? 'readonly' : '' ?>>
    </div>
  </div>

  <div class="full-width-field">
    <label for="subject">Subject:</label>
    <input type="text" id="subject" name="subject" required placeholder="Enter your subject"
           value="<?= htmlspecialchars($prevSubject) ?>">
    <div id="subjectError" class="error-message"><?= htmlspecialchars($subjectErrorMsg) ?></div>
  </div>

  <div class="full-width-field">
    <label for="comment">Comment:</label>
    <textarea id="comment" name="comment" required placeholder="Enter your comment" maxlength="500"><?= htmlspecialchars($prevComment) ?></textarea>
    <small id="commentCounter" class="helper-text"><?= strlen($prevComment) ?> / 500 characters</small>
    <div id="commentError" class="error-message"><?= htmlspecialchars($commentErrorMsg) ?></div>
  </div>

  <div class="full-width-field">
    <label for="contact_image_url">Attach Image (optional):</label>
    <input type="file" id="contact_image_url" name="contact_image_url" accept="image/*">
  </div>

  <div class="full-width-field">
    <button type="submit">Send</button>
  </div>
</form>
<!-- Login overlay for non-logged-in users -->
<?php if (!$loggedIn): ?>
<div id="loginOverlay">
  <div class="message-box">
    <h3>Login Required</h3>
    <p>The Contact Us feature requires you to log in to continue.</p>
    <p>You will be redirected to the login page in 5 seconds...</p>
    <a href="../auth/login.php?redirect=../help/contact.php">Click here if not redirected</a>
  </div>
</div>
<?php endif; ?>

<script>
const subjectInput = document.getElementById("subject");
const commentInput = document.getElementById("comment");
const subjectError = document.getElementById("subjectError");
const commentError = document.getElementById("commentError");

document.getElementById("contactForm").addEventListener("submit", function (e) {
    let isValid = true;
    [subjectError, commentError].forEach(el => el.style.display = "none");
    [subjectInput, commentInput].forEach(el => el.classList.remove("error"));

    if (subjectInput.value.trim() === "") {
        subjectError.innerText = "Subject is required";
        subjectError.style.display = "block";
        subjectInput.classList.add("error");
        isValid = false;
    }
    if (commentInput.value.trim() === "") {
        commentError.innerText = "Comment is required";
        commentError.style.display = "block";
        commentInput.classList.add("error");
        isValid = false;
    }
    if (commentInput.value.length > 500) {
        commentError.innerText = "Comment cannot exceed 500 characters";
        commentError.style.display = "block";
        commentInput.classList.add("error");
        isValid = false;
    }

    if (!isValid) e.preventDefault();
});

// Live character counter
const commentCounter = document.getElementById("commentCounter");
commentInput.addEventListener("input", () => {
    commentCounter.textContent = `${commentInput.value.length} / 500 characters`;
});

// Show login overlay on first focus for non-logged-in users
<?php if (!$loggedIn): ?>
const form = document.getElementById('contactForm');
const overlay = document.getElementById('loginOverlay');
form.addEventListener('focusin', function() {
    overlay.style.display = 'flex';
    setTimeout(() => {
        window.location.href = '../auth/login.php?redirect=../help/contact.php';
    }, 5000);
}, { once: true });
<?php endif; ?>
</script>
</div>
</body>
</html>
