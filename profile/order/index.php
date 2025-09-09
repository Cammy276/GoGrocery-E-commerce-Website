<!-- to get current user id -->
<?php
session_start();
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    echo "Logged in as User ID: " . $user_id;
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
$order_status = "paid";
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



<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Order</title>
        
            <!-- Bootstrap Icons -->
            <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
             <!-- Custom CSS -->
            <link rel="stylesheet" href="../../css/profile.css">
    </head>
    <body>
            <div id="header">
            <div class="logo">GoGrocery</div>
            <div class="nav-links">
                <a href="">Home</a>
                <a href="">About</a>
                <a href="">Help Center</a>
                <a href="">Best Seller</a>
                <a href="">Special Deal</a>
                <a href="">New Product</a>
            </div>
        </div>

        <div class="main-container">

            <!-- left side bar setting -->
            <div id="profileSettingSideBar">  
                <ul class="menu-items">
                    <!-- use Bootstrap icons-->
                    <li><a href="../settings/index.php"><i class="bi bi-gear-fill"></i> Profile Settings</a></li>
                    <li><a href="../deliveryAddress/index.php"><i class="bi bi-geo-alt-fill"></i> Delivery Addresses</a></li>
                    <li><a href="../cart/index.php"><i class="bi bi-cart3"></i> Cart</a></li>
                    <li><a href="../order/index.php" class="active"><i class="bi bi-bag-fill"></i> Orders</a></li>
                    <li><a href="../history/index.php"><i class="bi bi-clock-history"></i> History</a></li>
                    <li><a href=""><i class="bi bi-heart"></i> Wishlist</a></li>
                    <li><a href=""><i class="bi bi-award-fill"></i> Rewards</a></li>
                     <li><a href="../../auth/logout.php"><i class="bi bi-box-arrow-right"></i> Log Out</a></li>
                </ul>
            </div>
            
            <!--- right content space -->
            <div id="profileContent">
                <div class="content-header">
                    <h1>Orders</h1>
                    <p>Review the orders that are still pending delivery</p>
                </div>
                <div class="content">
                   <h2>Order List</h2>

                    <!--- shows message if error or no record -->
                    <?php if (!empty($errorMsg)): ?>
                        <p class="errMessage">Error occurred when fetching order records. Please try again.</p>
                        <pre class="errMessage"><?php echo htmlspecialchars($errorMsg); ?></pre>
                    <?php elseif (count($orderList) === 0): ?>
                        <p class="tips">No order record found</p>
                    <?php else: ?>
                        <br/>
                    <?php endif; ?>

                    <?php foreach ($orderList as $order): ?>
                        <a href="orderDetails.php?id=<?php echo $order['order_id']; ?>" 
                            class="cardLink">
                            <div class="card">
                                <div class="infoTwoColumn">
                                    <div class="infoLeft">
                                        <h3 class="orderId">Order ID: <?php echo $order['order_id']; ?></h3>
                                        <span class="orderDate"><?php echo date('M j, Y g:i A', strtotime($order['placed_at'])); ?></span>
                                    </div>
                                    <div class="infoRight">
                                        <span class="orderStatus status-<?php echo strtolower($order['status']); ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                        <span class="grandTotal">RM <?php echo number_format($order['grand_total'], 2); ?></span>
                                    </div>
                                </div>
                            </div>
                        </a>

                    <?php endforeach; ?>


                </div>
            </div>
        </div>

    </body>
</html>