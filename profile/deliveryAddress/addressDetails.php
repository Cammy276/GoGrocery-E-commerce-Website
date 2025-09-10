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

<!-- to get address_id passed in -->
 <?php
if (isset($_GET['id'])) {
    $address_id = intval($_GET['id']); // sanitize input
} else {
    die("Fail to get address_id from delivery address page.");
}

?>

<?php

include(__DIR__ . '/../../connect_db.php');

// Predefine variable for error message
$errorMsg = null;

//to get the address details by address id
$getAddInfoStmt = $conn->prepare("SELECT * FROM addresses WHERE address_id = ?");
$getAddInfoStmt->bind_param("i", $address_id);

if ($getAddInfoStmt->execute()){
    $addInfoResult = $getAddInfoStmt->get_result();

    if ($addInfoResult->num_rows === 0) {
        die("Delivery address not found.");
    }

    $address = $addInfoResult->fetch_assoc();

}
$getAddInfoStmt->close();


// Fetch ENUM values for state_territory dynamically
$enum_values = [];
$enum_result = $conn->query("SHOW COLUMNS FROM addresses LIKE 'state_territory'");
if ($enum_result) {
    $row = $enum_result->fetch_assoc();
    preg_match("/^enum\('(.*)'\)$/", $row['Type'], $matches);
    if (isset($matches[1])) $enum_values = explode("','", $matches[1]);
}


// Handle Update
if (isset($_POST['update'])) {
    $address_id = $_POST['address_id'];
    $label = $_POST['label'];
    $street = $_POST['street'];
    $apartment = $_POST['apartment'];
    $postcode = $_POST['postcode'];
    $city = $_POST['city'];
    $state_territory = $_POST['state_territory'];

    $stmt = $conn->prepare("UPDATE addresses SET label=?, street=?, apartment=?, postcode=?, city=?, state_territory=? WHERE address_id=?");
    $stmt->bind_param("ssssssi", $label, $street, $apartment, $postcode, $city, $state_territory, $address_id);
    if ($stmt->execute()) {
        //if operation success, redirect to addressDetails page
        header("Location: index.php?msg=updateSuccess");
        exit();
    } else {
        //store error message in variable
        $errorMsg = $stmt->error;
    }
    $stmt->close();
}


// Handle Delete
if (isset($_POST['delete'])) {
    $address_id = $_POST['address_id'];
    $stmt = $conn->prepare("DELETE FROM addresses WHERE address_id=?");
    $stmt->bind_param("i", $address_id);
    if ($stmt->execute()) {
        //if operation success, redirect to addressDetails page
        header("Location: index.php?msg=deleteSuccess");
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
        <title>Delivery Address Details</title>

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
                    <h1>Edit delivery address details</h1>
                    <p>Edit existing delivery address details for future orders</p>
                </div>
                <div class="content">
                    <h2>Delivery Address Form</h2>
                    <br/>
                    
                    <div class="card">
                        <form method='post'>

                            <input class="textInput" type='hidden' id='user_id' name='user_id' value="<?php echo $user_id ?>" />
                            <input class="textInput" type="hidden" name="address_id" value="<?php echo $address['address_id']; ?>">

                            <label>Label:</label>
                            <input class="textInput" type='text' id='label' name='label' 
                                value="<?php echo htmlspecialchars($address['label'] ?? ''); ?>"
                                placeholder='e.g. Home' required/>
                            <br/><br/>

                            <input class="textInput" type="text" id="apartment" name="apartment" 
                                value="<?php echo htmlspecialchars($address['apartment'] ?? ''); ?>" 
                                placeholder="e.g. Lot No. 241, Level 2 Menara Petronas" />
                            <br/><br/>

                            <label>Street:</label>
                            <input class="textInput" type='text' id='street' name='street' 
                                value="<?php echo htmlspecialchars($address['street'] ?? ''); ?>"
                                placeholder="e.g. Kuala Lumpur City Centre" required/>
                            <br/><br/>

                            <label>Postcode:</label>
                            <input class="textInput" type='text' id='postcode' name='postcode' 
                                value="<?php echo htmlspecialchars($address['postcode'] ?? ''); ?>"
                                placeholder="e.g. 50088" required/>
                            <br/><br/>

                            <label>City:</label>
                            <input class="textInput" type='text' id='city' name='city' 
                                value="<?php echo htmlspecialchars($address['city'] ?? ''); ?>"
                                placeholder="e.g. Kuala Lumpur" required/>
                            <br/><br/>

                            <label>State/Territory:</label>
                            <select name="state_territory" required>
                                <?php foreach($enum_values as $state): ?>
                                    <option value="<?= htmlspecialchars($state) ?>"
                                        <?= ($state === $address['state_territory']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($state) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <br/><br/>

                            <label>Country:</label>
                            <input class="textInput" type='text' id='state' name='state' value="Malaysia" disabled/>
                            <br/>
                            <p class="tips">GoGrocery's delivery service is currently available for Malaysia only. Thanks for your understanding!</p>
                            <br/>

                            
                            <?php if($errorMsg != null): ?>
                                <p class="errMessage">Save updated delivery address details fail. Please try again.</p>
                                <pre class="errMessage"><?php echo htmlspecialchars($errorMsg)?> </pre>
                            <?php endif; ?>

                            
                            <div class="buttonContainer">
                                <!-- save button -->
                                <input class="saveButton button" type='submit' name='update' value='Save' />
                                <!-- delete button -->
                                <input class="deleteButton button" type='submit' name='delete' value='Delete' />
                            </div>
                        </form>
                    </div>

                            
                
                </div>
            </div>
        </div>

    </body>
</html>