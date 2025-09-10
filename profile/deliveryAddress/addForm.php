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

// Fetch ENUM values for state_territory dynamically
$enum_values = [];
$enum_result = $conn->query("SHOW COLUMNS FROM addresses LIKE 'state_territory'");
if ($enum_result) {
    $row = $enum_result->fetch_assoc();
    preg_match("/^enum\('(.*)'\)$/", $row['Type'], $matches);
    if (isset($matches[1])) $enum_values = explode("','", $matches[1]);
}

// Handle Insert
if (isset($_POST['insert'])) {

    $user_id = $_POST['user_id'];
    $label = $_POST['label'];
    $street = $_POST['street'];
    $apartment = $_POST['apartment'];
    $postcode = $_POST['postcode'];
    $city = $_POST['city'];
    $state_territory = $_POST['state_territory'];

    $stmt = $conn->prepare("INSERT INTO addresses (user_id, label, street, apartment, postcode, city, state_territory) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssss", $user_id, $label, $street, $apartment, $postcode, $city, $state_territory);
    
    
    if ($stmt->execute()) {
        //if operation success, redirect to addressDetails page
        header("Location: index.php?msg=addSuccess");
        exit();
    } else {
        //store error message in variable
        $errorMsg = $stmt->error;
    }



    $stmt->close();

}


?>



<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Add New Delivery Address</title>
        
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
                    <li><a href=""><i class="bi bi-gear-fill"></i> Profile Settings</a></li>
                    <li><a href="../deliveryAddress/index.php" class="active"><i class="bi bi-geo-alt-fill"></i> Delivery Addresses</a></li>
                    <li><a href="../cart/index.php"><i class="bi bi-cart3"></i> Cart</a></li>
                    <li><a href="../order/index.php"><i class="bi bi-bag-fill"></i> Orders</a></li>
                    <li><a href="../history/index.php"><i class="bi bi-clock-history"></i> History</a></li>
                    <li><a href=""><i class="bi bi-heart"></i> Wishlist</a></li>
                    <li><a href=""><i class="bi bi-award-fill"></i> Rewards</a></li>
                    <li><a href=""><i class="bi bi-box-arrow-right"></i> Log Out</a></li>
                </ul>
            </div>
            
            <!--- right content space -->
            <div id="profileContent">
                <div class="content-header">
                    <h1>Add New Delivery Address</h1>
                    <p>Add a new delivery address for future orders</p>
                </div>
                <div class="content">
                    <h2>Delivery Address Form</h2>
                    <br/>
                    
                    <div class="card">
                        <form method='post'>

                            <input class="textInput" type='hidden' id='user_id' name='user_id' value="<?php echo $user_id ?>" />
                            
                            <label>Label:</label>
                            <input class="textInput" type='text' id='label' name='label' placeholder="e.g. Home" required/>
                            <br/><br/>

                            <label>Apartment/Unit (Optional):</label>
                            <input class="textInput" type='text' id='apartment' name='apartment' placeholder="e.g. Lot No. 241, Level 2 Menara Petronas"/>
                            <br/><br/>

                            <label>Street:</label>
                            <input class="textInput" type='text' id='street' name='street' placeholder="e.g. Kuala Lumpur City Centre" required/>
                            <br/><br/>

                            <label>Postcode:</label>
                            <input class="textInput" type='text' id='postcode' name='postcode' placeholder="e.g. 50088" required/>
                            <br/><br/>

                            <label>City:</label>
                            <input class="textInput" type='text' id='city' name='city' placeholder="e.g. Kuala Lumpur" required/>
                            <br/><br/>

                            <label>State/Territory:</label>
                            <select name="state_territory" required>
                                <?php foreach($enum_values as $state): ?>
                                    <option value="<?= $state ?>"><?= $state ?></option>
                                <?php endforeach; ?>
                            </select>
                            <br/><br/>

                            <label>Country:</label>
                            <input class="textInput" type='text' id='state' name='state' value="Malaysia" disabled/>
                            <br/>
                            <p class="tips">GoGrocery's delivery service is currently available for Malaysia only. Thanks for your understanding!</p>
                            <br/>

                            
                            <?php if($errorMsg != null): ?>
                                <p class="errMessage">Add new address fail. Please try again.</p>
                                <pre class="errMessage"><?php echo htmlspecialchars($errorMsg)?> </pre>
                            <?php endif; ?>

                            
                            <div class="buttonContainer">
                                <!-- save button -->
                                <input class="saveButton" type='submit' name='insert' value='Save' />
                            </div>
                        </form>
                    </div>
                
                </div>
            </div>
        </div>

    </body>
</html>