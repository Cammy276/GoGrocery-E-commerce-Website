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
include(__DIR__ . '/../connect_db.php');

// Predefine variable for error message
$errorMsg = null;

$subtotal = 0;
// Fetch all cart items based on user id
$cartStmt = $conn->prepare("SELECT * FROM cart_items WHERE user_id = ? ORDER BY product_name DESC");
$cartStmt->bind_param("i", $user_id);
if ($cartStmt->execute()) {
    $cartResult = $cartStmt->get_result();
    $cartList = [];

    while ($cartItem = $cartResult->fetch_assoc()) {
        $cartList[] = $cartItem; //store order in orderList

        $subtotal += $cartItem['quantity'] * $cartItem['unit_price'];
    }
} else {
    $errorMsg = $cartStmt->error;
}



// get available voucher
$sql = "
    SELECT
        v.voucher_id,
        v.voucher_name,
        v.description,
        v.terms_conditions,
        v.voucher_image_url,
        v.discount_type,
        v.discount_value,
        v.min_subtotal,
        v.start_date,
        v.end_date
    FROM user_vouchers uv
    JOIN vouchers v ON uv.voucher_id = v.voucher_id
    WHERE uv.user_id = ?
      AND uv.isUsed = 0
      AND NOW() BETWEEN v.start_date AND v.end_date
      AND v.min_subtotal <= ?
    ORDER BY v.start_date ASC
";

$voucherStmt = $conn->prepare($sql);
$voucherStmt->bind_param("id", $user_id, $subtotal);

$availableVouchers = [];
if ($voucherStmt->execute()) {
    $voucherResult = $voucherStmt->get_result();
    while ($voucher = $voucherResult->fetch_assoc()) {
        $availableVouchers[] = $voucher;
    }
} else {
    $errorMsg = $voucherStmt->error;
}
$voucherStmt->close();




$addressStmt = $conn->prepare("SELECT * FROM addresses WHERE user_id = ? ORDER BY label ASC");
$addressStmt->bind_param("i", $user_id); 

if ($addressStmt->execute()) {
    $result = $addressStmt->get_result();

    $addressList = [];
    while ($row = $result->fetch_assoc()) {
        $addressList[] = $row;
    }
} else {
    $errorMsg = $addressStmt->error;
}




//handle confirm payment

