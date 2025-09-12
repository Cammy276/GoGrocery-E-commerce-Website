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
    $order_id = intval($_GET['id']); // sanitize input
} else {
    die("Fail to get order_id from order page.");
}

?>

<?php 

// Include the database connection
include(__DIR__ . '/../../connect_db.php');

// Predefine variable for error message
$errorMsg = null;


//to get the order details by order_id
$orderStmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ?");
$orderStmt->bind_param("i", $order_id);

if ($orderStmt->execute()){
    $orderResult = $orderStmt->get_result();
    $orderInfo = $orderResult->fetch_assoc();
} else {
    $errorMsg = $orderStmt->error;
}
$orderStmt->close();

$address_id = $orderInfo['address_id'];
$voucher_id = $orderInfo['voucher_id'];

//to get the delivery address
$addressStmt = $conn->prepare("SELECT * FROM addresses WHERE address_id = ?");
$addressStmt->bind_param("i", $address_id);

if ($addressStmt->execute()){
    $addressResult = $addressStmt->get_result();
    $address = $addressResult->fetch_assoc();

    $parts = [];
    if (!empty($address['apartment'])) {
        $parts[] = $address['apartment'];
    }
    $parts[] = $address['street'];
    $parts[] = $address['postcode'];
    $parts[] = $address['city'];
    $parts[] = $address['state_territory'];

    $address = implode(", ", $parts);
} else {
    $errorMsg = $addressStmt->error;
}
$addressStmt->close();





//to get the order items by order_id
$itemStmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
$itemStmt->bind_param("i", $order_id);


if ($itemStmt->execute()){
    $itemResult = $itemStmt->get_result();
    $orderItemList = [];

    if ($itemResult->num_rows === 0) {
        die("Order item not found.");
    }
    while($row = $itemResult->fetch_assoc()) { 
        $orderItemList[] = $row;
    }
} else {
    $errorMsg = $itemStmt->error;
}
$itemStmt->close();


//to get the voucher by voucher_id
$voucherStmt = $conn->prepare("SELECT * FROM vouchers WHERE voucher_id = ?");
$voucherStmt->bind_param("i", $voucher_id);
$voucher = null;
if ($voucherStmt->execute()){
    $voucherResult = $voucherStmt->get_result();
    $voucher = $voucherResult->fetch_assoc();
} else {
    $errorMsg = $voucherStmt->error;
}
$voucherStmt->close();


// Handle update status
if (isset($_POST['update'])) {

    $state = $_POST['state'];

    $updateStmt = $conn->prepare("UPDATE orders SET status=? WHERE order_id=?");
    $updateStmt->bind_param("si", $state, $order_id);
    
    if ($updateStmt->execute()) {
        //if operation success, redirect to history page
        header("Location: ../history/index.php?msg=updateSuccess&order_id=" . $order_id);
        exit();
    } else {
        //store error message in variable
        $errorMsg = $updateStmt->error;
    }
    $$updateStmt->close();
}

?>


