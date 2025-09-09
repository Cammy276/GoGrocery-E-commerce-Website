<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Order Page</title>
        
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
                    <li><a href="../deliveryAddress/index.php"><i class="bi bi-geo-alt-fill"></i> Delivery Addresses</a></li>
                    <li><a href="../cart/index.php"><i class="bi bi-cart3"></i> Cart</a></li>
                    <li><a href="../order/index.php" class="active"><i class="bi bi-bag-fill"></i> Orders</a></li>
                    <li><a href="../history/index.php"><i class="bi bi-clock-history"></i> History</a></li>
                    <li><a href=""><i class="bi bi-heart"></i> Wishlist</a></li>
                    <li><a href=""><i class="bi bi-award-fill"></i> Rewards</a></li>
                     <li><a href="../../auth/logout.php"><i class="bi bi-box-arrow-right"></i> Log Out</a></li>
                </ul>
            </div>
            
            <!--- right content space -->
            <div id="profileContent">
                <div class="content-header">
                    <h1>Orders</h1>
                    <p>Review the orders that are still pending delivery</p>
                </div>
                <div class="content">
                   <h2>Order List</h2>
                   <p class="tips">No order record found<p>
                </div>
            </div>
        </div>

    </body>
</html>