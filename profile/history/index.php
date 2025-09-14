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
// Include the database connection
include(__DIR__ . '/../../connect_db.php');

// Predefine variable for error message
$errorMsg = null;

// Fetch all orders based on user id and status="paid"
$order_status = "delivered";
$orderStmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? AND status = ? ORDER BY placed_at DESC");
$orderStmt->bind_param("is", $user_id, $order_status);
if ($orderStmt->execute()) {
    $orderResult = $orderStmt->get_result();
    $orderList = [];

    while ($order = $orderResult->fetch_assoc()) {
        $orderList[] = $order; //store order in orderList
    }
} else {
    $errorMsg = $orderStmt->error;
}


$orderStmt->close();

?>

<?php
    include __DIR__ . '../../livechat/chat_UI.php';
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>History</title>
        
        <!-- Inter font -->
        <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
        <!-- Bootstrap -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
        <!-- Custom CSS -->
        <link rel="stylesheet" href="../../css/profile_styles.css">
        <link rel="stylesheet" href="../../css/orders_history_styles.css">
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
                    <li><a href="../history/index.php" class="active"><i class="bi bi-clock-history"></i> History</a></li>
                    <li><a href="../wishlist/index.php"><i class="bi bi-heart"></i> Wishlist</a></li>
                    <li><a href="../reward/index.php"><i class="bi bi-award-fill"></i> Rewards</a></li>
                    <li><a href="../../auth/logout.php"><i class="bi bi-box-arrow-right"></i> Log Out</a></li>
                    <li><a href="../../auth/delete_account.php"><i class="bi bi-trash"></i> Delete Account</a></li>
                </ul>
            </div>
            
            <!--- right content space -->
            <div id="profileContent">
                <div class="content-header">
                    <h1 style="text-align: left;">History</h1>
                    <p>Review the order records that are already completed.</p>
                </div>
                <div class="content">
                   <h2>My History</h2>

                   <!--- get message from orderDetails.php -->
                    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'updateSuccess'): ?>
                        <p class="successMessage">Order with Order ID <?php echo htmlspecialchars($_GET['order_id']) ?> is complete!</p>
                    <?php endif; ?>

                    <!--- shows message if error or no record -->
                    <?php if (!empty($errorMsg)): ?>
                        <p class="errMessage">Error occurred when fetching records. Please try again.</p>
                        <pre class="errMessage"><?php echo htmlspecialchars($errorMsg); ?></pre>
                    <?php elseif (count($orderList) === 0): ?>
                        <p class="tips">No order record found</p>
                    <?php else: ?>
                        <br/>
                    <?php endif; ?>

                    <?php foreach ($orderList as $order): ?>
                        <a href="historyDetails.php?id=<?php echo $order['order_id']; ?>" 
                            class="cardLink">
                            <div class="card">
                                <div class="infoTwoColumn">
                                    <div class="infoLeft">
                                        <h3 class="orderId">Order ID: <?php echo htmlspecialchars($order['order_id']); ?></h3>
                                        <span class="orderDate"><p><?php echo htmlspecialchars(date('M j, Y g:i A', strtotime($order['placed_at']))); ?></p></span>
                                    </div>
                                    <div class="infoRight">
                                        <span class="orderStatus status-<?php echo htmlspecialchars(strtolower($order['status'])); ?>">
                                            <p><?php echo ucfirst($order['status']); ?></p>
                                        </span>
                                        <span class="grandTotal"><p>RM <?php echo htmlspecialchars(number_format($order['grand_total'], 2)); ?></p></span>
                                    </div>
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