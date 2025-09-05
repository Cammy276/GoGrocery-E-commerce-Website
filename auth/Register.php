<?php
include(__DIR__ . '/../ConnectDB.php');

$message = "";
$messageColor = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];

    if ($password !== $confirm_password) {
        $message = "Passwords do not match!";
        $messageColor = "red";
    } else {
        $checkEmail = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $checkEmail->bind_param("s", $email);
        $checkEmail->execute();
        $checkEmail->store_result();

        $checkPhone = $conn->prepare("SELECT user_id FROM users WHERE phone = ?");
        $checkPhone->bind_param("s", $phone);
        $checkPhone->execute();
        $checkPhone->store_result();

        if ($checkEmail->num_rows > 0) {
            $message = "Email already exists! <a href='./Login.php'>Click here to login</a>";
            $messageColor = "red";
        } elseif ($checkPhone->num_rows > 0) {
            $message = "Phone number already exists! <a href='./Login.php'>Click here to login</a>";
            $messageColor = "red";
        } else {

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users(name, email, phone, password_hash) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $phone, $hashed_password);

            if ($stmt->execute()) {
                $message = "Registration Successful! <a href='./Login.php'>Click here to login</a>";
                $messageColor = "green";
            } else {
                $message = "Error: " . $stmt->error;
                $messageColor = "red";
            }
            $stmt->close();
        }

        $checkEmail->close();
        $checkPhone->close();
    }

    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="../css/RegisterStyles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

    <div class="register-container">
        <div class="register-box">
            <h2>Sign Up</h2>
            <p id="register-success-message" style="color: <?= htmlspecialchars($messageColor) ?>;">
            <?= $message ?>
            </p>
            <form id="register-form" method="POST">
                <div class="input-container">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" placeholder="Enter your name">
                    <small id="name-error" class="error-message"></small>
                </div>
                <div class="input-container">
                    <label for="email">Email</label>
                    <input type="text" id="email" name="email" placeholder="Enter your email">
                    <small id="email-error" class="error-message"></small>
                </div>
                <div class="input-container">
                    <label for="phone">Phone Number (Malaysia)</label>
                    <input type="text" id="phone" name="phone" placeholder="Enter your phone number">
                    <small id="phone-error" class="error-message"></small>
                </div>
                <div class="input-container">
                    <label for="password">Password</label>
                    <div style="position: relative;">
                        <input type="password" id="password" name="password" placeholder="Enter your password">
                        <i id="password-icon" class="bi bi-eye-fill" onclick="togglePassword('password','password-icon')" style="cursor:pointer; position:absolute; right:1px; top:45%; transform:translateY(-50%);"></i>
                    </div>
                    <small id="password-error" class="error-message"></small>
                </div>

                <div class="input-container">
                    <label for="confirm-password">Confirm Password</label>
                    <div style="position: relative;">
                        <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirm your password">
                        <i id="confirm-password-icon" class="bi bi-eye-fill" onclick="togglePassword('confirm-password','confirm-password-icon')" style="cursor:pointer; position:absolute; right:1px; top:45%; transform:translateY(-50%);"></i>
                    </div>
                    <small id="confirm-password-error" class="error-message"></small>
                </div>

                <div class="action-buttons">
                    <button type="submit" class="register-btn">Register</button>
                </div>
            </form>
            <div class="login-link">
                <p>Already have an account? <a href="./login.php">Log in <i class="bi bi-box-arrow-up-right"></i></a></p>
            </div>
        </div>
    </div>

    <script>
        const nameInput = document.getElementById("name");
        const emailInput = document.getElementById("email");
        const phoneInput = document.getElementById("phone");
        const passwordInput = document.getElementById("password");
        const confirmPasswordInput = document.getElementById("confirm-password");

        const nameError = document.getElementById("name-error");
        const emailError = document.getElementById("email-error");
        const phoneError = document.getElementById("phone-error");
        const passwordError = document.getElementById("password-error");
        const confirmPasswordError = document.getElementById("confirm-password-error");

        const namePattern = /^[A-Za-z\s]+$/;
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const phonePattern = /^\+?60\d{9,10}$/;
        const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]).{8,}$/;

        nameInput.addEventListener("input", () => {
            if (nameInput.value.trim() !== "" && namePattern.test(nameInput.value)) {
                nameError.style.display = "none";
                nameInput.classList.remove("error");
            }
        });

        emailInput.addEventListener("input", () => {
            if (emailInput.value.trim() !== "" && emailPattern.test(emailInput.value)) {
                emailError.style.display = "none";
                emailInput.classList.remove("error");
            }
        });

        phoneInput.addEventListener("input", () => {
            if (phoneInput.value.trim() !== "" && phonePattern.test(phoneInput.value)) {
                phoneError.style.display = "none";
                phoneInput.classList.remove("error");
            }
        });

        passwordInput.addEventListener("input", () => {
            if (passwordPattern.test(passwordInput.value)) {
                passwordError.style.display = "none";
                passwordInput.classList.remove("error");
            }
            if (confirmPasswordInput.value !== "" && confirmPasswordInput.value === passwordInput.value) {
                confirmPasswordError.style.display = "none";
                confirmPasswordInput.classList.remove("error");
            }
        });

        confirmPasswordInput.addEventListener("input", () => {
            if (confirmPasswordInput.value === passwordInput.value && passwordInput.value !== "") {
                confirmPasswordError.style.display = "none";
                confirmPasswordInput.classList.remove("error");
            } else if (confirmPasswordInput.value !== "") {
                confirmPasswordError.innerText = "Passwords do not match";
                confirmPasswordError.style.display = "block";
                confirmPasswordInput.classList.add("error");
            }
        });

        document.getElementById("register-form").addEventListener("submit", function(event) {
            let isValid = true;

            if (nameInput.value.trim() === "") {
                nameError.innerText = "Name is required";
                nameError.style.display = "block";
                nameInput.classList.add("error");
                isValid = false;
            } else if (!namePattern.test(nameInput.value)) {
                nameError.innerText = "Name should contain only letters and spaces.";
                nameError.style.display = "block";
                nameInput.classList.add("error");
                isValid = false;
            }

            if (emailInput.value.trim() === "") {
                emailError.innerText = "Email is required";
                emailError.style.display = "block";
                emailInput.classList.add("error");
                isValid = false;
            } else if (!emailPattern.test(emailInput.value)) {
                emailError.innerText = "Please enter a valid email address";
                emailError.style.display = "block";
                emailInput.classList.add("error");
                isValid = false;
            }

            if (phoneInput.value.trim() === "") {
                phoneError.innerText = "Phone number is required";
                phoneError.style.display = "block";
                phoneInput.classList.add("error");
                isValid = false;
            } else if (!phonePattern.test(phoneInput.value)) {
                phoneError.innerText = "Please enter a valid Malaysian phone number (e.g., +60123456789)";
                phoneError.style.display = "block";
                phoneInput.classList.add("error");
                isValid = false;
            }

            if (passwordInput.value.trim() === "") {
                passwordError.innerText = "Password is required";
                passwordError.style.display = "block";
                passwordInput.classList.add("error");
                isValid = false;
            } else if (!passwordPattern.test(passwordInput.value)) {
                passwordError.innerText = "Password must be at least 8 characters long and include uppercase, lowercase, number, and special character.";
                passwordError.style.display = "block";
                passwordInput.classList.add("error");
                isValid = false;
            }

            if (confirmPasswordInput.value.trim() === "") {
                confirmPasswordError.innerText = "Please confirm your password";
                confirmPasswordError.style.display = "block";
                confirmPasswordInput.classList.add("error");
                isValid = false;
            } else if (confirmPasswordInput.value !== passwordInput.value) {
                confirmPasswordError.innerText = "Passwords do not match";
                confirmPasswordError.style.display = "block";
                confirmPasswordInput.classList.add("error");
                isValid = false;
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

</body>
</html>
