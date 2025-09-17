<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Include the database connection
include(__DIR__ . '/../connect_db.php'); // $conn is available
include __DIR__ . '/../livechat/chat_UI.php';
$message = "";
$messageColor = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Use prepared statement to securely fetch user
    $stmt = $conn->prepare("SELECT user_id, email, password_hash FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user && password_verify($password, $user['password_hash'])) {
        // Successful login
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['email'] = $user['email'];

        // Redirect to intended page or homepage
        $redirect = $_GET['redirect'] ?? '../index.php';
        header("Location: " . $redirect);
        exit();
    } else {
        $message = "Invalid login credentials! Please try again.";
        $messageColor = "red";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login Page</title>
<header>
    <?php include '../header.php'; ?>
</header>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="../css/styles.css">
<link rel="stylesheet" href="../css/login_styles.css">
<link rel="stylesheet" href="../css/header_styles.css">
<link rel="stylesheet" href="../css/footer_styles.css">
</head>
</br>
<body>
<div class="login-container">
    <div class="login-box">
        <h2>Log in</h2>
        <?php if ($message): ?>
            <p id="login-error-message" style="color: <?= htmlspecialchars($messageColor) ?>;">
                <?= htmlspecialchars($message) ?>
            </p>
        <?php endif; ?>
        <form id= "login-form" method="POST">
            <div class="input-container">
                <label for="email">Email</label>
                <input type="text" id="email" name="email" placeholder="Enter your email">
                <small id="email-error" class="error-message"></small>
            </div>
            <div class="input-container">
                <label for="password">Password</label>
                <div style="position: relative;">
                    <input type="password" id="password" name="password" placeholder="Enter your password">
                    <i id="password-icon" class="bi bi-eye-fill" onclick="togglePassword('password','password-icon')" style="cursor:pointer; position:absolute; right:-5px; bottom:10px; transform:translateY(-50%);"></i>
                </div>
                <small id="password-error" class="error-message"></small>
                
                <div class="forgot-password-link">
                    <a href="./forgot_password.php">Forgot your password? <i class="bi bi-box-arrow-up-right"></i></a>
                </div>
            </div>
            <div class="action-buttons">
                <button type="submit" class="login-btn">Log in</button>
                <button type="button" class="guest-btn" onclick="window.location.href='../index.php';">Continue as Guest</button>
            </div>
        </form>
        <div class="register-link">
            <p>New customer? <a href="./register.php">Register Now <i class="bi bi-box-arrow-up-right"></i></a></p>
        </div>
    </div>
</div>
<script>
const emailInput = document.getElementById("email");
const passwordInput = document.getElementById("password");
const emailError = document.getElementById("email-error");
const passwordError = document.getElementById("password-error");

const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

// Real-time email validation
emailInput.addEventListener("input", () => {
    if (emailInput.value.trim() !== "" && emailPattern.test(emailInput.value)) {
        emailError.style.display = "none";
        emailInput.classList.remove("error");
    }
});

passwordInput.addEventListener("input", () => {
    if (passwordInput.value.trim() !== "") {
        passwordError.style.display = "none";
        passwordInput.classList.remove("error");
    }
});

document.getElementById("login-form").addEventListener("submit", function(event) {
    let isValid = true;

    // Reset error messages
    emailError.style.display = "none";
    passwordError.style.display = "none";
    emailInput.classList.remove("error");
    passwordInput.classList.remove("error");

    // Validate email
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
    }

    // Validate password
    if (passwordInput.value.trim() === "") {
        passwordError.innerText = "Please enter your password";
        passwordError.style.display = "block";
        passwordInput.classList.add("error");
        isValid = false;
    }

    if (!isValid) {
        event.preventDefault(); // Prevent form submission
        if (emailInput.classList.contains("error")) {
            emailInput.focus();
        } else if (passwordInput.classList.contains("error")) {
            passwordInput.focus();
        }
    }
});

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
</script>
<?php include '../footer.php'; ?>
</body>
</html>
