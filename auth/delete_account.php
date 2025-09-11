<?php
session_start();

// Prevent browser from caching this page
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$accountDeleted = false;

// Include your database connection
include(__DIR__ . '/../connect_db.php');

// Handle confirm
if (isset($_POST['action']) && $_POST['action'] === 'confirm') {
    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];

        // Delete the account securely
        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $userId);

        if ($stmt->execute()) {
            // Destroy session after successful deletion
            $_SESSION = array();
            session_destroy();
            $accountDeleted = true;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $accountDeleted ? "Account Deleted" : "Confirm Delete Account"; ?></title>
    <link rel="stylesheet" href="../css/delete_account_styles.css">
</head>
<body>
  <div class="overlay">
    <div class="container">
      <?php if (!$accountDeleted): ?>
        <!-- Step 1: Confirm Delete -->
        <h1>Are you sure you want to delete your account?</h1>
        <p>This action is permanent. You will lose all your data and cannot recover it.</p>
        <form method="post">
          <div class="button-group">
            <button type="button" onclick="history.back()" class="btn-cancel">Cancel</button>
            <button type="submit" name="action" value="confirm" class="btn-confirm">Delete Account</button>
          </div>
        </form>
      <?php else: ?>
        <!-- Step 2: Account Deleted -->
        <h1>Your account has been deleted</h1>
        <p>We’re sorry to see you go. You can create a new account anytime.</p>
        <div class="button-group">
          <button class="btn-home" onclick="window.location.href='/GoGrocery-E-commerce-Website/index.php'">Home</button>
          <button class="btn-signup" onclick="window.location.href='/GoGrocery-E-commerce-Website/auth/register.php'">Sign Up</button>
        </div>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
