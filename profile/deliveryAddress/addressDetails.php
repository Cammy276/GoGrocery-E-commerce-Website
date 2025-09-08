<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Order Page</title>
        
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                }
                
                body {
                    background-color: #f8f9fa;
                    color: #333;
                    line-height: 1.6;
                }
                
                #header {
                    background-color: #fff;
                    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                    padding: 15px 30px;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }
                
                .logo {
                    font-size: 24px;
                    font-weight: bold;
                    color: #4a6cf7;
                }
                
                .nav-links {
                    display: flex;
                    gap: 25px;
                }
                
                .nav-links a {
                    text-decoration: none;
                    color: #555;
                    font-weight: 500;
                    transition: color 0.3s;
                }
                
                .nav-links a:hover {
                    color: #4a6cf7;
                }
                
                .main-container {
                    display: flex;
                    max-width: 1200px;
                    margin: 30px auto;
                    gap: 30px;
                }
                
                #profileSettingSideBar {
                    flex: 0 0 280px;
                    background-color: #fff;
                    border-radius: 10px;
                    box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
                    padding: 25px 0;
                }
                
                .profile-header {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    padding: 0 20px 20px;
                    border-bottom: 1px solid #eee;
                    margin-bottom: 15px;
                }
                
                .profile-avatar {
                    width: 80px;
                    height: 80px;
                    border-radius: 50%;
                    background-color: #e6e9ff;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin-bottom: 15px;
                    color: #4a6cf7;
                    font-size: 30px;
                    font-weight: bold;
                }
                
                .profile-name {
                    font-weight: 600;
                    font-size: 18px;
                    margin-bottom: 5px;
                }
                
                .profile-email {
                    color: #777;
                    font-size: 14px;
                }
                
                .menu-items {
                    list-style: none;
                    padding: 0 15px;
                }
                
                .menu-items li {
                    margin-bottom: 5px;
                }
                
                .menu-items a {
                    display: flex;
                    align-items: center;
                    padding: 12px 15px;
                    text-decoration: none;
                    color: #555;
                    border-radius: 8px;
                    transition: all 0.3s;
                }
                
                .menu-items a:hover, .menu-items a.active {
                    background-color: #f0f3ff;
                    color: #4a6cf7;
                }
                
                .menu-items a i {
                    margin-right: 12px;
                    font-size: 18px;
                }
                
                #profileContent {
                    flex: 1;
                    background-color: #fff;
                    border-radius: 10px;
                    box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
                    padding: 30px;
                }
                
                .content-header {
                    margin-bottom: 25px;
                }
                
                .content-header h1 {
                    font-size: 24px;
                    color: #333;
                    margin-bottom: 10px;
                }
                
                .content-header p {
                    color: #777;
                }
                
                .settings-card {
                    background-color: #f9fafc;
                    border-radius: 10px;
                    padding: 25px;
                    margin-bottom: 25px;
                    border: 1px solid #eee;
                }
                
                .settings-card h2 {
                    font-size: 18px;
                    margin-bottom: 20px;
                    color: #444;
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                }
                
                .settings-card h2 span {
                    font-size: 14px;
                    color: #4a6cf7;
                    cursor: pointer;
                }

                
                
                form label {
                    display: block;
                    margin-bottom: 8px;
                    font-weight: 500;
                    color: #555;
                }
                .textInput{
                    width: 100%;
                    padding: 12px 15px;
                    border: 1px solid #ddd;
                    border-radius: 8px;
                    font-size: 15px;
                    transition: border 0.3s;
                }
                .textInput:focus {
                    border-color: #4a6cf7;
                    outline: none;
                    border-width: 3px;
                }
                .button {
                    color: white;
                    border: none;
                    padding: 12px 25px;
                    border-radius: 8px;
                    font-weight: 500;
                    cursor: pointer;
                }
                .saveButton {
                    background-color: #4a6cf7;
                    transition: background-color 0.3s;
                }
                .saveButton:hover {
                    background-color: #3048b4ff;
                }
                .deleteButton {
                    background-color: #f74a4aff;
                    transition: background-color 0.3s;
                }
                .deleteButton:hover {
                    background-color: #b43030ff;
                }
                
                @media (max-width: 900px) {
                    .main-container {
                        flex-direction: column;
                    }
                    
                    #profileSettingSideBar {
                        flex: 0 0 auto;
                        width: 100%;
                    }
                    
                    .two-columns {
                        flex-direction: column;
                        gap: 0;
                    }
                }
                hr {
                    color: grey;
                }
                
                @media (max-width: 768px) {
                    .nav-links {
                        display: none;
                    }
                    
                    #header {
                        padding: 15px 20px;
                    }
                    
                    .main-container {
                        margin: 20px;
                        gap: 20px;
                    }
                }
            </style>
            <!-- Bootstrap Icons -->
            <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
            <!-- Custom CSS -->
            <link rel="stylesheet" href="./css/styles.css">
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
                    
                    <div class="settings-card">
                        <form method='post'>
                            <label>Receiver Name:</label>
                            <input class="textInput" type='text' id='receiverName' name='receiverName' />
                            <br/><br/>

                            <label>Address line 1:</label>
                            <input class="textInput" type='text' id='addLine1' name='addLine1' />
                            <br/><br/>

                            <label>Address line 2:</label>
                            <input class="textInput" type='text' id='addLine2' name='addLin2' />
                            <br/><br/>

                            
                            <label>Postal Code:</label>
                            <input class="textInput" type='text' id='postalCode' name='postalCode' />
                            <br/><br/>

                            <label>City:</label>
                            <input class="textInput" type='text' id='city' name='city' />
                            <br/><br/>

                            <label>State:</label>
                            <input class="textInput" type='text' id='state' name='state' />
                            <br/><br/>

                            <label>Country:</label>
                            <input class="textInput" type='text' id='country' name='country' />
                            <br/><br/>

                            <!-- submit button -->
                            <input class="saveButton button" type='submit' name='submit' value='Save' />
                            <!-- delete button -->
                            <input class="deleteButton button" type='submit' name='submit' value='Delete' />
                        </form>
                    </div>
                
                </div>
            </div>
        </div>

    </body>
</html>