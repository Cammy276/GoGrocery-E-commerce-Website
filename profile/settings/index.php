<?php
session_start();
include(__DIR__ . '/../../connect_db.php');

// Base image folder
$baseUrl = "/GoGrocery-E-commerce-Website/images/users/";

// Defaults for guest
$name = "Guest";
$email = "";
$phone = "";
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
  <link rel="stylesheet" href="../../css/reward_styles.css">
  <link rel="stylesheet" href="../../css/header_styles.css">
  <link rel="stylesheet" href="../../css/footer_styles.css">
  <style>
    .profile-pic-container {
    position: relative;
    display: inline-block;
    }
    .edit-icon {
    cursor: pointer;
    margin-left: 8px;
    color: #6c757d; /* grey */
    }
    .edit-icon:hover {
    color: #000;
    }

  </style>
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
              <h1>Profile Settings</h1>
              <p>Manage your profile</p>
          </div>
          <div class="content">
              <?php if (!$isGuest && isset($_SESSION['success'])): ?>
              <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
              <?php endif; ?>

              <?php if (!$isGuest && isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
              <?php endif; ?>

              <div class="card p-4 mb-4">
                <form action="<?= $isGuest ? '#' : './update_profile.php' ?>" method="POST" enctype="multipart/form-data" id="profileForm">
                  <div class="text-center profile-pic-container mb-3">
                    <img src="<?= htmlspecialchars($profileImage) ?>" 
                      class="rounded-circle" 
                      style="width:120px;height:120px;object-fit:cover;" 
                      id="profilePicPreview" />
                    <?php if (!$isGuest): ?>
                      <label for="profilePicInput" class="edit-icon fs-5">
                      <i class="bi bi-pencil-fill"></i>
                      </label>
                      <input type="file" name="profile_pic" id="profilePicInput" accept="image/*" style="display:none;">
                    <?php endif; ?>
                  </div>

                  <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                      <div>
                        <strong>Name:</strong> 
                        <span class="view-mode"><?= htmlspecialchars($name) ?></span>
                        <?php if (!$isGuest): ?>
                          <input type="text" class="form-control edit-mode d-none textInput" name="name" value="<?= htmlspecialchars($name) ?>">
                        <?php endif; ?>
                      </div>
                   
                      <?php if (!$isGuest): ?>
                        <i class="bi bi-pencil-fill edit-icon toggle-edit"></i>
                      <?php endif; ?>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                      <div>
                        <strong>Email:</strong> 
                        <span class="view-mode"><?= htmlspecialchars($email) ?></span>
                        <?php if (!$isGuest): ?>
                          <input type="email" class="form-control edit-mode d-none textInput" name="email" value="<?= htmlspecialchars($email) ?>">
                        <?php endif; ?>
                      </div>
                      <?php if (!$isGuest): ?>
                        <i class="bi bi-pencil-fill edit-icon toggle-edit"></i>
                      <?php endif; ?>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                      <div>
                        <strong>Phone:</strong> 
                        <span class="view-mode"><?= htmlspecialchars($phone) ?></span>
                        <?php if (!$isGuest): ?>
                        <input type="text" class="form-control edit-mode d-none textInput" name="phone" value="<?= htmlspecialchars($phone) ?>">
                        <?php endif; ?>
                      </div>
                      <?php if (!$isGuest): ?>
                        <i class="bi bi-pencil-fill edit-icon toggle-edit"></i>
                      <?php endif; ?>
                    </li>
                  </ul>

                  <?php if (!$isGuest): ?>
                    <button type="submit" class="btn btn-success mt-3 w-100 edit-mode d-none">Save Changes</button>
                    <a href="../../auth/forgot_password.php" class="btn btn-primary mt-3 w-100">Forgot Password</a>
                  <?php else: ?>
                    <a href="../auth/login.php" class="btn btn-primary mt-3 w-100">Log In</a>
                  <?php endif; ?>
                </form>
              </div>
            </div>
          </div>
    </div>

    <?php if (!$isGuest): ?>
      <script>
        const toggles = document.querySelectorAll('.toggle-edit');

        toggles.forEach(icon => {
        icon.addEventListener('click', () => {
        const listItem = icon.closest('li');
        listItem.querySelector('.view-mode').classList.toggle('d-none');
        listItem.querySelector('.edit-mode').classList.toggle('d-none');

        if (document.querySelectorAll('.edit-mode:not(.d-none)').length > 0) {
        document.querySelector('.btn-success').classList.remove('d-none');
        } else {
        document.querySelector('.btn-success').classList.add('d-none');
        }
        });
        });

        // auto submit if profile picture selected
        document.getElementById('profilePicInput').addEventListener('change', () => {
        document.getElementById('profileForm').submit();
        });
      </script>
    <?php endif; ?>

  </body>
</html>
