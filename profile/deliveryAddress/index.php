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

// Fetch all addresses
$stmt = $conn->prepare("SELECT * FROM addresses WHERE user_id = ? ORDER BY label ASC");
$stmt->bind_param("i", $user_id); 

if ($stmt->execute()) {
    $result = $stmt->get_result();

    $addressList = [];
    while ($row = $result->fetch_assoc()) {
        $addressList[] = $row;
    }
} else {
    $errorMsg = $stmt->error;
}


$stmt->close();

?>


<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Delivery Address</title>
    
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
                    <li><a href="../deliveryAddress/index.php" class="active"><i class="bi bi-geo-alt-fill"></i> Delivery Addresses</a></li>
                    <li><a href="../cart/index.php"><i class="bi bi-cart3"></i> Cart</a></li>
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
                    <h1>Delivery Address</h1>
                    <p>Set or update your default delivery address for future orders</p>
                </div>
                <div class="content">
                    <div class="delivery-header">
                        <h2>Delivery Address List</h2>
                        <a href="addForm.php"><i class="bi bi-plus-circle-fill"></i></a>
                    </div>

                    <br/>
                    <!--- get message from addForm.php & addressDetails.php-->
                    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'addSuccess'): ?>
                        <p class="successMessage">Address added successfully!</p>
                    <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'updateSuccess'): ?>
                        <p class="successMessage">Address updated successfully!</p>
                    <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'deleteSuccess'): ?>
                        <p class="successMessage">Address deleted successfully!</p>
                    <?php endif; ?>

                    <!--- shows message if error or no record -->
                    <?php if (!empty($errorMsg)): ?>
                        <p class="errMessage">Error occurred when fetching addresses. Please try again.</p>
                        <pre class="errMessage"><?php echo htmlspecialchars($errorMsg); ?></pre>
                    <?php elseif (count($addressList) === 0): ?>
                        <p class="tips">No delivery address record found</p>
                    <?php endif; ?>

                    <?php foreach($addressList as $address): ?>
                        <a href="addressDetails.php?id=<?php echo $address['address_id']; ?>" class="cardLink">
                            <div class="card">
                                <p class="addressLabel"><?php echo $address['label'] ?></p>
                                
                                <p class="addressDetails">
                                    <?php
                                        $parts = [];
                                        if (!empty($address['apartment'])) {
                                            $parts[] = $address['apartment'];
                                        }
                                        $parts[] = $address['street'];
                                        $parts[] = $address['postcode'];
                                        $parts[] = $address['city'];
                                        $parts[] = $address['state_territory'];

                                        echo implode(", ", $parts);
                                    ?>
                                </p>                   
                            </div>
                        </a>
                    <?php endforeach; ?>
                    
                </div>
            </div>
        </div>

    </body>
</html>