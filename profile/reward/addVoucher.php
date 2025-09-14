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


// Handle Insert
if (isset($_POST['insert'])) {

    $user_id = intval($_POST['user_id']);
    $voucher_name = trim($_POST['voucher_name']);

    // Step 1: Check if voucher exists
    $checkVoucher = $conn->prepare("SELECT voucher_id, all_user_limit FROM vouchers WHERE voucher_name = ?");
    $checkVoucher->bind_param("s", $voucher_name);
    $checkVoucher->execute();
    $voucherResult = $checkVoucher->get_result();

    if ($voucherResult->num_rows === 0) {
        $errorMsg = "Voucher code does not exist.";
    } else {
        $voucherRow = $voucherResult->fetch_assoc();
        $voucher_id = $voucherRow['voucher_id'];
        $all_user_limit = $voucherRow['all_user_limit'];

        // Step 2: Check if user already has this voucher
        $checkUserVoucher = $conn->prepare("SELECT * FROM user_vouchers WHERE user_id = ? AND voucher_id = ?");
        $checkUserVoucher->bind_param("ii", $user_id, $voucher_id);
        $checkUserVoucher->execute();
        $userVoucherResult = $checkUserVoucher->get_result();

        if ($userVoucherResult->num_rows > 0) {
            $errorMsg = "You have already claimed this voucher.";
        } else {
            // Step 3: Check if voucher still available
            if ($all_user_limit !== null && $all_user_limit <= 0) {
                $errorMsg = "Oops, the voucher code is already out of stock.";
            } else {
                // Step 4: Insert into user_vouchers
                $insertUV = $conn->prepare("INSERT INTO user_vouchers (user_id, voucher_id, isUsed) VALUES (?, ?, 0)");
                $insertUV->bind_param("ii", $user_id, $voucher_id);

                if ($insertUV->execute()) {
                    // Step 5: Decrease all_user_limit (if not NULL)
                    if ($all_user_limit !== null) {
                        $updateVoucher = $conn->prepare("UPDATE vouchers SET all_user_limit = all_user_limit - 1 WHERE voucher_id = ?");
                        $updateVoucher->bind_param("i", $voucher_id);
                        $updateVoucher->execute();
                        $updateVoucher->close();
                    }

                    // Redirect after success
                    header("Location: index.php?msg=addSuccess");
                    exit();
                } else {
                    $errorMsg = "Error adding voucher: " . $insertUV->error;
                }

                $insertUV->close();
            }
        }
        $checkUserVoucher->close();
    }
    $checkVoucher->close();
}



?>



<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Redeem Rewards</title>
        
        <!-- Inter font -->
        <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
        <!-- Bootstrap -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
        <!-- Custom CSS -->
        <link rel="stylesheet" href="../../css/profile_styles.css">
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
                    <h1 style="text-align: left;">Redeem Rewards</h1>
                    <p>Use your special voucher code to redeem exciting rewards.</p>
                </div>
                <div class="content">
                    <h2>Redeem Here</h2>
                    <br/>
                    
                    <div class="card">
                        <form id="redeemForm" method='post'>

                            <input class="textInput" type='hidden' id='user_id' name='user_id' value="<?php echo $user_id ?>" />
                            
                            <label>Voucher code:</label>
                            <input class="textInput" type='text' id='voucher_name' name='voucher_name' placeholder="Try any secret code you have discovered..." />
                            <br/>
                            <div id="error_voucher" class="error"></div>
                            <br/>

                            <p class="tips">No code yet? Keep your eyes open, hidden surprises might be waiting for you.</p>
                            
                            <?php if($errorMsg != null): ?>
                                <p class="errMessage">Add new reward fail. Please try again.</p>
                                <pre class="errMessage"><?php echo htmlspecialchars($errorMsg)?> </pre>
                            <?php endif; ?>

                            
                            <div class="buttonContainer">
                                <!-- save button -->
                                <input class="saveButton" type='submit' name='insert' value='Redeem Voucher' />
                            </div>
                        </form>
                    </div>
                
                </div>
            </div>
        </div>


        <script>
            // to validate the input
            const form = document.getElementById("redeemForm");
            form.addEventListener("submit", function(e) {
                let valid = true;
                
                const voucher = document.getElementById("voucher_name").value.trim();
                const voucherInput = document.getElementById("voucher_name");
                document.getElementById("error_voucher").textContent = "";
                
                
                if (voucher === "") { 
                    document.getElementById("error_voucher").textContent = "Please enter a voucher code.";
                    voucherInput.classList.add("input-error")
                    valid = false; 
                }

                if (!valid) {
                    e.preventDefault();
                }

            });

        </script>
        <footer><?php include("../../footer.php") ?> </footer>
    </body>
</html>