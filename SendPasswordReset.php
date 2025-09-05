
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
$mysqli = require __DIR__ . "/ConnectDB.php"; // Ensure this returns the connection

// Prepare the SQL query to update the user's reset token and expiry time
$sql = "UPDATE userauthentication SET reset_token_hash = ?, reset_token_expires_at = ? WHERE email = ?";

// Prepare and bind the statement
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("sss", $token_hash, $expiry, $email);
$stmt->execute(); // Execute the query

if($mysqli -> affected_rows){
    // Require the mailer configuration
    require __DIR__ . "/vendor/mailer.php";

    // Set up the email
    $mail->setFrom("noreply@example.com");
    $mail->addAddress($email);
    $mail->Subject = "Password Reset Request";
    $mail->Body = <<<END
    <html>
    <body>
        <p>Dear $name,</p>
        <p>We received a request to reset your password. To proceed, please click the link below:</p>
        <p><a href="http://localhost/GoGrocery-E-commerce-Website/ResetPassword.php?token=$token">Reset your password</a></p>
        <p>If you did not request a password reset, please disregard this email.</p>
        <p>Thank you for using GoGrocery-E-commerce-Website.</p>
        <p>Regards,<br>Customer Service Team</p>
    </body>
    </html>
    END;

    // Send the email
    try {
        $mail->send();
        // Redirect to a success page or show a success message
        header("Location: SuccessPasswordReset.php?message=Password reset link sent to your email");
        exit;  // Make sure no further code is executed
    } catch (Exception $e) {
        // Log the error to a file instead of displaying it on the screen
        error_log("Error sending email: " . $mail->ErrorInfo);
        header("Location: error.php?message=Failed to send password reset email");
        exit;
    }
} else {
    // If no rows are affected, handle this case gracefully
    header("Location: error.php?message=No user found with the provided email");
    exit;
}
?>
<?php
// Get message from query parameter (fallback if missing)
$message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : "Password reset process completed.";
?>

