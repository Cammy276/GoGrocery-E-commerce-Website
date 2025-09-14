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
        //if operation success, redirect to delivery address page
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
        //if operation success, redirect to delivery address page
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

        <!-- Inter font -->
        <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
        <!-- Bootstrap -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
        <!-- Custom CSS -->
        <link rel="stylesheet" href="../../css/profile_styles.css">
        <link rel="stylesheet" href="../../css/deliveryAddress_styles.css">
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
                    <li><a href=""><i class="bi bi-gear-fill"></i> Profile Settings</a></li>
                    <li><a href="../deliveryAddress/index.php" class="active"><i class="bi bi-geo-alt-fill"></i> Delivery Addresses</a></li>
                    <li><a href="../cart/index.php"><i class="bi bi-cart3"></i> Cart</a></li>
                    <li><a href="../order/index.php"><i class="bi bi-bag-fill"></i> Orders</a></li>
                    <li><a href="../history/index.php"><i class="bi bi-clock-history"></i> History</a></li>
                    <li><a href="../wishlist/index.php"><i class="bi bi-heart"></i> Wishlist</a></li>
                    <li><a href="../reward/index.php"><i class="bi bi-award-fill"></i> Rewards</a></li>
                    <li><a href="../../auth/logout.php"><i class="bi bi-box-arrow-right"></i> Log Out</a></li>
                </ul>
            </div>
            
            <!--- right content space -->
            <div id="profileContent">
                <div class="content-header">
                    <h1 style="text-align: left;">Edit delivery address details</h1>
                    <p>Edit existing delivery address details for future orders</p>
                </div>
                <div class="content">
                    <h2>Delivery Address Form</h2>
                    <br/>
                    
                    <div class="card">
                        <form id="addressForm" method='post'>

                            <input class="textInput" type='hidden' id='user_id' name='user_id' value="<?php echo $user_id ?>" />
                            <input class="textInput" type="hidden" name="address_id" value="<?php echo $address['address_id']; ?>">

                            <label>Label:</label>
                            <input class="textInput" type='text' id='label' name='label' 
                                value="<?php echo htmlspecialchars($address['label'] ?? ''); ?>"
                                placeholder='e.g. Home' />
                            <br/>
                            <div id="error_Label" class="error"></div>
                            <br/>

                            <input class="textInput" type="text" id="apartment" name="apartment" 
                                value="<?php echo htmlspecialchars($address['apartment'] ?? ''); ?>" 
                                placeholder="e.g. Lot No. 241, Level 2 Menara Petronas" />
                            <br/><br/>

                            <label>Street:</label>
                            <input class="textInput" type='text' id='street' name='street' 
                                value="<?php echo htmlspecialchars($address['street'] ?? ''); ?>"
                                placeholder="e.g. Kuala Lumpur City Centre" />
                            <br/>
                            <div id="error_Street" class="error"></div>
                            <br/>

                            <label>Postcode:</label>
                            <input class="textInput" type='text' id='postcode' name='postcode' 
                                value="<?php echo htmlspecialchars($address['postcode'] ?? ''); ?>"
                                placeholder="e.g. 50088" />
                            <br/>
                            <div id="error_Postcode" class="error"></div>
                            <br/>

                            <label>City:</label>
                            <input class="textInput" type='text' id='city' name='city' 
                                value="<?php echo htmlspecialchars($address['city'] ?? ''); ?>"
                                placeholder="e.g. Kuala Lumpur" />
                            <br/>
                            <div id="error_City" class="error"></div>
                            <br/>

                            <label>State/Territory:</label>
                            <select name="state_territory" >
                                <option value="">-- Select State / Territory --</option>
                                <?php foreach($enum_values as $state): ?>
                                    <option value="<?= htmlspecialchars($state) ?>"
                                        <?= ($state === $address['state_territory']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($state) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <br/>
                            <div id="error_State_Territory" class="error"></div>
                            <br/>

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

        <script>
            // to validate the input
            const form = document.getElementById("addressForm");
            form.addEventListener("submit", function(e) {

                const clickedButton = e.submitter;
                if (clickedButton && clickedButton.name == "update") {
                    let valid = true;
                    
                    const label = document.getElementById("label").value.trim();
                    const street = document.getElementById("street").value.trim();
                    const postcode = document.getElementById("postcode").value.trim();
                    const city = document.getElementById("city").value.trim();
                    const state_territory = document.querySelector("select[name='state_territory']").value;


                    const labelInput = document.getElementById("label");
                    const streetInput = document.getElementById("street");
                    const postcodeInput = document.getElementById("postcode");
                    const cityInput = document.getElementById("city");
                    const state_territoryInput = document.querySelector("select[name='state_territory']");

                    document.getElementById("error_Label").textContent = "";
                    document.getElementById("error_Street").textContent = "";
                    document.getElementById("error_Postcode").textContent = "";
                    document.getElementById("error_City").textContent = "";
                    document.getElementById("error_State_Territory").textContent = "";

                    labelInput.classList.remove("input-error");
                    streetInput.classList.remove("input-error");
                    postcodeInput.classList.remove("input-error");
                    cityInput.classList.remove("input-error");
                    state_territoryInput.classList.remove("input-error");

                    
                    if (!/^[\w\s]{1,50}$/.test(label)) { 
                        document.getElementById("error_Label").textContent = "Please enter a label. Maximum 50 characters.";
                        labelInput.classList.add("input-error")
                        valid = false; 
                    }
                    if (!/^[\w\s.,-]{1,100}$/.test(street)) { 
                        document.getElementById("error_Street").textContent = "Please enter a valid street. Maximum 100 characters.";
                        streetInput.classList.add("input-error")
                        valid = false; 
                    }
                    if (!/^\d{5}$/.test(postcode)) { 
                        document.getElementById("error_Postcode").textContent = "Please enter a valid postcode. Postcode must be exactly 5 digits.";
                        postcodeInput.classList.add("input-error")
                        valid = false; 
                    }
                    if (!/^[A-Za-z\s-]{1,50}$/.test(city)) { 
                        document.getElementById("error_City").textContent = "Please enter a valid city. City must be 6–12 letters and space only.";
                        cityInput.classList.add("input-error")
                        valid = false; 
                    }
                    if (!state_territory) { 
                        document.getElementById("error_State_Territory").textContent = "Please select a state or territory.";
                        state_territoryInput.classList.add("input-error")
                        valid = false; 
                    }

                    if (!valid) {
                        e.preventDefault();

                        if (labelInput.classList.contains("input-error")) {
                            labelInput.focus();
                        } else if (streetInput.classList.contains("input-error")) {
                            streetInput.focus();
                        } else if (postcodeInput.classList.contains("input-error")) {
                            postcodeInput.focus();
                        } else if (cityInput.classList.contains("input-error")) {
                            cityInput.focus();
                        } else if (state_territoryInput.classList.contains("input-error")) {
                            state_territoryInput.focus();
                        }

                    }

            }});

        </script>
        <footer><?php include("../../footer.php") ?> </footer>
    </body>
</html>