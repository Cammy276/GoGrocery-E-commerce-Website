<!-- to get current user id -->
<?php
if (session_status() === PHP_SESSION_NONE){
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
$errorMsgList = [];

// Fetch all wishlist items based on user id
$wishStmt = $conn->prepare("
    SELECT w.product_id, w.created_at, p.product_name, p.sku, p.unit_price,
    p.discount_percent, p.special_offer_label, pi.product_image_url, pi.alt_text
    FROM wishlist w
    JOIN products p ON w.product_id = p.product_id
    LEFT JOIN product_images pi ON p.product_id = pi.product_id
    WHERE w.user_id = ?
    ORDER BY w.created_at DESC
");
$wishStmt->bind_param("i", $user_id);
$wishList = [];

if ($wishStmt->execute()) {
    $wishResult = $wishStmt->get_result();

    while ($row = $wishResult->fetch_assoc()) {
        $wishList[] = $row;
    }
} else {
    $errorMsg = $wishStmt->error;
}

$wishStmt->close();

// Handle delete from wishlist
if (isset($_POST['delete'])) {
    $delete_id = intval($_POST['delete']);
    $deleteStmt = $conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
    $deleteStmt->bind_param("ii", $user_id, $delete_id);

    if ($deleteStmt->execute()) {
        // Refresh to update the cart list
        header("Location: index.php");
        exit;
    } else {
        $errorMsgList[] = "Failed to delete from wishlist: " . $deleteStmt->error;
    }
    $deleteStmt->close();
}

// Handle move to cart
if (isset($_POST['moveToCart'])) {
    $product_id = intval($_POST['moveToCart']);

    // Fetch product details for insert into cart
    $prodStmt = $conn->prepare("SELECT product_name, sku, unit_price, 0 as line_discount FROM products WHERE product_id = ?");
    $prodStmt->bind_param("i", $product_id);
    $prodStmt->execute();
    $product = $prodStmt->get_result()->fetch_assoc();
    $prodStmt->close();

    if ($product) {
        // Insert or update into cart
        $insertStmt = $conn->prepare("
            INSERT INTO cart_items (user_id, product_id, product_name, sku, unit_price, quantity, line_discount)
            VALUES (?, ?, ?, ?, ?, 1, ?)
            ON DUPLICATE KEY UPDATE quantity = quantity + 1
        ");
        $insertStmt->bind_param(
            "iissdd",
            $user_id,
            $product_id,
            $product['product_name'],
            $product['sku'],
            $product['unit_price'],
            $product['line_discount']
        );

        if ($insertStmt->execute()) {
            // Remove from wishlist after moving
            $deleteStmt = $conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
            $deleteStmt->bind_param("ii", $user_id, $product_id);
            $deleteStmt->execute();
            $deleteStmt->close();

            header("Location:../cart/index.php");
            exit;
        } else {
            $errorMsgList[] = "Failed to add product to cart: " . $insertStmt->error;
        }
        $insertStmt->close();
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Wishlist</title>
    
    <!-- Inter font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../css/profile_styles.css">
    <link rel="stylesheet" href="../../css/cart_styles.css">
    <link rel="stylesheet" href="../../css/header_styles.css">
    <link rel="stylesheet" href="../../css/footer_styles.css">
</head>
<body>
    <header><?php include("../../header.php") ?></header>

    <div class="main-container">

        <!-- Left sidebar setting -->
        <div id="profileSettingSideBar">  
            <ul class="menu-items">
                <!-- Use Bootstrap icons-->
                <li><a href="../settings/index.php"><i class="bi bi-gear-fill"></i> Profile Settings</a></li>
                <li><a href="../deliveryAddress/index.php"><i class="bi bi-geo-alt-fill"></i> Delivery Addresses</a></li>
                <li><a href="../cart/index.php"><i class="bi bi-cart3"></i> Cart</a></li>
                <li><a href="../order/index.php"><i class="bi bi-bag-fill"></i> Orders</a></li>
                <li><a href="../history/index.php"><i class="bi bi-clock-history"></i> History</a></li>
                <li><a href="../wishlist/index.php" class="active"><i class="bi bi-heart"></i> Wishlist</a></li>
                <li><a href="../reward/index.php"><i class="bi bi-award-fill"></i> Rewards</a></li>
                <li><a href="../../auth/logout.php"><i class="bi bi-box-arrow-right"></i> Log Out</a></li>
                <li><a href="../../auth/delete_account.php"><i class="bi bi-trash"></i> Delete Account</a></li>
            </ul>
        </div>
        
        <!--- Main content -->
        <div id="profileContent">
            <div class="content-header">
                <h1>Wishlist</h1>
                <p>Items you saved for later. You can move them to your cart anytime.</p>
            </div>

            <div class="content">
                <h2>My Wishlist</h2>

                <!--- Show message if error or no record -->
                <?php if (!empty($errorMsg)): ?>
                    <p class="errMessage">Error occurred when fetching records. Please try again.</p>
                    <pre class="errMessage"><?php echo htmlspecialchars($errorMsg); ?></pre>
                <?php elseif (!empty($errorMsgList)): ?>
                    <p class="errMessage">Error occurred when uploading records. Please try again.</p>
                    <?php foreach($errorMsgList as $err): ?>
                        <pre class="errMessage"><?php echo htmlspecialchars($err); ?></pre>
                    <?php endforeach;?>
                <?php elseif (count($wishList) === 0): ?>
                    <p class="tips">No item in wishlist</p>
                <?php else: ?>
                    <br/>
                <?php endif; ?>

                <form id="wishlistForm" method = "POST">
                    <?php foreach ($wishList as $item): 
                        $final_price = $item['unit_price'];
                        $discount_amount = 0;
                        if (!empty($item['discount_percent']) && $item['discount_percent'] > 0) {
                            $discount_amount = $item['unit_price'] * ($item['discount_percent'] / 100);
                            $final_price = $item['unit_price'] - $discount_amount;
                        }
                    ?>
                        <div class="cart-item-card">  
                            <!--- Delete item --->                          
                            <button type="submit" class="deleteButton cart-deleteButton" name="delete" value="<?php echo $item['product_id']; ?>" >X</button>

                            <div class="cart-item-image">
                                <img src="<?php echo htmlspecialchars('../' . $item['product_image_url']); ?>" 
                                    alt="<?php echo htmlspecialchars($item['alt_text']); ?>" />
                            </div>
                            
                            <div class="cart-item-content">
                                <div class="cart-item-header">
                                    <div class="cart-item-info">
                                        <p class="cart-item-name"><?php echo htmlspecialchars($item['product_name']); ?></p>
                                        <p class="cart-item-sku">SKU: <?php echo htmlspecialchars($item['sku']); ?></p>
                                        <p class="cart-item-price">Unit price: RM <?php echo number_format($item['unit_price'], 2); ?></p>
                                        <p class="cart-item-price">
                                            Product Discount: 
                                            <?php echo $discount_amount > 0
                                                ? '-RM ' . number_format($discount_amount, 2)
                                                : '-'; ?>
                                        </p>
                                        <p class="cart-item-price"> Final Price: RM <?php echo number_format($final_price, 2); ?></p>
                                    </div>                                  
                                </div>
                                <button type="submit" class="btn btn-primary" name="moveToCart" value="<?php echo $item['product_id']; ?>">Move to Cart</button>
                            </div>
                        </div>
                    <?php endforeach; ?>            
                </form> 
            </div>
        </div>
    </div>

    <footer><?php include("../../footer.php") ?> </footer>
</body>
</html>