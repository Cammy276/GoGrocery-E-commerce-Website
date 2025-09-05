<?php
$message = $_GET['message'] ?? 'Password reset link sent successfully.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Success</title>
    <link rel="stylesheet" href="../css/SuccessPasswordResetStyles.css">
</head>
<body>
    <div class="success-container">
        <div class="success-box">
            <h2><?php echo htmlspecialchars($message); ?></h2>
            <a href="./login.php" class="btn btn-login">Back to Login</a>
            <a href="https://mail.google.com/" target="_blank" class="btn btn-gmail">Go to E-mail</a>
            <p class="note">Check your email inbox (or spam folder) for the reset link</p>
        </div>
    </div>
</body>
</html>
