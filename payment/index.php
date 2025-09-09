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
    SELECT v.*
    FROM vouchers v
    LEFT JOIN (
        SELECT voucher_id, COUNT(*) AS used_count
        FROM voucher_usages
        GROUP BY voucher_id
    ) vu_total ON v.voucher_id = vu_total.voucher_id
    LEFT JOIN (
        SELECT voucher_id, COUNT(*) AS user_used_count
        FROM voucher_usages
        WHERE user_id = ?
        GROUP BY voucher_id
    ) vu_user ON v.voucher_id = vu_user.voucher_id
    WHERE v.is_active = 1
      AND NOW() BETWEEN v.start_date AND v.end_date
      AND v.min_order_amount <= ?
      -- Check global usage limit if set
      AND (v.usage_limit IS NULL OR COALESCE(vu_total.used_count, 0) < v.usage_limit)
      -- Check per-user usage limit
      AND (COALESCE(vu_user.user_used_count, 0) < v.per_user_limit OR v.per_user_limit IS NULL)
";

$voucherStmt = $conn->prepare($sql);
$voucherStmt->bind_param("id", $user_id, $subtotal);

$availableVouchers = [];
if ($voucherStmt->execute()) {
    $voucherResult = $voucherStmt->get_result();
    while ($voucher = $voucherResult->fetch_assoc()) {
        $availableVouchers[] = $voucher;
    }
}
$voucherStmt->close();




$addressStmt = $conn->prepare("SELECT * FROM addresses WHERE user_id = ? ORDER BY label DESC");
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
?>



