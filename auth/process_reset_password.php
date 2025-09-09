<?php
// ProcessResetPassword.php

// Function to show styled error messages
function showError($message) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Error</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../css/process_reset_password_styles.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    </head>
    <body>
        <div class="reset-container">
            <h2>Error</h2>
           <p class="error-message" style="color: red; font-size: 15px; margin-top: 5px; text-align: center;">
                <?= htmlspecialchars($message) ?>
            </p>
            <div class="login-link">
            <a class="button-link" href="./login.php">Go to Login <i class="bi bi-box-arrow-in-right"></i></a>
        </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Check token and password fields
if (!isset($_POST['token']) || empty($_POST['token'])) showError("Token not found.");
$token = $_POST["token"];
$token_hash = hash("sha256", $token);

$mysqli = require __DIR__ . '/../connect_db.php';

$sql = "SELECT * FROM users WHERE reset_token_hash = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $token_hash);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user === null) showError("Token not found.");
if (strtotime($user["reset_token_expires_at"]) <= time()) showError("Token has expired.");
if (!isset($_POST["password"]) || !isset($_POST["confirm-password"])) showError("Password fields are required.");

// Hash the new password
$password_hash = password_hash($_POST["password"], PASSWORD_DEFAULT);

// Update the database
$updateSql = "UPDATE users
              SET password_hash = ?, reset_token_hash = NULL, reset_token_expires_at = NULL
              WHERE user_id = ?";
$updateStmt = $mysqli->prepare($updateSql);
$updateStmt->bind_param("si", $password_hash, $user["user_id"]);
$updateStmt->execute();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Password Reset Success</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/process_reset_password_styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="reset-container">
        <h2>Password Reset Successful</h2>
        <p class="success-message">Your password has been updated successfully.</p>
        <div class="login-link">
            <a class="button-link" href="./login.php">Go to Login <i class="bi bi-box-arrow-in-right"></i></a>
        </div>
    </div>
</body>
</html> 
