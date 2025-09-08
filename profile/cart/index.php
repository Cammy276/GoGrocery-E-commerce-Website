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
                
                .tips {
                    color: grey;
                    padding: 12px 15px;
                }
                
                @media (max-width: 900px) {
                    .main-container {
                        flex-direction: column;
                    }
                    
                    #profileSettingSideBar {
                        flex: 0 0 auto;
                        width: 100%;
                    }
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
                   <p class="tips">No item in cart<p>
                </div>
            </div>
        </div>

    </body>
</html>