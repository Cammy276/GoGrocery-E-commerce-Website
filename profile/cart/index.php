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
        <link rel="stylesheet" href="../../css/profile.css">
        <link rel="stylesheet" href="../../css/header_styles.css">
        <link rel="stylesheet" href="../../css/footer_styles.css">
        <style>
            .cart-item-card {
                display: flex;
                border-radius: 12px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.08);
                margin-bottom: 20px;
                overflow: hidden;
                border: 1px solid #eaeaea;
                background: white;
                transition: all 0.3s ease;
                position: relative; /* ensure button is placed inside */
            }

            .cart-item-card:hover {
                transform: translateY(-3px);
                box-shadow: 0 6px 16px rgba(0,0,0,0.12);
            }

            .cart-item-image {
                flex: 0 0 120px;
                background: #f8f9fa;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 15px;
            }

            .cart-item-image img {
                max-width: 100%;
                max-height: 100px;
                object-fit: contain;
            }

            .cart-item-content {
                flex: 1;
                padding: 20px;
                display: flex;
                flex-direction: column;
            }

            .cart-item-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                width: 100%;
            }

            .cart-item-info {
                flex: 1;
                display: flex;
                flex-direction: column;
                gap: 5px;
            }

            .cart-item-name {
                font-weight: 600;
                font-size: 18px;
                color: #2c3e50;
                margin-bottom: 5px;
            }

            .cart-item-sku {
                font-size: 14px;
                color: #6c757d;
            }

            .cart-item-price {
                font-weight: 600;
                font-size: 18px;
                color: #2c3e50;
            }

            .cart-item-quantity {
                flex-shrink: 0;
            }

            .cart-quantity-input {
                width: 70px;
                padding: 8px;
                border: 1px solid #ddd;
                border-radius: 6px;
                text-align: center;
                font-size: 16px;
                transition: border-color 0.2s ease, box-shadow 0.2s ease;
            }

            .cart-quantity-input:hover {
                border-color: #4a6cf7;
            }

            .cart-quantity-input:focus {
                border-color: #4a6cf7;
                box-shadow: 0 0 5px rgba(74, 108, 247, 0.4);
                outline: none; 
            }

            .discount-value {
                font-size: 14px;
                font-weight: 500;
                color: #dc3545;
            }

            .cart-summary {
                background: white;
                border-radius: 12px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.08);
                padding: 25px;
                margin-top: 30px;
                border: 1px solid #eaeaea;
            }

            .summary-title {
                font-size: 20px;
                font-weight: 600;
                margin-bottom: 20px;
                color: #2c3e50;
                display: flex;
                align-items: center;
            }

            .summary-title i {
                margin-right: 10px;
                color: #4a6cf7;
            }

            .summary-item {
                display: flex;
                justify-content: space-between;
                padding: 10px 0;
                border-bottom: 1px solid #f1f1f1;
            }

            .summary-item:last-child {
                border-bottom: none;
                font-weight: 700;
                font-size: 18px;
                color: #2c3e50;
                padding-top: 15px;
            }

            .cart-detail-item { display: flex; flex-direction: column; } 
            .cart-detail-label { font-size: 12px; color: #6c757d; margin-bottom: 5px; }

            .checkoutButton {
                background: #4a6cf7;
                color: white;
                border: none;
                padding: 15px 30px;
                border-radius: 8px;
                font-weight: 600;
                font-size: 16px;
                width: 100%;
                margin-top: 20px;
                transition: background-color 0.3s;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 10px;
            }

            .checkoutButton:hover {
                background: #3048b4;
            }

            .checkoutButton:disabled {
                background: #ccc;
                cursor: not-allowed;
            }

            .cart-deleteButton {
                top: 8px;
                left: 8px;
                background: transparent;
                border: none;
                cursor: pointer;
                color: #dc3545;
                font-size: 18px;
                transition: color 0.2s ease;
                padding: 8px;
            }



            @media (max-width: 768px) {
                .cart-item-card {
                    flex-direction: column;
                }

                .cart-item-image {
                    flex: 0 0 auto;
                    height: 120px;
                }
            }
        </style>

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
                    <li><a href="../cart/index.php" class="active"><i class="bi bi-cart3"></i> Cart</a></li>
                    <li><a href="../order/index.php"><i class="bi bi-bag-fill"></i> Orders</a></li>
                    <li><a href="../history/index.php"><i class="bi bi-clock-history"></i> History</a></li>
                    <li><a href=""><i class="bi bi-heart"></i> Wishlist</a></li>
                    <li><a href=""><i class="bi bi-award-fill"></i> Rewards</a></li>
                     <li><a href="../../auth/logout.php"><i class="bi bi-box-arrow-right"></i> Log Out</a></li>
                </ul>
            </div>
            
            <!--- right content space -->
            <div id="profileContent">
                <div class="content-header">
                    <h1>Cart</h1>
                    <p>View the items you have added before proceeding to checkout</p>
                </div>
                <div class="content">
                   <h2>Cart List</h2>

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

                    <form id="cartForm" method = "POST">
                        <?php 
                            $subtotal = 0;
                            $totalDiscount = 0;
                            foreach ($cartList as $item): 
                                $lineDiscountTotal = $item['line_discount'] * $item['quantity'];
                                $lineTotal = ($item['unit_price'] * $item['quantity']) - $lineDiscountTotal;

                                $subtotal += $lineTotal;
                                $totalDiscount += $lineDiscountTotal;

                                $productStmt = $conn->prepare("SELECT * FROM product_images WHERE product_id=?");
                                $productStmt->bind_param("i", $item['product_id']);
                                if ($productStmt->execute()) {
                                    $productResult = $productStmt->get_result();
                                    $productInfo = $productResult->fetch_assoc();
                                } else {
                                    $errorMsg = $productStmt->error;
                                }
                        ?>
                
                            <div class="cart-item-card">
                            
                                    
                                
                                <button type="submit" class="deleteButton cart-deleteButton" name="delete" value="<?php echo $item['cart_item_id']; ?>" >X</button>

                        


                                <div class="cart-item-image">
                                    <img src="<?php echo htmlspecialchars('../' . $productInfo['product_image_url']); ?>" 
                                        alt="<?php echo htmlspecialchars($productInfo['alt_text']); ?>" />
                                </div>
                                
                                <div class="cart-item-content">
                                    <div class="cart-item-header">
                                        <div class="cart-item-info">
                                            <p class="cart-item-name"><?php echo htmlspecialchars($item['product_name']); ?></p>
                                            <p class="cart-item-sku">SKU: <?php echo htmlspecialchars($item['sku']); ?></p>
                                            <p class="cart-item-price">
                                                Unit price: RM <?php echo number_format($item['unit_price'], 2); ?>
                                            </p>
                                            <p class="cart-detail-value discount-value">
                                                Product Discount: 
                                                <?php echo !empty($item['line_discount']) && $item['line_discount'] > 0 ? 
                                                '-RM ' . number_format($item['line_discount'], 2) : 
                                                '-'; ?>
                                            </p>
                                        </div>

                                        <div class="cart-item-details">
                                        
                                        
                                        
                                    </div>
                                    <div class="cart-detail-item">
                                        <label class="cart-detail-label">Quantity</label>
                                        <input type="number" class="cart-quantity-input"
                                            name="quantity[<?php echo $item['cart_item_id']; ?>]"
                                            value="<?php echo $item['quantity']; ?>" 
                                            min="1" 
                                            data-cart-id="<?php echo $item['cart_item_id']; ?>">
                                    </div>

                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        
                        <input type="submit" class="checkoutButton" name="checkout" value="Proceed to Checkout" <?php echo (count($cartList) === 0) ? 'disabled' : ''; ?>>

                
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