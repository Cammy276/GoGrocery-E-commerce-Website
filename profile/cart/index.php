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

// Fetch all cart items based on user id
$cartStmt = $conn->prepare("SELECT * FROM cart_items WHERE user_id = ? ORDER BY product_name DESC");
$cartStmt->bind_param("i", $user_id);
if ($cartStmt->execute()) {
    $cartResult = $cartStmt->get_result();
    $cartList = [];

    while ($cartItem = $cartResult->fetch_assoc()) {
        $cartList[] = $cartItem; //store order in orderList
    }
} else {
    $errorMsg = $cartStmt->error;
}


$cartStmt->close();

$errorMsgList = [];
// Handle checkout
if (isset($_POST['checkout'])) {
    foreach ($_POST['quantity'] as $cart_id => $qty) {
        $qty = max(1, intval($qty)); // ensure quantity is at least 1
        $updateStmt = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE cart_item_id = ? AND user_id = ?");
        $updateStmt->bind_param("iii", $qty, $cart_id, $user_id);
        if (!$updateStmt->execute()) {
            $errorMsgList[] = "Failed to update cart item #$cart_id: " . $updateStmt->error;
        }
        $updateStmt->close();
    }

    if (empty($errorMsgList)) {
        header("Location: ../../payment/index.php"); // replace with your payment page
        exit;
    }
    
}

// Handle delete
if (isset($_POST['delete'])) {
    $delete_id = intval($_POST['delete']);
    $deleteStmt = $conn->prepare("DELETE FROM cart_items WHERE cart_item_id = ? AND user_id = ?");
    $deleteStmt->bind_param("ii", $delete_id, $user_id);

    if ($deleteStmt->execute()) {
        // Refresh to update the cart list
        header("Location: index.php");
        exit;
    } else {
        $errorMsgList[] = "Failed to delete cart item #$delete_id: " . $deleteStmt->error;
    }
    $deleteStmt->close();
}


// Handle quantity update (auto-save)
if (isset($_POST['updateQuantity']) && isset($_POST['quantity'])) {
    foreach ($_POST['quantity'] as $cart_id => $qty) {
        $qty = max(1, intval($qty));
        $updateStmt = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE cart_item_id = ? AND user_id = ?");
        $updateStmt->bind_param("iii", $qty, $cart_id, $user_id);
        $updateStmt->execute();
        $updateStmt->close();
    }

    // Refresh the page after update
    header("Location: index.php");
    exit;
}

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Cart</title>
        
        <!-- Inter font -->
        <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
        <!-- Bootstrap -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
        <!-- Custom CSS -->
        <link rel="stylesheet" href="../../css/profile_styles.css">
        <link rel="stylesheet" href="../../css/cart_styles.css">
        <link rel="stylesheet" href="../../css/styles.css">
        <link rel="stylesheet" href="../../css/header_styles.css">
        <link rel="stylesheet" href="../../css/footer_styles.css">


    </head>
    <body>
        <header><?php include("../../header.php") ?></header>

        <div class="cart-container">

            <!-- left side bar setting -->
            <div id="profileSettingSideBar">  
                <ul class="menu-items">
                    <!-- use Bootstrap icons-->
                    <li><a href="../settings/index.php"><i class="bi bi-gear-fill"></i> Profile Settings</a></li>
                    <li><a href="../deliveryAddress/index.php"><i class="bi bi-geo-alt-fill"></i> Delivery Addresses</a></li>
                    <li><a href="../cart/index.php" class="active"><i class="bi bi-cart3"></i> Cart</a></li>
                    <li><a href="../order/index.php"><i class="bi bi-bag-fill"></i> Orders</a></li>
                    <li><a href="../history/index.php"><i class="bi bi-clock-history"></i> History</a></li>
                    <li><a href="../wishlist/index.php"><i class="bi bi-heart"></i> Wishlist</a></li>
                    <li><a href="../reward/index.php"><i class="bi bi-award-fill"></i> Rewards</a></li>
                     <li><a href="../../auth/logout.php"><i class="bi bi-box-arrow-right"></i> Log Out</a></li>
                     <li><a href="../../auth/delete_account.php"><i class="bi bi-trash"></i> Delete Account</a></li>
                </ul>
            </div>
            
            <!--- right content space -->
            <div id="profileContent">
                <div class="content-header">
                    <h1>Cart</h1>
                    <p>View the items you have added before proceeding to checkout.</p>
                </div>
                <div class="content">
                   <h2>My Cart</h2>

                   <!--- shows message if error or no record -->
                    <?php if (!empty($errorMsg)): ?>
                        <p class="errMessage">Error occurred when fetching records. Please try again.</p>
                        <pre class="errMessage"><?php echo htmlspecialchars($errorMsg); ?></pre>
                    <?php elseif (!empty($errorMsgList)): ?>
                        <p class="errMessage">Error occurred when uploading records. Please try again.</p>
                        <?php foreach($errorMsgList as $err): ?>
                        <pre class="errMessage"><?php echo htmlspecialchars($err); ?></pre>
                        <?php endforeach;?>
                    <?php elseif (count($cartList) === 0): ?>
                        <p class="tips">No item in cart</p>
                    <?php else: ?>
                        <br/>
                    <?php endif; ?>
                     
                    <form id="cartForm" method="POST">
