<?php
session_start();  // Start session to track the user

// Include the database connection
include('ConnectDB.php');

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch user data from the database
    $sql = "SELECT * FROM user WHERE email='$email'";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);

    // Verify password
    if ($user && password_verify($password, $user['password_hash'])) {
        // Successful login, create session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        header("Location: Homepage.php");  // Redirect to homepage
        exit();
    } else {
        $url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'];
        echo "<script>alert('Invalid login credentials!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="./style/LoginStyles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

    <div class="login-container">
        <div class="login-box">
            <h2>Log in</h2>
            <form method="POST">
                <div class="input-container">
                    <label for="email">Email</label>
                    <input type="text" id="email" name="email" placeholder="Enter your email">
                    <small id="email-error" class="error-message"></small>
                </div>
                <div class="input-container">
                    <label for="password">Password</label>
                    <div class="password-container">
                        <input type="password" id="password" name="password" placeholder="Enter your password">
                        <i id="password-icon" class="bi bi-eye-fill" onclick="togglePassword('password','password-icon')" style="cursor:pointer; position:absolute; right:1px; top:45%; transform:translateY(-50%);"></i>
                        <small id="password-error" class="error-message"></small>
                    </div>
                    <div class="forgot-password-link">
                         <a href="ForgotPassword.php">Forgot your password? <i class="bi bi-box-arrow-up-right"></i></a>
                    </div>
                </div>
                <div class="action-buttons">
                    <button type="submit" class="login-btn">Log in</button>
                    <button type="button" class="guest-btn" onclick="window.location.href='Homepage.php';">Continue as Guest</button>
                </div>
            </form>
            <div class="register-link">
                <p>New customer? <a href="Register.php">Register Now <i class="bi bi-box-arrow-up-right"></i></a></p>
            </div>
        </div>
    </div>

    <script>
        // Client-side validation
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

        document.querySelector("form").addEventListener("submit", function(event) {
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

</body>
</html>