// 🔹 Step 1: Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address_id     = $_POST['address_id'] ?? null;
    $payment_method = $_POST['payment_method'] ?? null;
    $voucher_id     = !empty($_POST['voucher_id']) ? $_POST['voucher_id'] : null;
    $voucher_discount_value = floatval($_POST['voucher_discount'] ?? 0);
    $shipping_fee = isset($_POST['shipping_fee']) ? floatval($_POST['shipping_fee']) : 4.50;
    $delivery_duration = ($shipping_fee === 4.50)  ? "3-7 working days" : "5-15 working days";
    $status         = "paid";
    $placed_at      = date("Y-m-d H:i:s");

    if (!$address_id || !$payment_method) {
        die("Missing required fields.");
    }

    // Fetch cart items
    $cartStmt = $conn->prepare("SELECT * FROM cart_items WHERE user_id = ?");
    $cartStmt->bind_param("i", $user_id);
    $cartStmt->execute();
    $cartResult = $cartStmt->get_result();

    $cartItems = [];
    $subtotal = 0;
    $totalLineDiscount = 0;

    while ($item = $cartResult->fetch_assoc()) {
        $cartItems[] = $item;
        $lineTotal = $item['unit_price'] * $item['quantity'];
        $subtotal += $lineTotal;
        $totalLineDiscount += $item['line_discount'];
    }
    $cartStmt->close();

    if (empty($cartItems)) {
        die("Cart is empty.");
    }

    // Apply discounts
    $subtotal -= $totalLineDiscount;
    $grand_total = ($subtotal - $voucher_discount_value) + $shipping_fee;

    // Insert into orders
    $orderStmt = $conn->prepare("
        INSERT INTO orders (
            user_id, address_id, status, payment_method, voucher_id, 
            subtotal, voucher_discount_value, shipping_fee, delivery_duration, placed_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $orderStmt->bind_param(
        "iissidddss",
        $user_id,
        $address_id,
        $status,
        $payment_method,
        $voucher_id,
        $subtotal,
        $voucher_discount_value,
        $shipping_fee,
        $delivery_duration,
        $placed_at
    );
    if (!$orderStmt->execute()) {
        die("Order insert failed: " . $orderStmt->error);
    }
    $order_id = $orderStmt->insert_id;
    $orderStmt->close();

    // Mark voucher as used 
    if (!empty($voucher_id)) {
        $updateVoucherStmt = $conn->prepare("
            UPDATE user_vouchers 
            SET isUsed = 1 
            WHERE user_id = ? AND voucher_id = ?
        ");
        $updateVoucherStmt->bind_param("ii", $user_id, $voucher_id);
        if (!$updateVoucherStmt->execute()) {
            die("Failed to update voucher status: " . $updateVoucherStmt->error);
        }
        $updateVoucherStmt->close();
    }


    // Insert into order_items
    $orderItemStmt = $conn->prepare("
        INSERT INTO order_items (
            order_id, product_id, product_name, sku, unit_price, quantity, line_discount
        ) VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    foreach ($cartItems as $item) {
        $line_total = ($item['unit_price'] * $item['quantity']) - $item['line_discount'];
        $orderItemStmt->bind_param(
            "iissdid",
            $order_id,
            $item['product_id'],
            $item['product_name'],
            $item['sku'],
            $item['unit_price'],
            $item['quantity'],
            $item['line_discount'],
        );
        if (!$orderItemStmt->execute()) {
            die("Order item insert failed: " . $orderItemStmt->error);
        }
    }
    $orderItemStmt->close();

    // Clear cart
    $clearStmt = $conn->prepare("DELETE FROM cart_items WHERE user_id = ?");
    $clearStmt->bind_param("i", $user_id);
    $clearStmt->execute();
    $clearStmt->close();

    //if operation success, redirect to order page
    header("Location: ../profile/order/index.php?msg=paymentSuccess&order_id=".$order_id);
    exit();

}
?>



<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Payment - GoGrocery</title>
        
        <!-- Inter font -->
        <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
        <!-- Bootstrap -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
        <!-- Custom CSS -->
                 <link rel="stylesheet" href="../css/payment_styles.css">
        <link rel="stylesheet" href="../css/profile_styles.css">

        <link rel="stylesheet" href="../css/header_styles.css">
        <link rel="stylesheet" href="../css/footer_styles.css">
        <style>

        </style>
    </head>
    <body>
        <header><?php include("../header.php") ?></header>

        <div class="main-container">
 
            <div id="profileContent">
                <div class="content-header">
                    <h1>Payment</h1>
                    <p>Review your order and complete your purchase</p>
                </div>
                
                <div class="content">
                    <form id="paymentForm" method="POST">
                        <div class="payment-container">
                            <div class="payment-left">

                                <!-- Order Items Section -->
                                <div class="payment-items">
                                    <h2 class="payment-section-title"><i class="bi bi-cart-check"></i> Order Items</h2>
                                    
                                    <?php
                                    $subtotal = 0;
                                    $totalLineDiscount = 0;
                                    $itemNum = 0;
                                    
                                    foreach ($cartList as $item): 
                                        
                                        //$lineTotal = $item['unit_price']*$item['quantity'] - $item['line_discount'];
                                        $lineTotal = $item['unit_price']*$item['quantity'];
                                        $totalLineDiscount += $item['line_discount'];
                                        $subtotal += $lineTotal;

                                        $itemNum += 1;
                        
                                        
                                        // Fetch product image
                                        $productStmt = $conn->prepare("SELECT * FROM product_images WHERE product_id=?");
                                        $productStmt->bind_param("i", $item['product_id']);
                                        if ($productStmt->execute()) {
                                            $productResult = $productStmt->get_result();
                                            $productInfo = $productResult->fetch_assoc();
                                        }
                                        $productStmt->close();
                                    ?>
                                    
                                    <div class="payment-item-card">
                                        <div class="payment-item-image">
                                            <img src="<?php echo htmlspecialchars($productInfo['product_image_url']); ?>" 
                                                alt="<?php echo htmlspecialchars($productInfo['alt_text']); ?>" />
                                        </div>
                                        
                                        <div class="payment-item-details">
                                            <div class="payment-item-info">
                                                <p class="payment-item-name"><?php echo htmlspecialchars($item['product_name']); ?></p>
                                                <p class="payment-item-small">SKU: <?php echo htmlspecialchars($item['sku']); ?></p>
                                                <p class="payment-item-small">
                                                    Unit discount: 
                                                    <?php echo ($item['line_discount'] > 0) ? "RM " . number_format($item['line_discount'], 2) : "-"; ?>
                                                </p>

                                            </div>
                                            
                                            <div class="payment-item-price">
                                                RM <?php echo number_format($item['unit_price'], 2); ?> /unit
                                            </div>
                                            
                                            <div class="payment-item-quantity">
                                                x <?php echo $item['quantity']; ?> unit(s)
                                            </div>
                                        
                                            
                                            <div class="payment-item-total">
                                                Total<br/>RM <?php echo number_format($lineTotal, 2); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                    <p class="tips">Note: Each item's total price has not yet adjusted to include any applicable item discount.</p>
                                </div>

                                <!-- Delivery Address Section -->
                                <div class="payment-options">
                                    <h2 class="payment-section-title"><i class="bi bi-geo-alt-fill"></i> Delivery Address</h2>
                                    
                                    <div class="payment-form-group">
                                        <label class="payment-form-label" for="delivery_address">Select Delivery Address</label>
                                        <select class="payment-form-select" id="delivery_address" name="address_id" >
                                            <option value="">-- Select Address --</option>
                                            <?php foreach ($addressList as $address): ?>
                                                <option 
                                                    value="<?php echo $address['address_id']; ?>" 
                                                    data-state="<?php echo htmlspecialchars($address['state_territory']); ?>">
                                                    <?php 
                                                        $parts = [];
                                                        if (!empty($address['apartment'])) $parts[] = $address['apartment'];
                                                        $parts[] = $address['street'];
                                                        $parts[] = $address['postcode'];
                                                        $parts[] = $address['city'];
                                                        $parts[] = $address['state_territory'];
                                                        echo htmlspecialchars($address['label'] . " - " . implode(", ", $parts));
                                                    ?>
                                                </option>

                                            <?php endforeach; ?>
                                        </select>
                                        <div id="error_DeliveryAddress" class="error"></div>
                                    </div>
                                </div>

                                
                                <!-- Payment Options Section -->
                                <div class="payment-options">
                                    <h2 class="payment-section-title"><i class="bi bi-credit-card"></i> Payment Options</h2>
                                    
                                    <div class="payment-form-group">
                                        <label class="payment-form-label" for="voucher">Select Voucher Applicable</label>
                                        <select class="payment-form-select" id="voucher" name="voucher_id">
                                            <?php if(count($availableVouchers) === 0): ?>
                                                <option value="">-- No Voucher is Applicable --</option>
                                            <?php else: ?>
                                                <option value="">-- Select Voucher --</option>
                                                <?php foreach ($availableVouchers as $voucher): ?>
                                                    <option 
                                                        value="<?php echo $voucher['voucher_id']; ?>" 
                                                        data-type="<?php echo $voucher['discount_type']; ?>" 
                                                        data-value="<?php echo $voucher['discount_value']; ?>">
                                                        <?php echo htmlspecialchars($voucher['description']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                        <p class="tips">Note: Only vouchers that meet the eligibility criteria will be displayed.</p>
                                    </div>
                                    
                                    <div class="payment-form-group">
                                
                                            <label for="method">Select Payment Method</label>
                                            <select class="payment-form-select" id="method" name="payment_method" >
                                                <option value="">-- Select --</option>
                                                <option value="card">Credit/Debit Card</option>
                                                <option value="bank_transfer">Bank Transfer (Manual)</option>
                                                <option value="cod">Cash on Delivery (COD)</option>
                                                <option value="e_wallet">E-Wallet</option>
                                                <option value="grabpay">GrabPay</option>
                                                <option value="fpx">FPX Online Banking</option>
                                            </select>
                                            <div id="error_paymentMethod" class="error"></div>

                                            <!-- CARD -->
                                            <div id="cardFields" class="hidden">
                                                <br/>
                                                <label>Card Number</label>
                                                <input type="text" class="textInput" id="card_number" name="card_number" maxlength="16">
                                                <div id="error_card" class="error"></div>

                                                <label>Expiry (MM/YY)</label>
                                                <input type="text" class="textInput" id="expiry" name="expiry" placeholder="MM/YY">
                                                <div id="error_expiry" class="error"></div>

                                                <label>CVV</label>
                                                <input type="password" class="textInput" id="cvv" name="cvv" maxlength="4">
                                                <div id="error_cvv" class="error"></div>

                                                <label>Name on Card</label>
                                                <input type="text" class="textInput" id="name" name="name">
                                                <div id="error_cardName" class="error"></div>
                                            </div>

                                            <!-- BANK TRANSFER MANUAL -->
                                            <div id="bankFields" class="hidden">
                                                <br/>
                                                <p>Please transfer the amount to:</p>
                                                <p><strong>Bank:</strong> Maybank</p>
                                                <p><strong>Account No:</strong> 123456789</p>
                                                <p><strong>Account Name:</strong> MyShop Sdn Bhd</p>

                                                <label>Reference Number</label>
                                                <input type="text" class="textInput" id="reference" name="reference">
                                                <div id="error_reference" class="error"></div>

                                                <label>Upload Receipt (jpg/png/pdf)</label>
                                                <input type="file" id="receipt" name="receipt" accept=".jpg,.jpeg,.png,.pdf">
                                                <div id="error_receipt" class="error"></div>
                                            </div>

                                            <!-- FPX -->
                                            <div id="fpxFields" class="hidden">
                                                <br/>
                                                <label>Select Bank</label>
                                                <select class="payment-form-select" id="bank_list" name="bank_list">
                                                    <option value="">-- Choose Your Bank --</option>
                                                    <option value="maybank2u">Maybank2u</option>
                                                    <option value="cimbclicks">CIMB Clicks</option>
                                                    <option value="rhb">RHB Now</option>
                                                    <option value="hongleong">Hong Leong Connect</option>
                                                    <option value="ambank">AmBank</option>
                                                    <option value="publicbank">Public Bank</option>
                                                </select>
                                                <div id="error_bank" class="error"></div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            
                                <!-- Order Summary Section -->
                                <div class="order-summary">
                                    <h2 class="payment-section-title"><i class="bi bi-receipt"></i> Order Summary</h2>
                                    
                                    <div class="summary-item">
                                        <p class="summary-label">Your items (<?php echo $itemNum ?>)</p>
                                        <p class="summary-value">RM <?php echo number_format($subtotal, 2); ?></p>
                                    </div>

                                    <div class="summary-item">
                                        <p class="summary-label">Total Item Discount:</p>
                                        <p class="summary-value discountValue">- RM <?php echo number_format($totalLineDiscount, 2); ?></p>
                                    </div>

                                    <?php $subtotal = $subtotal-$totalLineDiscount ?>
                                    <div class="summary-item summary-total">
                                        <p class="summary-label">Subtotal</p>
                                        <p class="summary-value">RM <?php echo number_format($subtotal, 2); ?></p>
                                    </div>
                                
                                    
                                    <div class="summary-item">
                                        <p class="summary-label">Voucher Discount</p>
                                        <p class="summary-value discountValue isVoucher">- RM 0.00</p>
                                    </div>

                                    <div class="summary-item">
                                        <p class="summary-label">Shipping Fee</p>
                                        <p class="summary-value" id="shipping-fee-display">RM 4.50</p>
                                        <input type="hidden" name="shipping_fee" id="shipping_fee" value="4.50">
                                    </div>

                                    
                                    <div class="summary-item summary-total">
                                        <p class="summary-label">Grand Total</p>
                                        <p class="summary-value" id = "grand-total">RM <?php echo number_format($subtotal + 5.00, 2); ?></p>
                                    </div>
                                    

                                <button type="submit" class="place-order-btn">Confirm Payment</button>
                            
                            </div>
                        </div>
                   </form>
                </div>
            </div>
        </div>

        <script>

            const form = document.getElementById("paymentForm");

            //to update summary value based on voucher
            document.addEventListener("DOMContentLoaded", function() {
                const voucherSelect = document.getElementById("voucher");
                const voucherDiscountEl = document.querySelector(".isVoucher");
                const totalEl = document.getElementById("grand-total");
                const subtotal = <?php echo json_encode($subtotal); ?>;
                const shippingFee = 5.00;

                // Hidden inputs to pass to backend
                const hiddenVoucherId = document.createElement("input");
                hiddenVoucherId.type = "hidden";
                hiddenVoucherId.name = "voucher_id";
                form.appendChild(hiddenVoucherId);

                const hiddenVoucherDiscount = document.createElement("input");
                hiddenVoucherDiscount.type = "hidden";
                hiddenVoucherDiscount.name = "voucher_discount";
                form.appendChild(hiddenVoucherDiscount);

                function updateTotals() {
                    let discountValue = 0;
                    const option = voucherSelect.options[voucherSelect.selectedIndex];
                    hiddenVoucherId.value = option.value || "";

                    // Base amount is subtotal (already after item discounts in PHP)
                    const baseAmount = subtotal;

                    if (option.value) {
                        const type = option.getAttribute("data-type");
                        const value = parseFloat(option.getAttribute("data-value"));

                        if (type === "PERCENT") {
                            discountValue = (baseAmount * value) / 100;
                        } else if (type === "FIXED") {
                            discountValue = Math.min(baseAmount, value);
                        }
                    }

                    // Update hidden input
                    hiddenVoucherDiscount.value = discountValue.toFixed(2);

                    // Update UI
                    voucherDiscountEl.textContent = "- RM " + discountValue.toFixed(2);

                    const finalTotal = (baseAmount - discountValue) + shippingFee;
                    totalEl.textContent = "RM " + finalTotal.toFixed(2);
                }

                // Trigger once on load
                updateTotals();

                // Update whenever voucher changes
                voucherSelect.addEventListener("change", updateTotals);
            });

            // update summary based on shipping fee (depends on delivery-address)
            document.addEventListener("DOMContentLoaded", function () {
                const addressSelect = document.getElementById("delivery_address");
                const shippingFeeEl = document.getElementById("shipping-fee-display"); // ✅ more precise
                const totalEl = document.getElementById("grand-total");
                const voucherSelect = document.getElementById("voucher");
                const hiddenShippingFee = document.getElementById("shipping_fee");

                const baseSubtotal = <?php echo json_encode($subtotal); ?>;

                function getVoucherDiscount() {
                    let discountValue = 0;
                    const option = voucherSelect.options[voucherSelect.selectedIndex];
                    if (option && option.value) {
                        const type = option.getAttribute("data-type");
                        const value = parseFloat(option.getAttribute("data-value"));
                        if (type === "PERCENT") {
                            discountValue = (baseSubtotal * value) / 100;
                        } else if (type === "FIXED") {
                            discountValue = Math.min(baseSubtotal, value);
                        }
                    }
                    return discountValue;
                }

                function updateTotals() {
                    const option = addressSelect.options[addressSelect.selectedIndex];
                    let shippingFee = 4.50; // default

                    if (option) {
                        const state = option.getAttribute("data-state");
                        if (state === "Sabah" || state === "Sarawak") {
                            shippingFee = 15.00;
                        }
                    }

                    const discountValue = getVoucherDiscount();
                    const finalTotal = (baseSubtotal - discountValue) + shippingFee;

                    // Update hidden shipping fee
                    hiddenShippingFee.value = shippingFee.toFixed(2);

                    // Update UI
                    shippingFeeEl.textContent = "RM " + shippingFee.toFixed(2);
                    totalEl.textContent = "RM " + finalTotal.toFixed(2);
                }

                // Run on load + when address/voucher changes
                updateTotals();
                addressSelect.addEventListener("change", updateTotals);
                voucherSelect.addEventListener("change", updateTotals);
            });



            // show / hide based on payment method
            const methodSelect = document.getElementById("method");
            const cardFields = document.getElementById("cardFields");
            const bankFields = document.getElementById("bankFields");
            const fpxFields = document.getElementById("fpxFields");
            
            // Toggle fields
            methodSelect.addEventListener("change", function() {
            cardFields.classList.add("hidden");
            bankFields.classList.add("hidden");
            fpxFields.classList.add("hidden");

            if (this.value === "card") cardFields.classList.remove("hidden");
            if (this.value === "bank_transfer") bankFields.classList.remove("hidden");
            if (this.value === "fpx") fpxFields.classList.remove("hidden");
            });

            // Validation of payment method and delivery address
            form.addEventListener("submit", function(e) {
                let valid = true;

                //for delivery address
                const deliveryAddress = document.querySelector("select[name='address_id']").value;
                const deliveryAddressInput = document.querySelector("select[name='address_id']");
                if (!deliveryAddress) { 
                    document.getElementById("error_DeliveryAddress").textContent = "Please select a delivery address.";
                    deliveryAddressInput.classList.add("input-error");
                    valid = false; 
                } else {
                    document.getElementById("error_DeliveryAddress").textContent = "";
                    deliveryAddressInput.classList.remove("input-error");
                }

                //for payment method
                const paymentMethod = document.querySelector("select[name='payment_method']").value;
                const paymentMethodInput = document.querySelector("select[name='payment_method']");
                if (!paymentMethod) { 
                    document.getElementById("error_paymentMethod").textContent = "Please select a payment method.";
                    paymentMethodInput.classList.add("input-error");
                    valid = false; 
                } else {
                    document.getElementById("error_paymentMethod").textContent = "";
                    paymentMethodInput.classList.remove("input-error");
                }


                const cardInput = document.getElementById("card_number");
                const expiryInput = document.getElementById("expiry");
                const cvvInput = document.getElementById("cvv");
                const cardNameInput = document.getElementById("name");
                const receiptInput = document.getElementById("receipt");
                const referenceInput = document.getElementById("reference");
                const bankSelect = document.getElementById("bank_list");

                if (methodSelect.value === "card") {
                    const card = document.getElementById("card_number").value.trim();
                    if (!/^\d{16}$/.test(card)) {
                        document.getElementById("error_card").textContent = "Card number must be 16 digits.";
                        cardInput.classList.add("input-error")
                        valid = false;
                    } else {
                        document.getElementById("error_card").textContent = "";
                        cardInput.classList.remove("input-error");
                    }

                    const expiry = document.getElementById("expiry").value.trim();
                    if (!/^(0[1-9]|1[0-2])\/\d{2}$/.test(expiry)) {
                        document.getElementById("error_expiry").textContent = "Invalid expiry format (MM/YY).";
                        expiryInput.classList.add("input-error");
                        valid = false;
                    } else {
                        document.getElementById("error_expiry").textContent = "";
                        expiryInput.classList.remove("input-error");
                    }

                    const cvv = document.getElementById("cvv").value.trim();
                    if (!/^\d{3,4}$/.test(cvv)) {
                        document.getElementById("error_cvv").textContent = "CVV must be 3 or 4 digits.";
                        cvvInput.classList.add("input-error");
                        valid = false;
                    } else {
                        document.getElementById("error_cvv").textContent = "";
                        cvvInput.classList.remove("input-error");
                    }

                    const cardName = document.getElementById("name").value.trim();
                    if (!/^[A-Za-z\s'-]+$/.test(cardName)) {
                        document.getElementById("error_cardName").textContent = "Name on card can only contain letters, spaces, hyphens, and apostrophes";
                        cardNameInput.classList.add("input-error");
                        valid = false;
                    } else {
                        document.getElementById("error_cardName").textContent = "";
                        cardNameInput.classList.remove("input-error");
                    }


                }

                if (methodSelect.value === "bank_transfer") {
                    const receipt = document.getElementById("receipt").files.length;
                    if (receipt === 0) {
                        document.getElementById("error_receipt").textContent = "Please upload payment receipt.";
                        receiptInput.classList.add("input-error");
                        valid = false;
                    } else {
                        document.getElementById("error_receipt").textContent = "";
                        receiptInput.classList.remove("input-error");
                    }

                    const reference = document.getElementById("reference").value.trim();
                    if (!/^[A-Za-z0-9]{6,12}$/.test(reference)) {
                    document.getElementById("error_reference").textContent = "Reference number must be 6–12 letters or digits.";
                    referenceInput.classList.add("input-error");
                    valid = false;
                    } else {
                        document.getElementById("error_reference").textContent = "";
                        referenceInput.classList.remove("input-error");
                    }
                }

                if (methodSelect.value === "fpx") {
                    const bank = document.getElementById("bank_list").value.trim();
                    if (bank === "") {
                    document.getElementById("error_bank").textContent = "Please select your bank.";
                    bankSelect.classList.add("input-error");
                    valid = false;
                    } else {
                        document.getElementById("error_bank").textContent = "";
                        bankSelect.classList.remove("input-error");
                    }
                }

                if (!valid) {
                    e.preventDefault();
                    if (deliveryAddressInput.classList.contains("input-error")) {
                        deliveryAddressInput.focus();
                    } else if (paymentMethodInput.classList.contains("input-error")) {
                        paymentMethodInput.focus();
                    } else if (cardInput.classList.contains("input-error")) {
                        cardInput.focus();
                    } else if (expiryInput.classList.contains("input-error")) {
                        expiryInput.focus();
                    } else if (cvvInput.classList.contains("input-error")) {
                        cvvInput.focus();
                    } else if (cardNameInput.classList.contains("input-error")) {
                        cardNameInput.focus();
                    } else if (referenceInput.classList.contains("input-error")) {
                        referenceInput.focus();
                    } else if (receiptInput.classList.contains("input-error")) {
                        receiptInput.focus();
                    } else if (bankSelect.classList.contains("input-error")) {
                        bankSelect.focus();
                    }
                }
            });
        </script>

        <footer><?php include("../footer.php") ?> </footer>
    </body>
</html>