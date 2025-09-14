<?php

require_once(__DIR__ . '/connect_db.php');

$user_id = $_SESSION['user_id'] ?? null;

// --- Recursive: get all category IDs ---
function getAllCategoryIds($conn, $parent_id) {
    $ids = [$parent_id];
    $stmt = $conn->prepare("SELECT category_id FROM categories WHERE parent_id = ?");
    $stmt->bind_param("i", $parent_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $ids = array_merge($ids, getAllCategoryIds($conn, $row['category_id']));
    }
    return $ids;
}

// --- Recursive: get all subcategories ---
function getAllSubcategories($conn, $parent_id) {
    $subcategories = [];
    $stmt = $conn->prepare("SELECT category_id, name, slug FROM categories WHERE parent_id = ?");
    $stmt->bind_param("i", $parent_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $subcategories[] = $row;
        $children = getAllSubcategories($conn, $row['category_id']);
        $subcategories = array_merge($subcategories, $children);
    }
    return $subcategories;
}

// --- Fetch top categories ---
$topCategories = $conn->query("SELECT category_id, name, slug FROM categories WHERE parent_id IS NULL LIMIT 7");
$categoryData = [];

while ($cat = $topCategories->fetch_assoc()) {
    $catId = $cat['category_id'];
    $all_category_ids = getAllCategoryIds($conn, $catId);

    $products = [];
    if (!empty($all_category_ids)) {
        $placeholders = implode(',', array_fill(0, count($all_category_ids), '?'));
        $types = str_repeat('i', count($all_category_ids));
        $sql = "SELECT p.product_id, p.product_name, p.unit_price, p.slug,
               p.special_offer_label,  -- <-- new
               pi.product_image_url, pi.alt_text
        FROM products p
        LEFT JOIN product_images pi ON p.product_id = pi.product_id
        WHERE p.category_id IN ($placeholders)
        ORDER BY RAND()
        LIMIT 10";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$all_category_ids);
        $stmt->execute();
        $products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    $subcategories = getAllSubcategories($conn, $catId);

    $categoryData[] = [
        'category_id'    => $catId,
        'category_name'  => $cat['name'],
        'category_slug'  => $cat['slug'],
        'subcategories'  => $subcategories,
        'products'       => $products
    ];
}

// --- Wishlist support ---
$wishlist_items = [];
if ($user_id) {
    $res = $conn->query("SELECT product_id FROM wishlist WHERE user_id = $user_id");
    while ($row = $res->fetch_assoc()) {
        $wishlist_items[] = $row['product_id'];
    }
}

// --- Helper ---
function getImageUrl($path) {
    return !empty($path) ? BASE_URL . ltrim($path, '/') : BASE_URL . 'images/products/placeholder.png';
}

// --- Helper: get cart info for AJAX updates ---
function getCartInfo($conn, $user_id){
    $stmt = $conn->prepare("
        SELECT COALESCE(SUM(quantity),0) AS total_qty,
               COALESCE(SUM((unit_price - line_discount) * quantity),0) AS total_price
        FROM cart_items
        WHERE user_id = ?
    ");
    $stmt->bind_param("i",$user_id);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();
    return [
        'total_qty' => (int)$data['total_qty'],
        'total_price' => (float)$data['total_price']
    ];
}
$cart_data = $user_id ? getCartInfo($conn,$user_id) : ['total_qty'=>0,'total_price'=>0.00];
?>
<?php
    include __DIR__ . '/../livechat/chat_UI.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Home Page</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="./css/styles.css">
<link rel="stylesheet" href="./css/index_display_styles.css">
</head>
<body>
<div class="container my-4">
<?php foreach ($categoryData as $cat): ?>
    <div class="category-section">
        <h3 class="category-title">
            <a href="<?= BASE_URL ?>products-listing/category.php?slug=<?= urlencode($cat['category_slug']) ?>">
                <?= htmlspecialchars($cat['category_name']) ?>
            </a>
        </h3>

        <?php if(!empty($cat['subcategories'])): ?>
        <div class="carousel-wrapper">
            <button class="scroll-btn left" onclick="scrollLeft(this)"><i class="bi bi-chevron-left"></i></button>
            <div class="scroll-container subcategories">
                <?php foreach($cat['subcategories'] as $sub): ?>
                    <a href="<?= BASE_URL ?>products-listing/category.php?slug=<?= urlencode($sub['slug']) ?>">
                        <?= htmlspecialchars($sub['name']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
            <button class="scroll-btn right" onclick="scrollRight(this)"><i class="bi bi-chevron-right"></i></button>
        </div>
        <?php endif; ?>
        <div class="carousel-wrapper">
    <button class="scroll-btn left" onclick="scrollLeft(this)"><i class="bi bi-chevron-left"></i></button>
    <div class="scroll-container">
        <?php foreach($cat['products'] as $p):
            $slug = $p['slug'] ?? $p['product_id'];
            $link = BASE_URL . "products-listing/product.php?slug=" . urlencode($slug);
            $img  = getImageUrl($p['product_image_url']);
            $alt  = $p['alt_text'] ?? $p['product_name'];
            $in_wishlist = in_array($p['product_id'], $wishlist_items);
        ?>
        <div class="product-item">
            <a href="<?= $link ?>" style="text-decoration:none;color:inherit;">
                <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($alt) ?>">
                <h5><?= htmlspecialchars($p['product_name']) ?></h5>
                <p>RM <?= number_format($p['unit_price'],2) ?></p>
            </a>
            <div class="product-footer">
                <?php if(!empty($p['special_offer_label'])): ?>
                    <span class="special-offer"><?= htmlspecialchars($p['special_offer_label']) ?></span>
                <?php endif; ?>
                <button class="wishlist-icon <?= $in_wishlist ? 'filled' : '' ?>" data-product-id="<?= $p['product_id'] ?>">
                    <i class="bi bi-heart<?= $in_wishlist ? '-fill' : '' ?>"></i>
                </button>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <button class="scroll-btn right" onclick="scrollRight(this)"><i class="bi bi-chevron-right"></i></button>
</div>
<?php endforeach; ?>
</div>
<script>
// Scroll functions
function scrollLeft(btn){ btn.nextElementSibling.scrollBy({ left:-300, behavior:'smooth' }); }
function scrollRight(btn){ btn.previousElementSibling.scrollBy({ left:300, behavior:'smooth' }); }
function updateScrollButtons(wrapper){
    const container = wrapper.querySelector('.scroll-container');
    const leftBtn = wrapper.querySelector('.scroll-btn.left');
    const rightBtn = wrapper.querySelector('.scroll-btn.right');
    const overflow = container.scrollWidth > container.clientWidth;
    leftBtn.style.display = overflow ? 'block' : 'none';
    rightBtn.style.display = overflow ? 'block' : 'none';
}
document.querySelectorAll('.carousel-wrapper').forEach(updateScrollButtons);
window.addEventListener('resize', ()=>document.querySelectorAll('.carousel-wrapper').forEach(updateScrollButtons));

// Wishlist toggle + live update
document.querySelectorAll('.wishlist-icon').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const productId = this.dataset.productId;
        const icon = this.querySelector('i');

        <?php if (!$user_id): ?>
        window.location.href = '<?= BASE_URL ?>auth/login.php';
        return;
        <?php else: ?>
        const isFilled = this.classList.contains('filled');

        fetch('<?= BASE_URL ?>products-listing/wishlist_toggle.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ product_id: productId, action: isFilled ? 'remove' : 'add' })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Toggle heart icon
                this.classList.toggle('filled');
                icon.className = 'bi ' + (isFilled ? 'bi-heart' : 'bi-heart-fill');

                // Update header wishlist count
                const wishlistCounter = document.getElementById('wishlist-count');
                if (wishlistCounter) wishlistCounter.textContent = data.wishlist_count;

                // Update header cart count & total price
                const cartCountEl = document.getElementById('cart-count');
                const cartTotalEl = document.getElementById('cart-total');
                if (cartCountEl) cartCountEl.textContent = data.cart_count;
                if (cartTotalEl) cartTotalEl.textContent = 'RM ' + parseFloat(data.cart_total).toFixed(2);
            } else {
                if (data.status === 'not_logged_in') {
                    window.location.href = '<?= BASE_URL ?>auth/login.php';
                } else {
                    alert('Failed to update wishlist.');
                }
            }
        })
        .catch(err => console.error('Error updating wishlist:', err));
        <?php endif; ?>
    });
});
</script>
</body>
</html>