<?php
include __DIR__ . '/../livechat/chat_UI.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <header>
        <?php include '../header.php'; ?>
    </header>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/header_styles.css">
    <link rel="stylesheet" href="../css/forgot_password_styles.css">
    <link rel="stylesheet" href="../css/footer_styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="forgot-password-container">
        <div class="forgot-password-box">
            <h2>Forgot Password</h2>
            <p id="info-message">Please enter your registered email. A reset password link will be sent to this email.</p>
            <form method="POST" action="./send_password_reset.php">
                <div class="input-container">
                    <label for="email">Email</label>
                    <input type="text" id="email" name="email" placeholder="Enter your email">
                    <small id="email-error" class="error-message"></small>
                </div>
                <div class="action-buttons">
                    <button type="submit" id="sendBtn" class="send-btn">Send</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const emailInput = document.getElementById("email");
        const emailError = document.getElementById("email-error");
        const sendBtn = document.getElementById("sendBtn");
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        // Check if email exists in DB via AJAX
        async function checkEmailExists(email) {
            const response = await fetch("./check_email.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: "email=" + encodeURIComponent(email)
            });
            return response.json();
        }

        // Real-time validation
        emailInput.addEventListener("blur", async () => {
            const email = emailInput.value.trim();

            if (email === "") {
                emailError.innerText = "Please enter your email address";
                emailError.style.display = "block";
                emailInput.classList.add("error");
                return;
            }

            if (!emailPattern.test(email)) {
                emailError.innerText = "Please enter a valid email address";
                emailError.style.display = "block";
                emailInput.classList.add("error");
                return;
            }

            // Check DB
            const result = await checkEmailExists(email);
            if (!result.exists) {
                emailError.innerText = "This email is not registered.";
                emailError.style.display = "block";
                emailInput.classList.add("error");
            } else {
                emailError.style.display = "none";
                emailInput.classList.remove("error");
            }
        });

        // On Send click
        sendBtn.addEventListener("click", function(event) {
            if (emailInput.classList.contains("error") || emailInput.value.trim() === "") {
                event.preventDefault();
                emailInput.focus();
            }
        });
    </script>
    <?php include '../footer.php'; ?>
</body>
</html>
