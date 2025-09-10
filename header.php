<?php
session_start();
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
    $stmt = $conn->prepare("SELECT SUM(quantity) AS total_qty, SUM((unit_price - line_discount) * quantity) AS total_price 
                            FROM cart_items WHERE user_id = ?");
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

<!-- TOP HEADER -->
<div class="header-top">
  <div class="header-left">
    <a href="index.php"><img src="./images/logo/gogrocery_logo.png" alt="Company Logo"></a>
  </div>

  <!-- Categories + Search -->
  <div class="header-middle">
    <div class="categories-menu">
      <div class="categories-btn" onclick="toggleCategories()">
        <i class="bi bi-list"></i> Categories
      </div>
      <div class="categories-dropdown">
        <ul>
          <?php
          function renderCategoryMenu($cats) {
              foreach ($cats as $cat) {
                  $cat_link = "./products-listing/category.php?slug=" . urlencode($cat['slug']);
                  if (!empty($cat['children'])) {
                      echo "<li class='has-children'><a href='$cat_link'>{$cat['name']}</a> <i class='bi bi-chevron-right'></i><ul>";
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
      <form action="search.php" method="GET">
        <input type="text" name="q" placeholder="Search products, brands, categories...">
        <button type="submit"><i class="bi bi-search"></i></button>
      </form>
    </div>
  </div>

  <!-- Right Icons -->
  <div class="header-right">
    <div class="icon-box">
      <a href="login.php"><i class="bi bi-person-fill"></i></a>
      <span class="label">Login</span>
    </div>
    <div class="icon-box">
      <a href="wishlist.php"><i class="bi bi-heart-fill"></i></a>
      <span class="icon-badge"><?= $wishlist_count ?></span>
      <span class="label">Wishlist</span>
    </div>
    <div class="icon-box">
      <a href="cart.php"><i class="bi bi-cart-fill"></i></a>
      <span class="icon-badge"><?= $cart_count ?></span>
      <span class="label">RM <?= number_format($cart_total,2) ?></span>
    </div>
  </div>
</div>

<!-- NAVIGATION -->
<div class="navbar">
  <a href="index.php" class="<?= ($current_page == 'index.php' ? 'active' : '') ?>"><i class="bi bi-house-fill"></i><span>Home</span></a>
  <a href="about.php" class="<?= ($current_page == 'about.php' ? 'active' : '') ?>"><i class="bi bi-file-earmark-fill"></i><span>About</span></a>
  <a href="help.php" class="<?= ($current_page == 'help.php' ? 'active' : '') ?>"><i class="bi bi-exclamation-circle-fill"></i><span>Help Center</span></a>
  <a href="best-seller.php" class="<?= ($current_page == 'best-seller.php' ? 'active' : '') ?>"><i class="bi bi-fire"></i><span>Best Seller</span></a>
  <a href="special-deal.php" class="<?= ($current_page == 'special-deal.php' ? 'active' : '') ?>"><i class="bi bi-tag-fill"></i><span>Special Deal</span></a>
  <a href="new-product.php" class="<?= ($current_page == 'new-product.php' ? 'active' : '') ?>"><i class="bi bi-gem"></i><span>New Product</span></a>
</div>
</body>
</html>
