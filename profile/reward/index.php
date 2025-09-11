<!-- to get current user id -->
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
} else {
    echo "You are not logged in!";
}
?>


<?php

echo "Logged in as User ID: " . $user_id;
// Include the database connection
include(__DIR__ . '/../../connect_db.php');

// Predefine variable for error message
$errorMsg = null;

// Fetch all non-used voucher based on user_id
$rewardStmt = $conn->prepare("
    SELECT v.voucher_id, v.voucher_name, v.description, v.terms_conditions,
           v.voucher_image_url, v.discount_type, v.discount_value,
           v.min_subtotal, v.start_date, v.end_date
    FROM user_vouchers uv
    JOIN vouchers v ON uv.voucher_id = v.voucher_id
    WHERE uv.user_id = ?
      AND uv.isUsed = FALSE
      AND NOW() BETWEEN v.start_date AND v.end_date
");

$rewardStmt->bind_param("i", $user_id);

if ($rewardStmt->execute()) {
    $rewardResult = $rewardStmt->get_result();
    $rewardList = [];

    while ($reward = $rewardResult->fetch_assoc()) {
        $rewardList[] = $reward;
    }
} else {
    $errorMsg = $rewardStmt->error;
}


$rewardStmt->close();




//handle insert
if (isset($_POST['insert'])) {
    // Assume you already have $user_id and $voucher_name from form/input
    $voucher_name = trim($_POST['voucher_name']);

    // 1. Check if voucher exists
    $voucherStmt = $conn->prepare("SELECT voucher_id FROM vouchers WHERE voucher_name = ?");
    $voucherStmt->bind_param("s", $voucher_name);
    $voucherStmt->execute();
    $voucherResult = $voucherStmt->get_result();

    if ($voucherResult->num_rows === 0) {
        // Voucher not found
        $errorMsg = "Voucher Code does not exist.";
    } else {
        $voucher = $voucherResult->fetch_assoc();
        $voucher_id = $voucher['voucher_id'];

        // 2. Check if user already has this voucher
        $UserVoucherStmt = $conn->prepare("SELECT * FROM user_vouchers WHERE user_id = ? AND voucher_id = ?");
        $UserVoucherStmt->bind_param("ii", $user_id, $voucher_id);
        $UserVoucherStmt->execute();
        $UserVoucherResult = $UserVoucherStmt->get_result();

        if ($UserVoucherResult->num_rows > 0) {
            // Already claimed
            $errorMsg = "You have already claimed this voucher.";
        } else {
            // 3. Insert new record
            $addUVStmt = $conn->prepare("INSERT INTO user_vouchers (user_id, voucher_id) VALUES (?, ?)");
            $addUVStmt->bind_param("ii", $user_id, $voucher_id);

            if ($addUVStmt->execute()) {
                header("Location: index.php?msg=addSuccess");
                exit();
            } else {
                $errorMsg = "Error adding voucher: " . $addUVStmt->error;
            }
        }
    }

}

?>



<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Rewards</title>
        
        <!-- Inter font -->
        <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
        <!-- Bootstrap -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
        <!-- Custom CSS -->
        <link rel="stylesheet" href="../../css/profile_styles.css">
        <link rel="stylesheet" href="../../css/cart_styles.css">
        <link rel="stylesheet" href="../../css/reward_styles.css">
        <link rel="stylesheet" href="../../css/header_styles.css">
        <link rel="stylesheet" href="../../css/footer_styles.css">
css\reward_style.css
    </head>
    <body>
        <header><?php include("../../header.php") ?></header>

        <div class="main-container">

            <!-- left side bar setting -->
            <div id="profileSettingSideBar">  
                <ul class="menu-items">
                    <!-- use Bootstrap icons-->
                    <li><a href="../settings/index.php"><i class="bi bi-gear-fill"></i> Profile Settings</a></li>
                    <li><a href="../deliveryAddress/index.php"><i class="bi bi-geo-alt-fill"></i> Delivery Addresses</a></li>
                    <li><a href="../cart/index.php"><i class="bi bi-cart3"></i> Cart</a></li>
                    <li><a href="../order/index.php"><i class="bi bi-bag-fill"></i> Orders</a></li>
                    <li><a href="../history/index.php"><i class="bi bi-clock-history"></i> History</a></li>
                    <li><a href="../wishlist/index.php"><i class="bi bi-heart"></i> Wishlist</a></li>
                    <li><a href="../reward/index.php" class="active"><i class="bi bi-award-fill"></i> Rewards</a></li>
                    <li><a href="../../auth/logout.php"><i class="bi bi-box-arrow-right"></i> Log Out</a></li>
                </ul>
            </div>
            
            <!--- right content space -->
            <div id="profileContent">
                <div class="content-header">
                    <h1>Rewards</h1>
                    <p>Explore your rewards and redeem vouchers to enjoy exclusive benefits.</p>
                </div>
                <div class="content">
                    <div class="reward-header">
                        <h2>My Rewards</h2>
                        <a href="addVoucher.php"><i class="bi bi-plus-circle-fill"></i></a>
                    </div>
                    <!--- get message from reward/index.php -->
                    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'addSuccess'): ?>
                        <p class="successMessage">Voucher has been successfully claim.</p>
                    <?php endif; ?>

                    <!--- shows message if error or no record -->
                    <?php if (!empty($errorMsg)): ?>
                        <p class="errMessage">Error occurred when claiming voucher. Please try again.</p>
                        <pre class="errMessage"><?php echo htmlspecialchars($errorMsg); ?></pre>
                    <?php elseif (count($rewardList) === 0): ?>
                        <p class="tips">No voucher record found</p>
                    <?php else: ?>
                        <br/>
                    <?php endif; ?>

                    <?php foreach ($rewardList as $voucher): ?>
                        <a href="rewardDetails.php?id=<?php echo $voucher['voucher_id'] ?>" class="cardLink">
                            <div class="voucher-card">
                                <!-- Image on the left -->
                                <img class="voucher-image" 
                                    src="<?php echo htmlspecialchars($voucher['voucher_image_url']); ?>" 
                                    alt="Voucher Image">

                                <!-- Content on the right -->
                                <div class="voucher-content">
                                    <!-- Top row: Title + Badge -->
                                    <div class="voucher-header">
                                        <p class="voucher-title">
                                            <?php echo htmlspecialchars($voucher['voucher_name']); ?>
                                        </p>
                                        <p class="badge <?php echo $voucher['discount_type']; ?>">
                                            <?php echo $voucher['discount_type'] == 'PERCENT' ? 'Percentage' : 'Fixed'; ?>
                                        </p>
                                    </div>

                                    <!-- Valid Until -->
                                    <p class="voucher-valid">
                                        Valid until <?php echo date('M j, Y', strtotime($voucher['end_date'])); ?>
                                    </p>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>

                </div>
            </div>
        </div>


        <footer><?php include("../../footer.php") ?> </footer>

    </body>
</html>