<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Order Details</title>

        <!-- Inter font -->
        <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
        <!-- Bootstrap -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            <div id="profileSettingSideBar">  
                <ul class="menu-items">
                    <li><a href=""><i class="bi bi-gear-fill"></i> Profile Settings</a></li>
                    <li><a href="../deliveryAddress/index.php"><i class="bi bi-geo-alt-fill"></i> Delivery Addresses</a></li>
                    <li><a href="../cart/index.php"><i class="bi bi-cart3"></i> Cart</a></li>
                    <li><a href="../order/index.php" class="active"><i class="bi bi-bag-fill"></i> Orders</a></li>
                    <li><a href="../history/index.php"><i class="bi bi-clock-history"></i> History</a></li>
                    <li><a href="../wishlist/index.php"><i class="bi bi-heart"></i> Wishlist</a></li>
                    <li><a href="../reward/index.php"><i class="bi bi-award-fill"></i> Rewards</a></li>
                    <li><a href="../../auth/logout.php"><i class="bi bi-box-arrow-right"></i> Log Out</a></li>
                </ul>
            </div>

            <div id="profileContent">
                <div class="content-header">
                    <h1>Orders</h1>
                    <p>Review the orders that are still pending delivery.</p>
                </div>

                <div class="content">
                    <h2>My Order Details</h2>
                    
                    <!--- shows message if error or no record -->
                    <?php if (!empty($errorMsg)): ?>
                        <p class="errMessage">Error occurred when fetching records. Please try again.</p>
                        <pre class="errMessage"><?php echo htmlspecialchars($errorMsg); ?></pre>
                    <?php elseif (empty($orderInfo)): ?>
                        <p class="tips">No order record found</p>
                    <?php else: ?>
                        <br/>
                    <?php endif; ?>


                    <div class="card-order">
                        <div class="order-header">
                            <h3 class="order-orderId">Order ID: <?php echo $order_id; ?></h3>
                            <p class="order-status status-paid">Paid</p>
                        </div>

                        <div class="order-body">
                            <div class="order-details">
                                <div class="detail-item">
                                    <p class="detail-label">Placed On</p>
                                    <p class="detail-value">
                                        <?php echo isset($orderInfo['placed_at']) 
                                        ? date('M j, Y g:i A', strtotime($orderInfo['placed_at'])) 
                                        : '-'; ?>
                                    </p>
                                </div>
                                <div class="detail-item">
                                    <p class="detail-label">Number of Item</p>
                                    <p class="detail-value"><?php echo count($orderItemList); ?></p>
                                </div>
                            </div>

                            <!-- Order Items -->
                            <div class="order-items">
                                <h4 class="items-title"><i class="bi bi-list-check"></i> <p>Order Items</p></h4>
                                <ul class="item-list">
                                    <?php foreach ($orderItemList as $item): ?>
                                    <li>
                                        <div class="item-info">
                                            <p class="item-name"><?php echo htmlspecialchars($item['product_name']); ?></p>
                                            <p class="item-details">SKU: <?php echo $item['sku']; ?></p>
                                            <p class="item-details">
                                                Unit discount: 
                                                <?php echo ($item['line_discount'] > 0) ? "RM " . number_format($item['line_discount'], 2) : "-"; ?>
                                            </p>
                                        </div>
                                        <p class="item-quantity">Qty: <?php echo $item['quantity']; ?></p>
                                        <p class="item-price">RM <?php echo number_format($item['unit_price'], 2); ?>/unit</p>
                                        <p class="item-total">RM <?php echo number_format($item['line_total'], 2); ?></p>
                                    </li>
                                    <?php endforeach; ?>
                                    <p class="tips">Each item's total price already adjusted to include any applicable item discount</p>
                                </ul>
                                
                            </div>

                            <!-- Payment & Delivery -->
                            <div class="order-payment-section">
                                <h4 class="payment-title"><i class="bi bi-credit-card"></i> Payment & Delivery Information</h4>
                                <div class="payment-item">
                                    <p class="payment-label">Address</p>
                                    <p class="payment-value"><?php echo isset($address) ? $address : '-'; ?></p>
                                </div>
                                <div class="payment-item">
                                    <p class="payment-label">Payment Method</p>
                                    <p class="payment-value"><?php echo isset($orderInfo['payment_method']) ? ucfirst($orderInfo['payment_method']) : '-'; ?></p>
                                </div>
                                <div class="payment-item">
                                    <p class="payment-label">Voucher Applied</p>
                                    <p class="payment-value"><?php echo isset($voucher) && !empty($voucher) ? ucwords($voucher['description']) : '-'; ?></p>
                                </div>
                                <div class="payment-item">
                                    <p class="payment-label">Delivery Duration</p>
                                    <p class="payment-value"><?php echo isset($orderInfo['delivery_duration']) ? $orderInfo['delivery_duration'] : '-'; ?></p>
                                </div>
                            </div>

                            <!-- Price Breakdown -->
                            <div class="breakdown-section">
                                <h4 class="breakdown-title"><i class="bi bi-receipt"></i> Price Breakdown</h4>
                                <div class="breakdown-vertical">
                                    <div class="breakdown-item">
                                        <p class="breakdown-label">Subtotal</p>
                                        <p class="breakdown-value">RM <?php echo isset($orderInfo['subtotal']) ? number_format($orderInfo['subtotal'], 2) : '0.00'; ?></p>
                                    </div>
                                    <div class="breakdown-item">
                                        <p class="breakdown-label">Voucher Discount</p>
                                        <p class="breakdown-value discountValue">- RM <?php echo isset($orderInfo['voucher_discount_value']) ? number_format($orderInfo['voucher_discount_value'], 2) : '0.00'; ?></p>
                                    </div>
                                    <div class="breakdown-item">
                                        <p class="breakdown-label">Shipping Fee</p>
                                        <p class="breakdown-value">RM <?php echo isset($orderInfo['shipping_fee']) ? number_format($orderInfo['shipping_fee'], 2) : '0.00'; ?></p>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="grand-total">
                            Grand Total: RM <?php echo isset($orderInfo['grand_total']) ? number_format($orderInfo['grand_total'], 2) : '-'; ?>
                        </div>
                    </div>

                    <form method='post'>
                        <input class="textInput" type='hidden' id='state' name='state' value="delivered" />
                        <div class="buttonContainer">
                            <input class="saveButton" type='submit' name='update' value='Order received' />
                        </div>
                    </form>

                </div>
            </div>
        </div>

        <footer><?php include("../../footer.php") ?> </footer>
    </body>
</html>
