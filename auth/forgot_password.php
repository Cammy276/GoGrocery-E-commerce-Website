<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <header>
        <?php include '../header.php'; ?>
    </header>
    <link rel="stylesheet" href="../css/header_styles.css">
    <link rel="stylesheet" href="../css/forgot_password_styles.css">
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
                    <button type="submit" class="send-btn">Send</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const emailInput = document.getElementById("email");
        const emailError = document.getElementById("email-error");
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        // Real-time validation
        emailInput.addEventListener("input", () => {
            if (emailInput.value.trim() !== "" && emailPattern.test(emailInput.value)) {
                emailError.style.display = "none";
                emailInput.classList.remove("error");
            }
        });

        document.querySelector("form").addEventListener("submit", function(event) {
            let isValid = true;

            if (emailInput.value.trim() === "") {
                emailError.innerText = "Please enter your email address";
                emailError.style.display = "block";
                emailInput.classList.add("error");
                isValid = false;
            } else if (!emailPattern.test(emailInput.value)) {
                emailError.innerText = "Please enter a valid email address";
                emailError.style.display = "block";
                emailInput.classList.add("error");
                isValid = false;
            } else {
                emailError.style.display = "none";
                emailInput.classList.remove("error");
            }

            if (!isValid) {
                event.preventDefault();
            }
        });
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("bi-eye-fill");
                icon.classList.add("bi-eye-slash-fill");
            } else {
                input.type = "password";
                icon.classList.remove("bi-eye-slash-fill");
                icon.classList.add("bi-eye-fill");
            }
        }
        
    </script>
    <?php include '../footer.php'; ?>
</body>
</html>