<?php foreach ($cartList as $item): 
    // Fetch product image for this cart item
    $productStmt = $conn->prepare("SELECT * FROM product_images WHERE product_id = ?");
    $productStmt->bind_param("i", $item['product_id']);
    $productInfo = null;

    if ($productStmt->execute()) {
        $productResult = $productStmt->get_result();
        $productInfo = $productResult->fetch_assoc();
    }
    $productStmt->close();

    // Determine image path, fallback to placeholder if missing
    $imagePath = $productInfo['product_image_url'] ?? 'images/products/placeholder.png';
    $altText = $productInfo['alt_text'] ?? 'Product Image';

    // Build full URL
    $imageUrl = rtrim(BASE_URL, '/') . '/' . ltrim($imagePath, '/');
?>
    <div class="cart-item-card">  
        <!-- Delete button -->
        <button type="submit" class="deleteButton cart-deleteButton" 
                name="delete" value="<?= $item['cart_item_id']; ?>">X</button>

        <!-- Product Image -->
        <div class="cart-item-image">
            <img src="<?= htmlspecialchars($imageUrl) ?>" alt="<?= htmlspecialchars($altText) ?>">
        </div>
        
        <!-- Product Info -->
        <div class="cart-item-content">
            <div class="cart-item-header">
                <div class="cart-item-info">
                    <p class="cart-item-name"><?= htmlspecialchars($item['product_name']); ?></p>
                    <p class="cart-item-sku">SKU: <?= htmlspecialchars($item['sku']); ?></p>
                    <p class="cart-item-price">
                        Unit price: RM <?= number_format($item['unit_price'], 2); ?>
                    </p>
                    <p class="cart-item-price">
                        Product Discount: <?= !empty($item['line_discount']) && $item['line_discount'] > 0 ? 
                            '-RM ' . number_format($item['line_discount'], 2) : '-'; ?>
                    </p>
                </div>

                <div class="cart-detail-item">
                    <label class="cart-detail-label">Quantity</label>
                    <input type="number" class="cart-quantity-input"
                        name="quantity[<?= $item['cart_item_id']; ?>]"
                        value="<?= $item['quantity']; ?>" 
                        min="1" 
                        data-cart-id="<?= $item['cart_item_id']; ?>">
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<input type="submit" class="checkoutButton" name="checkout" value="Proceed to Checkout" <?= (count($cartList) === 0) ? 'disabled' : ''; ?>>
</form>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // Grab all quantity inputs
                const quantityInputs = document.querySelectorAll(".cart-quantity-input");


                quantityInputs.forEach(input => {
                    input.addEventListener("change", function() {
                        // Submit the form immediately when quantity changes
                        const form = document.getElementById("cartForm");

                        // Create a hidden input so PHP knows this is a quantity update
                        let hidden = document.createElement("input");
                        hidden.type = "hidden";
                        hidden.name = "updateQuantity";
                        hidden.value = "1";
                        form.appendChild(hidden);

                        form.submit();
                    });
                });
            });
        </script>

        <footer><?php include("../../footer.php") ?> </footer>
    </body>
</html>