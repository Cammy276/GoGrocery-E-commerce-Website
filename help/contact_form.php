<?php
require __DIR__ . '/../init.php';

// If user not logged in, show notice and redirect
if (!isset($_SESSION['user_id'])) {
    echo "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Login Required</title>
        <meta http-equiv='refresh' content='3;url=../auth/login.php?redirect=../help/contact_form.php'>
        <link rel='stylesheet' href='../css/styles.css'>
    </head>
    <body>
        <div class='message'>
            <h3>Login Required</h3>
            <p>The Contact Us feature requires you to log in to continue.</p>
            <p>You will be redirected to the login page in 3 seconds...</p>
            <a href='../auth/login.php?redirect=../help/contact_form.php'>Click here if you are not redirected</a>
        </div>
    </body>
    </html>";
    exit;
}

$user_id = $_SESSION['user_id'];

// Retrieve user info
$stmt = $conn->prepare("SELECT name, email, phone FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $email, $phone);
$stmt->fetch();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Form</title>
  <link rel="stylesheet" href="../css/styles.css">
  <link rel="stylesheet" href="../css/contact_form_styles.css">
</head>
<body>
  <!-- Success / error message after redirect -->
  <?php if (isset($_GET['status']) && $_GET['status'] === "success"): ?>
    <p class="success-message">Message sent successfully!</p>
  <?php elseif (isset($_GET['status']) && $_GET['status'] === "error"): ?>
    <p class="server-error">There was an error sending your message. Please try again.</p>
  <?php endif; ?>

  <form id="contactForm" action="send_contact_form.php" method="POST" enctype="multipart/form-data">
    <div class="inline-group">
        <div class="field">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($name) ?>" readonly>
            <div id="nameError" class="error-message"></div>
        </div>
        <div class="field">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" readonly>
            <div id="emailError" class="error-message"></div>
        </div>
        <div class="field">
            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($phone) ?>" readonly>
            <div id="phoneError" class="error-message"></div>
        </div>
    </div>

    <!-- Subject, Comment, Attach, and Send span across all three -->
    <div class="full-width-field">
      <label for="subject">Subject:</label>
      <input type="text" id="subject" name="subject" required placeholder="Enter your subject">
      <div id="subjectError" class="error-message"></div>
    </div>

    <div class="full-width-field">
      <label for="comment">Comment:</label>
      <textarea id="comment" name="comment" required placeholder="Enter your comment" maxlength="500"></textarea>
      <small id="commentCounter" class="helper-text">0 / 500 characters</small>
      <div id="commentError" class="error-message"></div>
    </div>

    <div class="full-width-field">
      <label for="contact_image_url">Attach Image (optional):</label>
      <input type="file" id="contact_image_url" name="contact_image_url" accept="image/*">
    </div>

    <div class="full-width-field">
      <button type="submit">Send</button>
    </div>
  </form>

  <script>
    const subjectInput = document.getElementById("subject");
    const commentInput = document.getElementById("comment");
    const subjectError = document.getElementById("subjectError");
    const commentError = document.getElementById("commentError");

    document.getElementById("contactForm").addEventListener("submit", function (e) {
        let isValid = true;

        // reset all
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

        if (!isValid) {
            e.preventDefault(); // stop form submission
        }
    });

    // live counter for comment only
    const commentCounter = document.getElementById("commentCounter");
    commentInput.addEventListener("input", () => {
        commentCounter.textContent = `${commentInput.value.length} / 500 characters`;
    });
  </script>
</body>
</html>
