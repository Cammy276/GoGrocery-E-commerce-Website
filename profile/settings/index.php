<?php
session_start();
include(__DIR__ . '/../../connect_db.php');

// Base image folder
$baseUrl = "/GoGrocery-E-commerce-Website/images/users/";

// Defaults for guest
$name = "Guest";
$email = "-";
$phone = "-";
$profileImage = $baseUrl . "default.png";
$isGuest = true;

// If logged in, fetch user info
if (isset($_SESSION['user_id'])) {
  $user_id = $_SESSION['user_id'];
  $stmt = $conn->prepare("SELECT name, email, phone, profile_image_url FROM users WHERE user_id = ?");
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $stmt->bind_result($dbName, $dbEmail, $dbPhone, $dbProfileImage);
  if ($stmt->fetch()) {
    $name = $dbName;
    $email = $dbEmail;
    $phone = $dbPhone;
    $profileImage = $dbProfileImage ?: $baseUrl . "default.png";
    $isGuest = false;
  }
  $stmt->close();
  }
?>
<?php
    include __DIR__ . '../../livechat/chat_UI.php';
?>
<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Profile Settings</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
        <!-- Custom CSS -->
        <link rel="stylesheet" href="../../css/profile_styles.css">
        <link rel="stylesheet" href="../../css/cart_styles.css">
        <link rel="stylesheet" href="../../css/profileSetting_styles.css">
        <link rel="stylesheet" href="../../css/orders_history_styles.css">
        <link rel="stylesheet" href="../../css/deliveryAddress_styles.css">
        <link rel="stylesheet" href="../../css/wishlist_styles.css">
        <link rel="stylesheet" href="../../css/styles.css">
        <link rel="stylesheet" href="../../css/reward_styles.css">
        <link rel="stylesheet" href="../../css/header_styles.css">
        <link rel="stylesheet" href="../../css/footer_styles.css">
    </head>
    <body class="p-4">

        <header><?php include("../../header.php") ?></header>

        <div class="main-container">

            <!-- left side bar setting -->
            <div id="profileSettingSideBar">  
                <ul class="menu-items">
                    <!-- use Bootstrap icons-->
                    <li><a href="../settings/index.php" class="active"><i class="bi bi-gear-fill"></i> Profile Settings</a></li>
                    <li><a href="../deliveryAddress/index.php"><i class="bi bi-geo-alt-fill"></i> Delivery Addresses</a></li>
                    <li><a href="../cart/index.php"><i class="bi bi-cart3"></i> Cart</a></li>
                    <li><a href="../order/index.php"><i class="bi bi-bag-fill"></i> Orders</a></li>
                    <li><a href="../history/index.php"><i class="bi bi-clock-history"></i> History</a></li>
                    <li><a href="../wishlist/index.php"><i class="bi bi-heart"></i> Wishlist</a></li>
                    <li><a href="../reward/index.php"><i class="bi bi-award-fill"></i> Rewards</a></li>
                    <li><a href="../../auth/logout.php"><i class="bi bi-box-arrow-right"></i> Log Out</a></li>
                    <li><a href="../../auth/delete_account.php"><i class="bi bi-trash"></i> Delete Account</a></li>
                </ul>
            </div>

            <!--- right content space -->
            <div id="profileContent">
                <div class="content-header">
                    <h1 style="text-align: left;">Profile Settings</h1>
                    <p>Manage your profile</p>
                </div>
                <div class="content">
                    <?php if (!$isGuest && isset($_SESSION['success'])): ?>
                        <div class="successMessage"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
                    <?php endif; ?>

                    <?php if (!$isGuest && isset($_SESSION['error'])): ?>
                        <div class="errMessage"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                    <?php endif; ?>

                    <div class="card p-4 mb-4 text-center">
                        <form action="<?= $isGuest ? '#' : './update_profile.php' ?>" method="POST" enctype="multipart/form-data" id="profileForm">

                            <!-- profile picture --->
                            <div class="text-center profile-pic-container mb-3 ">
                                <img src="<?= htmlspecialchars($profileImage) ?>" 
                                    class="rounded-circle border border-3" 
                                    style="width:120px;height:120px;object-fit:cover; " 
                                    id="profilePicPreview" />

                                <!-- if is user, then only can edit ---->
                                <?php if (!$isGuest): ?>
                                    <label for="profilePicInput" class="edit-icon fs-5">
                                        <i class="bi bi-pencil-fill"></i>
                                    </label>
                                    <input type="file" name="profile_pic" id="profilePicInput" accept="image/*" style="display:none;">
                                <?php endif; ?>
                            </div>

                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">         
                                    <div class="field-row">
                                        <label for="name"><strong>Name:</strong></label>
                                        <span class="view-mode"><?= htmlspecialchars($name) ?></span>
                                        <?php if (!$isGuest): ?>
                                            <div class="input-error-wrapper">
                                                <input type="text" id="name" class="form-control edit-mode d-none textInput" 
                                                name="name" value="<?= htmlspecialchars($name) ?>">
                                                <div class="error-message" id="error_name"></div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (!$isGuest): ?>
                                        <i class="bi bi-pencil-fill edit-icon toggle-edit"></i>
                                    <?php endif; ?>
                                </li>

                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="field-row">
                                        <label><strong>Email:</strong> </label>
                                        <span class="view-mode"><?= htmlspecialchars($email) ?></span>
                                        <?php if (!$isGuest): ?>
                                            <div class="input-error-wrapper">
                                                <input type="email" id="email" class="form-control edit-mode d-none textInput" name="email" value="<?= htmlspecialchars($email) ?>">
                                                <div class="error-message" id="error_email"></div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (!$isGuest): ?>
                                        <i class="bi bi-pencil-fill edit-icon toggle-edit"></i>
                                    <?php endif; ?>
                                </li>

                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="field-row">
                                        <label><strong>Phone:</strong> </label>
                                        <span class="view-mode"><?= htmlspecialchars($phone) ?></span>
                                        <?php if (!$isGuest): ?>
                                            <div class="input-error-wrapper">
                                                <input type="text" id="phone" class="form-control edit-mode d-none textInput" name="phone" value="<?= htmlspecialchars($phone) ?>">
                                            <div class="error-message" id="error_phone"></div>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (!$isGuest): ?>
                                        <i class="bi bi-pencil-fill edit-icon toggle-edit"></i>
                                    <?php endif; ?>
                                </li>
                            </ul>

                            <?php if (!$isGuest): ?>
                                <button type="submit" class="btn btn-success mt-3 w-100 d-none saveButton">Save Changes</button>
                                <a href="../../auth/forgot_password.php" class="btn mt-3 w-100 changePasswordBtn">Change Password</a>
                            <?php else: ?>

                            <a href="../../auth/login.php" class="btn btn-primary mt-3 w-100 saveButton">Log In</a>
                            <p class="tips"> Log in to enjoy full access to all features! </p>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <footer><?php include("../../footer.php") ?> </footer>

        <?php if (!$isGuest): ?>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const form = document.getElementById('profileForm');
                    if (!form) {
                        return;
                    }

                    const saveBtn = document.querySelector('.btn-success'); 
                    const changePasswordBtn = document.querySelector('.changePasswordBtn'); 

                    // Toggle edit / save / change password
                    const toggles = form.querySelectorAll('.toggle-edit');
                    toggles.forEach(icon => {
                        icon.addEventListener('click', () => {
                            const listItem = icon.closest('li');
                            if (!listItem) return;
                            const viewEl = listItem.querySelector('.view-mode');
                            const editEl = listItem.querySelector('.edit-mode');
                            if (viewEl) viewEl.classList.toggle('d-none');
                            if (editEl) editEl.classList.toggle('d-none');

                            const isEditing = form.querySelectorAll('.edit-mode:not(.d-none)').length > 0;
                            saveBtn.classList.toggle('d-none', !isEditing);
                            changePasswordBtn.classList.toggle('d-none', isEditing);
                        });
                    });

                    // Auto-submit profile pic
                    const profilePicInput = document.getElementById('profilePicInput');
                    if (profilePicInput) {
                        profilePicInput.addEventListener('change', () => form.submit());
                    }

                    //validation for input
                    form.addEventListener('submit', function (e) {
                        e.preventDefault();     
                        let valid = true;

                        const nameInput = document.getElementById("name");
                        const emailInput = document.getElementById("email");
                        const phoneInput = document.getElementById("phone");

                        //check if inputs are edited
                        const isNameEdited = nameInput && nameInput.offsetParent !== null;
                        const isEmailEdited = emailInput && emailInput.offsetParent !== null;
                        const isPhoneEdited = phoneInput && phoneInput.offsetParent !== null;

                        document.getElementById("error_name").textContent = "";
                        document.getElementById("error_email").textContent = "";
                        document.getElementById("error_phone").textContent = "";

                        nameInput.classList.remove("input-error");
                        emailInput.classList.remove("input-error");
                        phoneInput.classList.remove("input-error");

                        const namePattern = /^[A-Za-z\s]+$/;
                        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        const phonePattern = /^(?:\+60|60|0)1\d{8,9}$/;

                        if (isNameEdited) {
                            const val = nameInput.value.trim();
                            if (!val) {
                                document.getElementById("error_name").textContent = "Name is required";
                                nameInput.classList.add("input-error");
                                valid = false;
                            } else if (!namePattern.test(val)) {
                                document.getElementById("error_name").textContent = "Name should contain only letters and spaces.";
                                nameInput.classList.add("input-error");
                                valid = false;
                            }
                        }

                        if (isEmailEdited) {
                            const val = emailInput.value.trim();
                            if (!val) {
                                document.getElementById("error_email").textContent = "Email is required";
                                emailInput.classList.add("input-error");
                                valid = false;
                            } else if (!emailPattern.test(val)) {
                                document.getElementById("error_email").textContent = "Please enter a valid email address";
                                emailInput.classList.add("input-error");
                                valid = false;
                            }
                        }

                        if (isPhoneEdited) {
                            const val = phoneInput.value.trim();
                            if (!val) {
                                document.getElementById("error_phone").textContent = "Phone number is required";
                                phoneInput.classList.add("input-error");
                                valid = false;
                            } else if (!phonePattern.test(val)) {
                                document.getElementById("error_phone").textContent = "Please enter a valid Malaysian phone number (e.g., +60123456789)";
                                phoneInput.classList.add("input-error");
                                valid = false;
                            }
                        }

                        if (!valid) {
                            e.preventDefault();

                            if (nameInput.classList.contains("input-error")) {
                                nameInput.focus();
                            } else if (emailInput.classList.contains("input-error")) {
                                emailInput.focus();
                            } else if (phoneInput.classList.contains("input-error")) {
                                phoneInput.focus();
                            }
                            return;
                        }

                        const formData = new FormData();
                        if(isEmailEdited) {
                            formData.append('email', emailInput.value.trim());
                        }
                        if(isPhoneEdited) {
                            formData.append('phone', phoneInput.value.trim());
                        }

                        fetch('./checkDuplicate.php', { method: 'POST', body: formData })
                        .then(res => res.json())
                        .then(data => {
                            let duplicate = false;
                            if(data.email) {
                                document.getElementById("error_email").textContent = "Email already in use by another user";
                                emailInput.classList.add("input-error");
                                duplicate = true;
                            }
                            if(data.phone) {
                                document.getElementById("error_phone").textContent = "Phone already in use by another user";
                                phoneInput.classList.add("input-error");
                                duplicate = true;
                            }
                            if(duplicate) {
                                if(emailInput.classList.contains("input-error")) {
                                    emailInput.focus();
                                }
                                else if(phoneInput.classList.contains("input-error")) {
                                    phoneInput.focus();
                                }
                                return;
                            }
                            //if all pass then only subimt
                            form.submit();
                        })
                        .catch(err => {
                            console.error("Duplicate check failed", err);
                            form.submit(); 
                        });


                    });

                

                });
            </script>


        <?php endif; ?>


    </body>
</html>
