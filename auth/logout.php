<?php
session_start();

// Prevent browser from caching this page
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$loggedOut = false;

// Handle confirm
if (isset($_POST['action'])) {
    if ($_POST['action'] === 'confirm') {
        // Destroy session
        $_SESSION = array();
        session_destroy();
        $loggedOut = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $loggedOut ? "Logged Out" : "Confirm Logout"; ?></title>
    <header>
      <?php include '../header.php'; ?>
    </header>
    <link rel="stylesheet" href="../css/logout_styles.css">
</head>
<body>
  <div class="overlay">
    <div class="container">
      <?php if (!$loggedOut): ?>
        <!-- Step 1: Confirm Logout -->
        <h1>Are you sure you want to logout?</h1>
        <p>You can always log back in later.</p>
        <form method="post">
          <div class="button-group">
            <button type="button" onclick="history.back()" class="btn-cancel">Cancel</button>
            <button type="submit" name="action" value="confirm" class="btn-confirm">Confirm Logout</button>
          </div>
        </form>
      <?php else: ?>
        <!-- Step 2: Logged Out -->
        <h1>You are logged out</h1>
        <p>You can log in again or return to the homepage.</p>
        <div class="button-group">
          <button class="btn-home" onclick="window.location.href='/GoGrocery-E-commerce-Website/index.php'">Home</button>
          <button class="btn-login" onclick="window.location.href='/GoGrocery-E-commerce-Website/auth/login.php'">Log In</button>
        </div>
      <?php endif; ?>
    </div>
  </div>
</body>
<?php include '../footer.php'; ?>
</html>
