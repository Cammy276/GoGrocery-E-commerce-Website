<?php
// Suppress errors and warnings (in production, it's recommended to handle errors gracefully)
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Get the email from POST request
$email = $_POST['email'];

// Generate a random token
$token = bin2hex(random_bytes(16));

// Hash the token using SHA-256
$token_hash = hash("sha256", $token);

// Set the expiry time for the token (30 minutes)
$expiry = date("Y-m-d H:i:s", time() + 60 * 30);

// Include the database connection
$mysqli = require __DIR__ . "/../connect_db.php"; // Ensure this returns the connection

// Step 1: Retrieve user by email
$sql = "SELECT name FROM users WHERE email = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    // If no user found, redirect gracefully
    header("Location: ./error.php?message=No user found with the provided email");
    exit;
}

$name = $user['name'];

// Step 2: Update reset token and expiry
$sql = "UPDATE users SET reset_token_hash = ?, reset_token_expires_at = ? WHERE email = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("sss", $token_hash, $expiry, $email);
$stmt->execute();

if ($mysqli->affected_rows) {
    // Require the mailer configuration
    require __DIR__ . "/../vendor/mailer.php";

    // Set up the email
    $mail->setFrom("noreply@example.com", "GoGrocery Support");
    $mail->addAddress($email, $name);
    $mail->Subject = "Password Reset Request";
    $mail->isHTML(true);
    $mail->Body = <<<END
    <html>
    <body>
        <p>Dear {$name},</p>
        <p>We received a request to reset your password. To proceed, please click the link below:</p>
        <p><a href="http://localhost/GoGrocery-E-commerce-Website/auth/reset_password.php?token={$token}">Reset your password</a></p>
        <p>If you did not request a password reset, please disregard this email.</p>
        <p>Thank you for using GoGrocery-E-commerce-Website.</p>
        <p>Regards,<br>Customer Service Team</p>
    </body>
    </html>
    END;

    // Send the email
    try {
        $mail->send();
        header("Location: ./success_password_reset.php?message=Password reset link sent to your email");
        exit;
    } catch (Exception $e) {
        error_log("Error sending email: " . $mail->ErrorInfo);
        header("Location: ./error.php?message=Failed to send password reset email");
        exit;
    }
} else {
    header("Location: ./error.php?message=No user found with the provided email");
    exit;
}
?>
