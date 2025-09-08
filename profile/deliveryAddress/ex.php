<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings</title>
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
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #555;
        }
        
        .form-group input, .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
            transition: border 0.3s;
        }
        
        .form-group input:focus, .form-group select:focus {
            border-color: #4a6cf7;
            outline: none;
        }
        
        .two-columns {
            display: flex;
            gap: 20px;
        }
        
        .two-columns .form-group {
            flex: 1;
        }
        
        .save-btn {
            background-color: #4a6cf7;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .save-btn:hover {
            background-color: #3b5be3;
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div id="header">
        <div class="logo">ShopEase</div>
        <div class="nav-links">
            <a href="#">Home</a>
            <a href="#">About</a>
            <a href="#">Help Center</a>
            <a href="#">Best Seller</a>
            <a href="#">Special Deal</a>
            <a href="#">New Product</a>
        </div>
    </div>

    <div class="main-container">
        <div id="profileSettingSideBar">
            <div class="profile-header">
                <div class="profile-avatar">JD</div>
                <div class="profile-name">John Doe</div>
                <div class="profile-email">john.doe@example.com</div>
            </div>
            
            <ul class="menu-items">
                <li><a href="#" class="active"><i class="fas fa-user-cog"></i> Profile Settings</a></li>
                <li><a href="#"><i class="fas fa-truck"></i> Delivery</a></li>
                <li><a href="#"><i class="fas fa-map-marker-alt"></i> Addresses</a></li>
                <li><a href="#"><i class="fas fa-shopping-cart"></i> Cart</a></li>
                <li><a href="#"><i class="fas fa-shopping-bag"></i> Orders</a></li>
                <li><a href="#"><i class="fas fa-history"></i> History</a></li>
                <li><a href="#"><i class="fas fa-heart"></i> Wishlist</a></li>
                <li><a href="#"><i class="fas fa-award"></i> Rewards</a></li>
                <li><a href="#"><i class="fas fa-sign-out-alt"></i> Log Out</a></li>
            </ul>
        </div>
        
        <div id="profileContent">
            <div class="content-header">
                <h1>Profile Settings</h1>
                <p>Manage and protect your account</p>
            </div>
            
            <div class="settings-card">
                <h2>Personal Information <span>Edit</span></h2>
                
                <div class="two-columns">
                    <div class="form-group">
                        <label for="firstName">First Name</label>
                        <input type="text" id="firstName" value="John">
                    </div>
                    
                    <div class="form-group">
                        <label for="lastName">Last Name</label>
                        <input type="text" id="lastName" value="Doe">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" value="john.doe@example.com">
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" value="+1 (555) 123-4567">
                </div>
                
                <button class="save-btn">Save Changes</button>
            </div>
            
            <div class="settings-card">
                <h2>Password & Security <span>Edit</span></h2>
                
                <div class="form-group">
                    <label for="currentPassword">Current Password</label>
                    <input type="password" id="currentPassword" value="········">
                </div>
                
                <div class="two-columns">
                    <div class="form-group">
                        <label for="newPassword">New Password</label>
                        <input type="password" id="newPassword" value="········">
                    </div>
                    
                    <div class="form-group">
                        <label for="confirmPassword">Confirm Password</label>
                        <input type="password" id="confirmPassword" value="········">
                    </div>
                </div>
                
                <button class="save-btn">Update Password</button>
            </div>
        </div>
    </div>

    <script>
        // Simple JavaScript to handle the edit functionality
        document.querySelectorAll('.settings-card h2 span').forEach(editSpan => {
            editSpan.addEventListener('click', function() {
                const card = this.closest('.settings-card');
                const inputs = card.querySelectorAll('input');
                
                inputs.forEach(input => {
                    if (input.hasAttribute('readonly')) {
                        input.removeAttribute('readonly');
                        input.style.backgroundColor = '#fff';
                    } else {
                        input.setAttribute('readonly', true);
                        input.style.backgroundColor = '#f5f5f5';
                    }
                });
                
                if (this.textContent === 'Edit') {
                    this.textContent = 'Cancel';
                } else {
                    this.textContent = 'Edit';
                }
            });
        });
    </script>
</body>
</html>