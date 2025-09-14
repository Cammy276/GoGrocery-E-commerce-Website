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



<!-- to get order_id passed in -->
 <?php
if (isset($_GET['id'])) {
    $voucher_id = intval($_GET['id']); // sanitize input
} else {
    die("Fail to get voucher_id from reward page.");
}

?>

<?php

// Include the database connection
include(__DIR__ . '/../../connect_db.php');

// Predefine variable for error message
$errorMsg = null;


$sql = "
SELECT v.voucher_id, v.voucher_name, v.description, v.terms_conditions,
       v.voucher_image_url, v.discount_type, v.discount_value,
       v.min_subtotal, v.start_date, v.end_date,
       uv.isUsed
FROM user_vouchers uv
JOIN vouchers v ON uv.voucher_id = v.voucher_id
WHERE uv.user_id = ?
ORDER BY v.start_date ASC
";

$voucherStmt = $conn->prepare("SELECT * FROM vouchers WHERE voucher_id = ? ");
$voucherStmt->bind_param("i", $voucher_id);

if ($voucherStmt->execute()) {
    $voucherResult = $voucherStmt->get_result();
    $voucherInfo = $voucherResult->fetch_assoc();

} else {
    $errorMsg = $voucherStmt->error;
}

$voucherStmt->close();




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
                    <h1 style="text-align: left;">Rewards</h1>
                    <p>Explore your rewards and redeem vouchers to enjoy exclusive benefits.</p>
                </div>
                <div class="content">
                   <h2>My Rewards</h2>

                    <!--- shows message if error or no record -->
                    <?php if (!empty($errorMsg)): ?>
                        <p class="errMessage">Error occurred when fetching record. Please try again.</p>
                        <pre class="errMessage"><?php echo htmlspecialchars($errorMsg); ?></pre>
                    <?php elseif (empty($voucherInfo)): ?>
                        <p class="tips">No voucher record found</p>
                    <?php else: ?>
                        <br/>
                    <?php endif; ?>

     

    <div class="card">
        <img class="voucher-image details" src="../<?php echo htmlspecialchars($voucherInfo['voucher_image_url']); ?>" alt="Voucher Image">

        <h3 class="voucher-title details"><?php echo htmlspecialchars($voucherInfo['voucher_name']); ?></h3>
        <p class="voucher-description details"><?php echo htmlspecialchars($voucherInfo['description']); ?></p>

        <div class="badges details">
            <p class="badge details <?php echo $voucherInfo['discount_type']; ?>">
                <?php echo $voucherInfo['discount_type'] == 'PERCENT' ? 'PERCENTAGE' : 'FIXED'; ?>
            </p>
        </div>

        <div class="voucher-details" details>
            <div class="voucher-detail-item details">
                <p class="voucher-detail-label details">Discount</p>
                <p class="voucher-detail-value discount-value details">
                    <?php echo $voucherInfo['discount_type'] == 'PERCENT'
                        ? htmlspecialchars($voucherInfo['discount_value']) . '% OFF'
                        : 'RM' . htmlspecialchars($voucherInfo['discount_value']) . ' OFF'; ?>
                </p>
            </div>
            
            <div class="voucher-detail-item details">
                <p class="voucher-detail-label details">Minimum Spend</p>
                <p class="voucher-detail-value min-spend details">RM<?php echo htmlspecialchars($voucherInfo['min_subtotal']); ?></p>
            </div>
            
            <div class="voucher-detail-item details">
                <p class="voucher-detail-label details">Valid Until</p>
                <p class="voucher-detail-value duration details"><?php echo htmlspecialchars(date('M j, Y', strtotime($voucherInfo['end_date']))); ?></p>
            </div>
        </div>

       
            <p class="terms-label details">Terms & Conditions:</p>
            <p class="terms-content details"><?php echo htmlspecialchars($voucherInfo['terms_conditions']); ?></p>
        
    </div>



                </div>
            </div>
        </div>


        <footer><?php include("../../footer.php") ?> </footer>

    </body>
</html>