<?php
define('BASE_URL', '/GoGrocery-E-commerce-Website/'); 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include(__DIR__ . '/connect_db.php');

$user_id = $_SESSION['user_id'] ?? null;

// --- Fetch Categories (Recursive up to 3 levels) ---
function fetchCategories($conn, $parent_id = null, $level = 0) {
    $sql = "SELECT category_id, name, slug FROM categories WHERE parent_id " . 
           ($parent_id === null ? "IS NULL" : "= ?") . " ORDER BY name ASC";
    $stmt = $conn->prepare($sql);
    if ($parent_id !== null) $stmt->bind_param("i", $parent_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $categories = [];
    while ($row = $result->fetch_assoc()) {
        if ($level < 2) {
            $row['children'] = fetchCategories($conn, $row['category_id'], $level + 1);
        }
        $categories[] = $row;
    }
    return $categories;
}

$categories = fetchCategories($conn);

// --- Wishlist Count ---
$wishlist_count = 0;
if ($user_id) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM wishlist WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $wishlist_count = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
}

// --- Cart Count + Total Price ---
$cart_count = 0;
$cart_total = 0.00;
if ($user_id) {
    $stmt = $conn->prepare("
        SELECT 
            COALESCE(SUM(quantity),0) AS total_qty, 
            COALESCE(SUM((unit_price - line_discount) * quantity),0) AS total_price 
        FROM cart_items 
        WHERE user_id = ?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $cart_data = $stmt->get_result()->fetch_assoc();
    $cart_count = $cart_data['total_qty'] ?? 0;
    $cart_total = $cart_data['total_price'] ?? 0.00;
}

// --- Current page for nav active state ---
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Header</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="./css/styles.css">
<link rel="stylesheet" href="./css/header_styles.css">
<script>
function toggleCategories() {
    const dropdown = document.querySelector('.categories-dropdown');
    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
}
document.addEventListener("click", function(e) {
    const btn = document.querySelector(".categories-menu");
    if (!btn.contains(e.target)) {
        document.querySelector(".categories-dropdown").style.display = "none";
    }
});
</script>
</head>
<body>

<header class="site-header">
  <!-- TOP HEADER -->
  <div class="header-top">
    <div class="header-left">
      <a href="index.php"><img src="<?= BASE_URL ?>images/logo/gogrocery_logo.png" alt="GoGrocery Logo"></a>
    </div>

    <!-- Categories + Search -->
    <div class="header-middle">
      <div class="categories-menu">
        <div class="categories-btn" onclick="toggleCategories()">
          <i class="bi bi-list"></i>Categories
        </div>
        <div class="categories-dropdown">
          <ul>
            <?php
            function renderCategoryMenu($cats) {
                foreach ($cats as $cat) {
                    $cat_link = BASE_URL . "products-listing/category.php?slug=" . urlencode($cat['slug']);
                    if (!empty($cat['children'])) {
                        echo "<li class='has-children'>
                                <a href='$cat_link'>{$cat['name']}</a>
                                <ul>";
                        renderCategoryMenu($cat['children']);
                        echo "</ul></li>";
                    } else {
                        echo "<li><a href='$cat_link'>{$cat['name']}</a></li>";
                    }
                }
            }
            renderCategoryMenu($categories);
            ?>
          </ul>
        </div>
      </div>

      <!-- Search Box -->
      <div class="search-box">
        <form action="search.php" method="GET" class="search-form">
          <input type="text" name="q" placeholder="Search products, brands, categories...">
          <button type="submit"><i class="bi bi-search"></i></button>
        </form>
      </div>
    </div>

    <!-- Right Icons -->
    <div class="header-right">
      <div class="icon-box">
          <?php if (isset($_SESSION['user_id'])): ?>
              <a href="<?=BASE_URL?>profile/settings"><i class="bi bi-person-fill"></i></a>
              <span class="label">Profile</span>
          <?php else: ?>
              <a href="<?=BASE_URL?>auth/login.php"><i class="bi bi-person-fill"></i></a>
              <span class="label">Login</span>
          <?php endif; ?>
      </div>
      <div class="icon-box">
        <a href="<?=BASE_URL?>profile/wishlist/index.php"><i class="bi bi-heart-fill"></i></a>
        <span class="icon-badge" id="wishlist-count"><?= $wishlist_count ?></span>
        <span class="label">Wishlist</span>
      </div>
      <div class="icon-box">
        <a href="<?=BASE_URL?>profile/cart/index.php"><i class="bi bi-cart-fill"></i></a>
        <span class="icon-badge" id="cart-count"><?= (int)$cart_count ?></span>
        <span class="label" id="cart-total">RM <?= number_format((float)$cart_total, 2) ?></span>
      </div>
    </div>
  </div>

  <!-- NAVIGATION -->
  <div class="navbar">
    <div class="icon-box">
      <a href="<?= BASE_URL ?>index.php" class="<?= ($current_page == 'index.php' ? 'active' : '') ?>"><i class="bi bi-house-fill"></i><span class="label">Home</span></a>
    </div>
    <div class="icon-box">
      <a href="<?= BASE_URL ?>company/about.php" class="<?= ($current_page == 'about.php' ? 'active' : '') ?>"><i class="bi bi-file-earmark-fill"></i><span class="label">About</span></a>
    </div>
    <?php
    $help_pages = ['faq.php', 'contact.php'];
    ?>
    <div class="icon-box dropdown">
      <a href="javascript:void(0)" class="dropdown-toggle">
        <i class="bi bi-exclamation-circle-fill"></i>
        <span class="label">Help Center</span>
      </a>
      <div class="dropdown-menu">
        <a href="<?= BASE_URL ?>help/faq.php" class="<?= ($current_page == 'faq.php' ? 'active' : '') ?>">FAQs</a>
        <a href="<?= BASE_URL ?>help/contact.php" class="<?= ($current_page == 'contact.php' ? 'active' : '') ?>">Contact Form</a>
      </div>
    </div>
    <div class="icon-box">
      <a href="<?= BASE_URL ?>profile/order/index.php" class="<?= ($current_page == 'index.php' ? 'active' : '') ?>"><i class="bi bi-bag-fill"></i></i><span class="label">Orders</span></a>
    </div>
    <div class="icon-box">
      <a href="<?= BASE_URL ?>profile/deliveryAddress/index.php" class="<?= ($current_page == 'index.php' ? 'active' : '') ?>"><i class="bi bi-geo-alt-fill"></i><span class="label">Delivery Address</span></a>
    </div>
    <div class="icon-box">
      <a href="<?= BASE_URL ?>profile/reward/index.php" class="<?= ($current_page == 'index.php' ? 'active' : '') ?>"><i class="bi bi-award-fill"></i> <span class="label">Rewards</span></a>
    </div>
  </div>
</header>
<script>
document.addEventListener("DOMContentLoaded", function() {
  document.querySelectorAll(".icon-box.dropdown").forEach(dropdown => {
    const toggle = dropdown.querySelector(".dropdown-toggle");
    const menu = dropdown.querySelector(".dropdown-menu");

    toggle.addEventListener("click", function(e) {
      e.preventDefault();
      dropdown.classList.toggle("open");
    });

    // Close dropdown if clicking outside
    document.addEventListener("click", function(e) {
      if (!dropdown.contains(e.target)) dropdown.classList.remove("open");
    });
  });
});
</script>
</body>
</html>
