<?php
include(__DIR__ . '/../connect_db.php');
session_start();
$user_id = $_SESSION['user_id'] ?? null;

$slug = $_GET['slug'] ?? '';
if (!$slug) die("Category not specified.");

// --- Get category info ---
$stmt = $conn->prepare("SELECT category_id, name, parent_id FROM categories WHERE slug = ?");
$stmt->bind_param("s", $slug);
$stmt->execute();
$category = $stmt->get_result()->fetch_assoc();
if (!$category) die("Category not found.");

// --- Get all category IDs recursively ---
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
$all_category_ids = getAllCategoryIds($conn, $category['category_id']);

$order_sql = "";
if(!empty($_GET['sort'])) {
    switch ($_GET['sort']) {
        case 'price_asc':
            $order_sql = " ORDER BY p.unit_price ASC ";
            break;
        case 'price_desc':
            $order_sql = " ORDER BY p.unit_price DESC ";
            break;
        case 'newest':
            $order_sql = " ORDER BY p.created_at DESC";
            break;
        case 'popular':
            $order_sql = " ORDER BY p.product_id DESC ";
            break;   
    }
}

$sortApplied = !empty($_GET['sort']);

// --- Fetch products ---
$products = [];
if (!empty($all_category_ids)) {
    $placeholders = implode(',', array_fill(0, count($all_category_ids), '?'));
    $types = str_repeat('i', count($all_category_ids));

    $sql = "SELECT p.*, pi.product_image_url, pi.alt_text 
            FROM products p 
            LEFT JOIN product_images pi ON p.product_id = pi.product_id 
            WHERE p.category_id IN ($placeholders) $order_sql";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$all_category_ids);
    $stmt->execute();
    $products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// --- Fetch user's wishlist items ---
$wishlist_items = [];
if ($user_id) {
    $res = $conn->query("SELECT product_id FROM wishlist WHERE user_id = $user_id");
    while ($row = $res->fetch_assoc()) $wishlist_items[] = $row['product_id'];
}

// --- Helper function for image URL ---
function getImageUrl($image_path){
    return !empty($image_path) ? BASE_URL . ltrim($image_path,'/') : BASE_URL . 'images/products/placeholder.png';
}

// --- Fetch subcategories depending on current level ---
$subcategories = [];
if ($category['parent_id'] === null) {
    // top-level -> fetch level2
    $stmt = $conn->prepare("SELECT category_id, name, slug FROM categories WHERE parent_id = ?");
    $stmt->bind_param("i", $category['category_id']);
    $stmt->execute();
    $subcategories = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} else {
    // not top-level, fetch children of this category
    $stmt = $conn->prepare("SELECT category_id, name, slug FROM categories WHERE parent_id = ?");
    $stmt->bind_param("i", $category['category_id']);
    $stmt->execute();
    $subcategories = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($category['name']) ?></title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<link rel="stylesheet" href="../css/styles.css">
<link rel="stylesheet" href="../css/header_styles.css">
<link rel="stylesheet" href="../css/footer_styles.css">
<link rel="stylesheet" href="../css/category_styles.css">
<link rel="stylesheet" href="../css/filter_sort.css">
</head>
<header>
    <?php include '../header.php'; ?>
</header>
<body>
    <div class="category-container">
<h1><?= htmlspecialchars($category['name']) ?></h1>

<?php if (!empty($subcategories)): ?>
<div class="subcategories">
    <?php foreach ($subcategories as $sub): ?>
        <a href="category.php?slug=<?= urlencode($sub['slug']) ?>">
            <?= htmlspecialchars($sub['name']) ?>
        </a>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<div class="category-header">
    <h1><?= htmlspecialchars($category['name']) ?></h1>
    <div class="filter-sort">
        <button id="filterSortBtn" class="<?= $sortApplied ? 'active' : '' ?>">
            <i class="bi <?= $sortApplied ? 'bi-funnel-fill' : 'bi-funnel' ?>"></i> Sort
        </button>
        <div class="filter-sort-dropdown" id="filterSortDropdown">
            <a href="?slug=<?= urlencode($slug) ?>&sort=price_asc">Price: Low to High</a>
            <a href="?slug=<?= urlencode($slug) ?>&sort=price_desc">Price: High to Low</a>
            <a href="?slug=<?= urlencode($slug) ?>&sort=newest">Newest</a>
            <a href="?slug=<?= urlencode($slug) ?>&sort=popular">Most Popular</a>
        </div>
    </div>
</div>

<div class="products-grid">
<?php if (!empty($products)): ?>
    <?php foreach ($products as $p):
        $product_slug = $p['slug'] ?? $p['product_id'];
        $product_link = "product.php?slug=" . urlencode($product_slug);
        $image_url = getImageUrl($p['product_image_url']);
        $alt_text = $p['alt_text'] ?? $p['product_name'];
        $in_wishlist = in_array($p['product_id'], $wishlist_items);
    ?>
    <div class="product-item">
        <a href="<?= $product_link ?>" style="text-decoration:none;color:inherit; flex-grow:1;">
            <img src="<?= htmlspecialchars($image_url) ?>" alt="<?= htmlspecialchars($alt_text) ?>">
            <h3><?= htmlspecialchars($p['product_name']) ?></h3>
            <p>RM <?= number_format($p['unit_price'],2) ?> / <?= htmlspecialchars($p['weight_volume'] ?? '-') ?></p>
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
<?php else: ?>
    <p>No products found in this category.</p>
<?php endif; ?>
</div>

<script>
document.getElementById("filterSortBtn").addEventListener("click", () => {
    document.getElementById("filterSortDropdown").classList.toggle("show");
});
window.addEventListener("click", (e) => {
    if (!e.target.closest(".filter-sort")) {
        document.getElementById("filterSortDropdown").classList.remove("show");
    }
});
document.querySelectorAll('.wishlist-icon').forEach(btn => {
    btn.addEventListener('click', function(e){
        e.preventDefault();
        const productId = this.dataset.productId;
        <?php if(!$user_id): ?>
            window.location.href = '<?= BASE_URL ?>auth/login.php';
            return;
        <?php else: ?>
            const isFilled = this.classList.contains('filled');
            fetch('<?= BASE_URL ?>products-listing/wishlist_toggle.php', {
                method:'POST',
                headers:{ 'Content-Type':'application/json' },
                body: JSON.stringify({ product_id: productId, action: isFilled ? 'remove':'add' })
            }).then(res => res.json()).then(data=>{
                if(data.success){
                    this.classList.toggle('filled');
                    this.querySelector('i').className = 'bi ' + (isFilled ? 'bi-heart':'bi-heart-fill');

                    const counter = document.getElementById('wishlist-count');
                    if(counter){
                        let count = parseInt(counter.textContent) || 0;
                        if(data.status === 'added'){
                            count++;
                        } else if(data.status === 'removed' && count > 0){
                            count--;
                        }
                        counter.textContent = count;
                    }
                } else {
                    alert('Failed to update wishlist.');
                }
            });
        <?php endif; ?>
    });
});
</script>
<?php include '../footer.php'; ?>
</div>
</body>
</html>