<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Payment - GoGrocery</title>
        
        <!-- Bootstrap Icons -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
        <!-- Custom CSS -->
        <link rel="stylesheet" href="../css/profile.css">
        <style>
            /* Payment Page Specific Styles */
            .payment-container {
                display: grid;
                grid-template-columns: 1fr 400px;
                gap: 30px;
            }
            
            @media (max-width: 992px) {
                .payment-container {
                    grid-template-columns: 1fr;
                }
            }
            
            .payment-items {
                background: white;
                border-radius: 12px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.08);
                padding: 25px;
                margin-bottom: 25px;
                border: 1px solid #eaeaea;
            }
            
            .payment-section-title {
                font-size: 20px;
                font-weight: 600;
                margin-bottom: 20px;
                color: #2c3e50;
                display: flex;
                align-items: center;
            }
            
            .payment-section-title i {
                margin-right: 10px;
                color: #4a6cf7;
            }
            
            .payment-item-card {
                display: flex;
                border-radius: 8px;
                margin-bottom: 15px;
                overflow: hidden;
                border: 1px solid #eaeaea;
                background: #fafafa;
            }
            
            .payment-item-image {
                flex: 0 0 80px;
                background: #ffffffff;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 10px;
            }
            
            .payment-item-image img {
                max-width: 100%;
                max-height: 60px;
                object-fit: contain;
            }
            
            .payment-item-details {
                flex: 1;
                padding: 15px;
                display: grid;
                grid-template-columns: 2fr 1fr 1fr 1fr;
                gap: 10px;
                align-items: center;
            }
            
            .payment-item-info {
                display: flex;
                flex-direction: column;
            }
            
            .payment-item-name {
                font-weight: 600;
                font-size: 16px;
                color: #2c3e50;
                margin-bottom: 4px;
            }
            
            .payment-item-sku {
                font-size: 12px;
                color: #6c757d;
            }
            
            .payment-item-price, .payment-item-quantity, .payment-item-discount, .payment-item-discount-total, .payment-item-total {
                font-size: 14px;
                text-align: center;
            }
            
            .payment-item-price {
                font-weight: 600;
            }
            
            .payment-item-discount, .payment-item-discount-total {
                color: #dc3545;
                font-weight: 500;
            }
            
            .payment-item-total {
                font-weight: 600;
                color: #2c3e50;
            }
            
            .payment-options {
                background: white;
                border-radius: 12px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.08);
                padding: 25px;
                margin-bottom: 25px;
                border: 1px solid #eaeaea;
            }
            
            .payment-form-group {
                margin-bottom: 20px;
            }
            
            .payment-form-label {
                display: block;
                margin-bottom: 8px;
                font-weight: 500;
                color: #555;
                font-size: 16px;
            }
            
            .payment-form-select {
                width: 100%;
                padding: 12px 15px;
                border: 1px solid #ddd;
                border-radius: 8px;
                font-size: 15px;
                transition: border 0.3s;
                background-color: white;
            }
            
            .payment-form-select:focus {
                border-color: #4a6cf7;
                outline: none;
            }
            
            .order-summary {
                background: white;
                border-radius: 12px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.08);
                padding: 25px;
                border: 1px solid #eaeaea;
                position: sticky;
                top: 20px;
            }
            
            .summary-item {
                display: flex;
                justify-content: space-between;
                padding: 10px 0;
                border-bottom: 1px solid #f1f1f1;
            }
            
            .summary-item:last-child {
                border-bottom: none;
            }
            
            .summary-label {
                font-size: 15px;
                color: #6c757d;
            }
            
            .summary-value {
                font-size: 15px;
                font-weight: 500;
                color: #2c3e50;
            }
            
            .summary-total {
                font-weight: 700;
                font-size: 18px;
                color: #2c3e50;
                margin-top: 10px;
                padding-top: 15px;
                border-top: 2px solid #eaeaea;
            }
            
            .discount-value {
                color: #dc3545;
            }
            
            .place-order-btn {
                background: #4a6cf7;
                color: white;
                border: none;
                padding: 15px;
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
            
            .place-order-btn:hover {
                background: #3048b4;
            }
            
            @media (max-width: 768px) {
                .payment-item-details {
                    grid-template-columns: 1fr;
                    text-align: left;
                    gap: 5px;
                }
                
                .payment-item-price, .payment-item-quantity, .payment-item-discount, 
                .payment-item-discount-total, .payment-item-total {
                    text-align: left;
                    display: flex;
                    justify-content: space-between;
                }
                
                .payment-item-price::before {
                    content: "Price: ";
                    font-weight: normal;
                }
                
                .payment-item-quantity::before {
                    content: "Qty: ";
                    font-weight: normal;
                }
                
                .payment-item-discount::before {
                    content: "Unit Discount: ";
                    font-weight: normal;
                }
                
                .payment-item-discount-total::before {
                    content: "Discount Total: ";
                    font-weight: normal;
                }
                
                .payment-item-total::before {
                    content: "Line Total: ";
                    font-weight: normal;
                }
            }
               .hidden { display: none; }
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
 
            <div id="profileContent">
                <div class="content-header">
                    <h1>Payment</h1>
                    <p>Review your order and complete your purchase</p>
                </div>
                
                <div class="content">
                    <form id="paymentForm" method="POST" action="process_payment.php">
                        <div class="payment-container">
                            <div class="payment-left">
                                <!-- Order Items Section -->
                                <div class="payment-items">
                                    <h2 class="payment-section-title"><i class="bi bi-cart-check"></i> Order Items</h2>
                                    
                                    <?php
                                    $subtotal = 0;
                                    $totalLineDiscount = 0;
                                    
                                    foreach ($cartList as $item): 
                                        $lineDiscountTotal = $item['line_discount'] * $item['quantity'];
                                        $lineTotal = ($item['unit_price'] * $item['quantity']) - $lineDiscountTotal;
                                        
                                        $subtotal += $lineTotal;
                                        $totalLineDiscount += $lineDiscountTotal;
                                        
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
                                                <div class="payment-item-name"><?php echo htmlspecialchars($item['product_name']); ?></div>
                                                <div class="payment-item-sku">SKU: <?php echo htmlspecialchars($item['sku']); ?></div>
                                                <div class="payment-item-sku">
                                                Unit discount: RM <?php echo number_format($item['line_discount'], 2); ?>
                                            </div>
                                            </div>
                                            
                                            <div class="payment-item-price">
                                                RM <?php echo number_format($item['unit_price'], 2); ?>
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
                                </div>

                                <!-- Delivery Address Section -->
                                <div class="payment-options">
                                    <h2 class="payment-section-title"><i class="bi bi-geo-alt-fill"></i> Delivery Address</h2>
                                    
                                    <div class="payment-form-group">
                                        <label class="payment-form-label" for="delivery_address">Select Delivery Address</label>
                                        <select class="payment-form-select" id="delivery_address" name="address_id" required>
                                            <option value="">-- Select Address --</option>
                                            <?php foreach ($addressList as $address): ?>
                                                <option value="<?php echo $address['address_id']; ?>">
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
                                    </div>
                                </div>

                                
                                <!-- Payment Options Section -->
                                <div class="payment-options">
                                    <h2 class="payment-section-title"><i class="bi bi-credit-card"></i> Payment Options</h2>
                                    
                                    <div class="payment-form-group">
                                        <label class="payment-form-label" for="voucher">Select Voucher</label>
                                        <select class="payment-form-select" id="voucher" name="voucher_id">
                                            <option value="">No voucher</option>
                                            <?php foreach ($availableVouchers as $voucher): ?>
                                                <option 
                                                    value="<?php echo $voucher['voucher_id']; ?>" 
                                                    data-type="<?php echo $voucher['discount_type']; ?>" 
                                                    data-value="<?php echo $voucher['discount_value']; ?>">
                                                    <?php echo htmlspecialchars($voucher['description']) . " - " . htmlspecialchars($voucher['code']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>

                                    </div>
                                    
                                    <div class="payment-form-group">
                                
                                            <label for="method">Select Payment Method2</label>
                                            <select id="method" name="payment_method" required>
                                                <option value="">-- Select --</option>
                                                <option value="card">Credit/Debit Card</option>
                                                <option value="bank_transfer">Bank Transfer (Manual)</option>
                                                <option value="cod">Cash on Delivery (COD)</option>
                                                <option value="e_wallet">E-Wallet</option>
                                                <option value="grabpay">GrabPay</option>
                                                <option value="fpx">FPX Online Banking</option>
                                            </select>

                                            <!-- CARD -->
                                            <div id="cardFields" class="hidden">
                                                <label>Card Number</label>
                                                <input type="text" id="card_number" name="card_number" maxlength="16">
                                            <div id="error_card" class="error"></div>

                                            <label>Expiry (MM/YY)</label>
                                            <input type="text" id="expiry" name="expiry" placeholder="MM/YY">
                                            <div id="error_expiry" class="error"></div>

                                            <label>CVV</label>
                                            <input type="password" id="cvv" name="cvv" maxlength="4">
                                            <div id="error_cvv" class="error"></div>

                                            <label>Name on Card</label>
                                            <input type="text" id="name" name="name">
                                            </div>

                                            <!-- BANK TRANSFER MANUAL -->
                                            <div id="bankFields" class="hidden">
                                            <p>Please transfer the amount to:</p>
                                            <p><strong>Bank:</strong> Maybank</p>
                                            <p><strong>Account No:</strong> 123456789</p>
                                            <p><strong>Account Name:</strong> MyShop Sdn Bhd</p>

                                            <label>Reference Number</label>
                                            <input type="text" id="reference" name="reference">

                                            <label>Upload Receipt (jpg/png/pdf)</label>
                                            <input type="file" id="receipt" name="receipt" accept=".jpg,.jpeg,.png,.pdf">
                                            <div id="error_receipt" class="error"></div>
                                            </div>

                                            <!-- FPX -->
                                            <div id="fpxFields" class="hidden">
                                            <label>Select Bank</label>
                                            <select id="bank_list" name="bank_list">
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

                                            </select>
                                        </div>
                                    </div>
                                </div>
                            
                                <!-- Order Summary Section -->
                                <div class="order-summary">
                                    <h2 class="payment-section-title"><i class="bi bi-receipt"></i> Order Summary</h2>
                                    
                                    <div class="summary-item">
                                        <span class="summary-label">Subtotal</span>
                                        <span class="summary-value">RM <?php echo number_format($subtotal, 2); ?></span>
                                    </div>
                                    
                                    <div class="summary-item">
                                        <span class="summary-label">Items Discount</span>
                                        <span class="summary-value discount-value">-RM <?php echo number_format($totalLineDiscount, 2); ?></span>
                                    </div>
                                    
                                    
                                    
                                    <div class="summary-item">
                                        <span class="summary-label">Voucher Discount</span>
                                        <span class="summary-value discount-value" id="voucherDiscountValue">-RM 0.00</span>
                                    </div>

                                    <!--------- ---->
                                    <div class="summary-item summary-total">
                                        <span class="summary-label">Total Discount</span>
                                        <span class="summary-value discount-value" id="voucherDiscountValue">- RM <?php echo number_format($totalLineDiscount + 5.00, 2); ?></span>
                                    </div>



                                    <div class="summary-item">
                                        <span class="summary-label">Shipping Fee</span>
                                        <span class="summary-value">RM 5.00</span>
                                    </div>
                                    
                                    <div class="summary-item summary-total">
                                        <span class="summary-label">Total</span>
                                        <span class="summary-value" id = "grand-total">RM <?php echo number_format($subtotal + 5.00, 2); ?></span>
                                    </div>
                                    

                                <button type="submit" class="place-order-btn">Confirm Payment</button>
                            
                            </div>
                        </div>
                   </form>
                </div>
            </div>
        </div>

        <script>
document.addEventListener("DOMContentLoaded", function() {
    const voucherSelect = document.getElementById("voucher");
    const voucherDiscountEl = document.getElementById("voucherDiscountValue");
    const totalEl = document.getElementById("grand-total");
    const subtotal = <?php echo json_encode($subtotal); ?>;
    const totalLineDiscount = <?php echo json_encode($totalLineDiscount); ?>;
    const shippingFee = 5.00;

    // Hidden inputs to pass to backend
    const form = document.getElementById("paymentForm");
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

        // Base amount is subtotal minus line discounts
        const baseAmount = subtotal - totalLineDiscount;

        if (option.value) {
            const type = option.getAttribute("data-type");
            const value = parseFloat(option.getAttribute("data-value"));

            if (type === "PERCENT") {
                discountValue = (baseAmount * value) / 100;
            } else if (type === "FIXED") {
                discountValue = Math.min(baseAmount, value); // don’t allow over-discount
            }
        }

        // Update hidden input
        hiddenVoucherDiscount.value = discountValue.toFixed(2);

        // Update UI
        voucherDiscountEl.textContent = "-RM " + discountValue.toFixed(2);

        const finalTotal = (baseAmount - discountValue) + shippingFee;
        totalEl.textContent = "RM " + finalTotal.toFixed(2);
    }

    // Trigger once on load in case default is selected
    updateTotals();

    // Update whenever voucher changes
    voucherSelect.addEventListener("change", updateTotals);
});





            const methodSelect = document.getElementById("method");
    const cardFields = document.getElementById("cardFields");
    const bankFields = document.getElementById("bankFields");
    const fpxFields = document.getElementById("fpxFields");
    const form = document.getElementById("paymentForm");

    // Toggle fields
    methodSelect.addEventListener("change", function() {
      cardFields.classList.add("hidden");
      bankFields.classList.add("hidden");
      fpxFields.classList.add("hidden");

      if (this.value === "card") cardFields.classList.remove("hidden");
      if (this.value === "bank_transfer") bankFields.classList.remove("hidden");
      if (this.value === "fpx") fpxFields.classList.remove("hidden");
    });

    // Validation
    form.addEventListener("submit", function(e) {
      let valid = true;

      if (methodSelect.value === "card") {
        const card = document.getElementById("card_number").value;
        if (!/^\d{16}$/.test(card)) {
          document.getElementById("error_card").textContent = "Card number must be 16 digits.";
          valid = false;
        } else document.getElementById("error_card").textContent = "";

        const expiry = document.getElementById("expiry").value;
        if (!/^(0[1-9]|1[0-2])\/\d{2}$/.test(expiry)) {
          document.getElementById("error_expiry").textContent = "Invalid expiry format (MM/YY).";
          valid = false;
        } else document.getElementById("error_expiry").textContent = "";

        const cvv = document.getElementById("cvv").value;
        if (!/^\d{3,4}$/.test(cvv)) {
          document.getElementById("error_cvv").textContent = "CVV must be 3 or 4 digits.";
          valid = false;
        } else document.getElementById("error_cvv").textContent = "";
      }

      if (methodSelect.value === "bank_transfer") {
        const receipt = document.getElementById("receipt").files.length;
        if (receipt === 0) {
          document.getElementById("error_receipt").textContent = "Please upload payment receipt.";
          valid = false;
        } else document.getElementById("error_receipt").textContent = "";
      }

      if (methodSelect.value === "fpx") {
        const bank = document.getElementById("bank_list").value;
        if (bank === "") {
          document.getElementById("error_bank").textContent = "Please select your bank.";
          valid = false;
        } else document.getElementById("error_bank").textContent = "";
      }

      if (!valid) e.preventDefault();
    });
        </script>
    </body>
</html>