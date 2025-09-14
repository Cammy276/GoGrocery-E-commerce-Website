<?php
$token = $_GET["token"] ?? '';

if (!$token) {
    die("Invalid token");
}

$token_hash = hash("sha256", $token);

$mysqli = require __DIR__ . "/../connect_db.php";

$sql = "SELECT * FROM users WHERE reset_token_hash = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $token_hash);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user === null) {
    die("Token not found");
}

if (strtotime($user["reset_token_expires_at"]) <= time()) {
    die("Token has expired");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/header_styles.css">
    <link rel="stylesheet" href="../css/reset_password_styles.css">
    <link rel="stylesheet" href="../css/footer_styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<header>
    <?php include '../header.php'; ?>   
</header>
<body>
    <div class="reset-container">
        <h2>Reset Password</h2>
        <form method="post" action="./process_reset_password.php">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

            <div class="input-container">
                <label for="password" id="password-label">New Password</label>
                <div style="position: relative;">
                    <input type="password" id="password" name="password" placeholder="Enter your new password">
                    <i id="password-icon" class="bi bi-eye-fill" onclick="togglePassword('password','password-icon')" style="cursor:pointer; position:absolute; right:1px; top:45%; transform:translateY(-50%);"></i>
                </div>
                <small id="password-error" class="error-message"></small>
            </div>

            <div class="input-container">
                <label for="confirm-password" id="password-confirmation-label">Confirm Password</label>
                <div style="position: relative;">
                    <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirm your password">
                    <i id="confirm-password-icon" class="bi bi-eye-fill" onclick="togglePassword('confirm-password','confirm-password-icon')" style="cursor:pointer; position:absolute; right:1px; top:45%; transform:translateY(-50%);"></i>
                </div>
                <small id="confirm-password-error" class="error-message"></small>
            </div>

            <button type="submit">Reset Password</button>
        </form>
        <div class="login-link">
            <p><a href="./login.php">Back to Login <i class="bi bi-box-arrow-up-right"></i></a></p>
        </div>
    </div>

    <script>
        const passwordInput = document.getElementById("password");
        const passwordConfirmationInput = document.getElementById("confirm-password");
        const passwordError = document.getElementById("password-error");
        const confirmPasswordError = document.getElementById("confirm-password-error");
        const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]).{8,}$/;

        // Toggle password visibility
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

        // Real-time password validation
        passwordInput.addEventListener("input", () => {
            if (passwordInput.value.trim() === "") {
                passwordError.innerText = "Password is required";
                passwordError.style.display = "block";
                passwordInput.classList.add("error");
            } else if (!passwordPattern.test(passwordInput.value)) {
                passwordError.innerText =
                    "Password must be at least 8 characters long and include uppercase, lowercase, number, and special character.";
                passwordError.style.display = "block";
                passwordInput.classList.add("error");
            } else {
                passwordError.style.display = "none";
                passwordInput.classList.remove("error");
            }

            if (passwordConfirmationInput.value !== "" && passwordConfirmationInput.value === passwordInput.value) {
                confirmPasswordError.style.display = "none";
                passwordConfirmationInput.classList.remove("error");
            }
        });

        passwordConfirmationInput.addEventListener("input", () => {
            if (passwordConfirmationInput.value === passwordInput.value && passwordConfirmationInput.value.trim() !== "") {
                confirmPasswordError.style.display = "none";
                passwordConfirmationInput.classList.remove("error");
            } else if (passwordConfirmationInput.value.trim() !== "") {
                confirmPasswordError.innerText = "Passwords do not match";
                confirmPasswordError.style.display = "block";
                passwordConfirmationInput.classList.add("error");
            }
        });

        // On form submit
        document.querySelector("form").addEventListener("submit", function (event) {
            let isValid = true;

            if (passwordInput.value.trim() === "") {
                passwordError.innerText = "Please enter a new password.";
                passwordError.style.display = "block";
                passwordInput.classList.add("error");
                isValid = false;
            } else if (!passwordPattern.test(passwordInput.value)) {
                passwordError.innerText =
                    "Password must be at least 8 characters long and include uppercase, lowercase, number, and special character.";
                passwordError.style.display = "block";
                passwordInput.classList.add("error");
                isValid = false;
            }

            if (passwordConfirmationInput.value.trim() === "") {
                confirmPasswordError.innerText = "Please confirm your password.";
                confirmPasswordError.style.display = "block";
                passwordConfirmationInput.classList.add("error");
                isValid = false;
            } else if (passwordInput.value !== passwordConfirmationInput.value) {
                confirmPasswordError.innerText = "Passwords do not match.";
                confirmPasswordError.style.display = "block";
                passwordConfirmationInput.classList.add("error");
                isValid = false;
            }

            if (!isValid) {
                event.preventDefault();
            }
        });
    </script>
<footer>
     <?php include '../footer.php'; ?>  
</footer>
</body>
</html>
