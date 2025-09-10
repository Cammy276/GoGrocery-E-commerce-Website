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

// Fetch all orders based on user id adn status="paid"
$order_status = "paid";
$orderStmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? AND status = ? ORDER BY placed_at DESC");
$orderStmt->bind_param("is", $user_id, $order_status);
$orderStmt->execute();
$orderResult = $orderStmt->get_result();
$orderList = [];

while ($order = $orderResult->fetch_assoc()) {
    $order_id = $order['order_id'];
   
    // Fetch order items based on order id
    $itemStmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
    $itemStmt->bind_param("i", $order_id);
    $itemStmt->execute();
    $itemResult = $itemStmt->get_result();
    $orderItemList = $itemResult->fetch_all(MYSQLI_ASSOC);
    $itemStmt->close();

    $order['itemList'] = $orderItemList; // attach items to order
    $orderList[] = $order; //store order in orderList
}

$orderStmt->close();

?>


<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Order Page</title>
        
            <!-- Bootstrap Icons -->
            <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
             <!-- Bootstrap CSS -->
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <!-- Custom CSS -->
            <link rel="stylesheet" href="../../css/profile.css">
            <style>
                .order-card {
                    border-radius: 12px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
                    margin-bottom: 24px;
                    overflow: hidden;
                    transition: transform 0.2s, box-shadow 0.2s;
                    border: 1px solid #eaeaea;
                }
                
                .order-card:hover {
                    transform: translateY(-4px);
                    box-shadow: 0 6px 16px rgba(0,0,0,0.12);
                }
                
                .order-header {
                    background-color: #f8f9fa;
                    padding: 16px 20px;
                    border-bottom: 1px solid #eaeaea;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }
                
                .order-id {
                    font-weight: 600;
                    color: #2c3e50;
                    font-size: 18px;
                    margin: 0;
                }
                
                .order-status {
                    padding: 6px 12px;
                    border-radius: 50px;
                    font-size: 14px;
                    font-weight: 500;
                }
                
                .status-paid {
                    background-color: #fff3cd;
                    color: #856404;
                }
                
                
                .status-delivered {
                    background-color: #d1ecf1;
                    color: #0c5460;
                }
                
                .status-cancelled {
                    background-color: #f8d7da;
                    color: #721c24;
                }
                
                .order-body {
                    padding: 20px;
                }
                
                .order-details {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                    gap: 16px;
                    margin-bottom: 20px;
                }
                
                .detail-item {
                    display: flex;
                    flex-direction: column;
                }
                
                .detail-label {
                    font-size: 12px;
                    color: #6c757d;
                    margin-bottom: 4px;
                }
                
                .detail-value {
                    font-size: 16px;
                    font-weight: 500;
                    color: #2c3e50;
                }
                
                .order-items {
                    margin-top: 16px;
                }
                
                .items-title {
                    font-size: 16px;
                    font-weight: 600;
                    margin-bottom: 12px;
                    color: #2c3e50;
                    display: flex;
                    align-items: center;
                }
                
                .items-title i {
                    margin-right: 8px;
                }
                
                .item-list {
                    list-style: none;
                    padding: 0;
                    margin: 0;
                }
                
                .item-list li {
                    display: flex;
                    justify-content: space-between;
                    padding: 12px 0;
                    border-bottom: 1px solid #f1f1f1;
                }
                
                .item-list li:last-child {
                    border-bottom: none;
                }
                
                .item-info {
                    flex: 2;
                }
                
                .item-name {
                    font-weight: 500;
                    margin-bottom: 4px;
                }
                
                .item-sku {
                    font-size: 12px;
                    color: #6c757d;
                }
                
                .item-quantity, .item-price, .item-total {
                    flex: 1;
                    text-align: right;
                    padding: 0 8px;
                }
                
                .item-price, .item-total {
                    font-weight: 500;
                }
                
                .grand-total {
                    background-color: #f8f9fa;
                    padding: 16px 20px;
                    text-align: right;
                    font-weight: 600;
                    font-size: 18px;
                    color: #2c3e50;
                    border-top: 1px solid #eaeaea;
                }
                
                .no-orders {
                    text-align: center;
                    padding: 40px 20px;
                    color: #6c757d;
                }
                
                .no-orders i {
                    font-size: 64px;
                    margin-bottom: 16px;
                    color: #dee2e6;
                }
                
                .no-orders h3 {
                    margin-bottom: 8px;
                    color: #6c757d;
                }
                
                .btn-view-order {
                    background-color: #6f42c1;
                    color: white;
                    border: none;
                    padding: 8px 16px;
                    border-radius: 6px;
                    font-size: 14px;
                    transition: background-color 0.2s;
                }
                
                .btn-view-order:hover {
                    background-color: #5a32a3;
                    color: white;
                }
                
                @media (max-width: 768px) {
                    .order-details {
                        grid-template-columns: 1fr;
                    }
                    
                    .item-list li {
                        flex-direction: column;
                    }
                    
                    .item-quantity, .item-price, .item-total {
                        text-align: left;
                        padding: 4px 0;
                    }
                }
        </style>
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
                    <li><a href=""><i class="bi bi-gear-fill"></i> Profile Settings</a></li>
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
                   <p class="tips">No order record found<p>
                    <h2 class="mb-4">Your Orders</h2>
                        <?php foreach ($orderList as $order): ?>
                            <div class="order-card">
                                <div class="order-header">
                                    <h3 class="order-id">Order ID: <?php echo $order['order_id']; ?></h3>
                                    <span class="order-status status-paid">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </div>
                                
                                <div class="order-body">
                                    <div class="order-details">
                                        <div class="detail-item">
                                            <span class="detail-label">Placed On</span>
                                            <span class="detail-value"><?php echo date('M j, Y g:i A', strtotime($order['placed_at'])); ?></span>
                                        </div>
                                        
                                        <div class="detail-item">
                                            <span class="detail-label">Items</span>
                                            <span class="detail-value"><?php echo count($order['itemList']); ?></span>
                                        </div>
                                        
                                        <div class="detail-item">
                                            <span class="detail-label">Order Total</span>
                                            <span class="detail-value">RM <?php echo number_format($order['grand_total'], 2); ?></span>
                                        </div>
                                    </div>
                                    
                                    <div class="order-items">
                                        <h4 class="items-title"><i class="bi bi-list-check"></i> Order Items</h4>
                                        <ul class="item-list">
                                            <?php foreach ($order['itemList'] as $item): ?>
                                                <li>
                                                    <div class="item-info">
                                                        <div class="item-name"><?php echo htmlspecialchars($item['product_name']); ?></div>
                                                        <div class="item-sku">SKU: <?php echo $item['sku']; ?></div>
                                                    </div>
                                                    <div class="item-quantity">Qty: <?php echo $item['quantity']; ?></div>
                                                    <div class="item-price">RM <?php echo number_format($item['unit_price'], 2); ?></div>
                                                    <div class="item-total">RM <?php echo number_format($item['line_total'], 2); ?></div>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                                
                                <div class="grand-total">
                                    Grand Total: RM <?php echo number_format($order['grand_total'], 2); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>

                </div>
            </div>
        </div>

    </body>
</html>