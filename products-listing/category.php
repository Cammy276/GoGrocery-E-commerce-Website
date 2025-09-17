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
<?php
include __DIR__ . '/../livechat/chat_UI.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($category['name']) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<link rel="stylesheet" href="../css/styles.css">
<link rel="stylesheet" href="../css/header_styles.css">
<link rel="stylesheet" href="../css/footer_styles.css">
<link rel="stylesheet" href="../css/filter_sort.css">
<style>
     .category-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }
    
    h1 {
        text-align: center;
        margin: 20px 0 30px;
        color: #009c99;
        font-weight: 700;
    }
    
    .category-header {
        position: relative;
        margin-bottom: 30px;
        padding: 15px;
        border-radius: 8px;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .category-header-inner {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 15px;
        width: 100%;
    }
    
    .subcategories {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        flex: 1;
        min-width: 0;
        max-width: calc(100% - 180px);
    }

    .subcategories:empty {
        display:none;
    }
    
    .subcategories a {
        padding: 8px 16px;
        background-color: #e9ecef;
        border-radius: 20px;
        text-decoration: none;
        color: #495057;
        transition: all 0.3s;
        font-weight: 500;
    }
    
    .subcategories a:hover {
        background-color: #28a7b5;
        color: white;
        transform: translateY(-2px);
    }
    
    .filter-sort {
        flex-shrink: 0;
        position: relative;
        z-index: 10;
    }
    
    .filter-sort button {
        background: #28a7b5;
        color: #fff;
        border: none;
        padding: 12px 20px;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-size: 16px;
        transition: all 0.3s;
    }

    .filter-sort button:hover{
        background: #0d6f79;
        transform: translateY(-2px);
    }

    .filter-sort button i {
        font-size: 1.3rem;
        margin: 0;
        line-height: 1;
        display: flex;
        align-items: center;
    }

    .filter-sort button.active {
        background: #0b5c65;
        transform: scale(1.05);
        box-shadow: 0 0 8px rgba(0,0,0,0.2);
        font-weight: bold;
    }

    .filter-sort button.active:hover {
        background: #096671;
        transform: scale(1.08);
    }

    .filter-sort-dropdown {
        display: none;
        position: absolute;
        right: 0;
        top: 110%;
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 6px;
        min-width: 180px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        z-index: 1000;
    }

    .filter-sort-dropdown a {
        display: block;
        padding: 10px 12px;
        text-decoration: none;
        color: #333;
        font-size: 14px;
        transition: background 0.2s;
    }

    .filter-sort-dropdown a:hover {
        background: #f5f5f5;
    }

    .filter-sort-dropdown.show {
        display: block;
    }
    
    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 25px;
        margin-top: 30px;
    }
    
    .product-item {
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s, box-shadow 0.3s;
        display: flex;
        flex-direction: column;
    }
    
    .product-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    .product-item img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-bottom: 1px solid #eee;
    }
    
    .product-item h3 {
        padding: 15px 15px 5px;
        font-size: 18px;
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
    }
    
    .product-item p {
        padding: 0 15px;
        color: #28a7b5;
        font-weight: 600;
        margin: 0 0 15px;
    }
    
    .product-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 15px 15px;
        margin-top: auto;
    }
    
    .special-offer {
        background: #ff6b6b;
        color: white;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .wishlist-icon {
        background: none;
        border: none;
        color: #ccc;
        font-size: 1.5rem;
        cursor: pointer;
        transition: color 0.3s;
        padding: 5px;
    }
    
    .wishlist-icon.filled, .wishlist-icon:hover {
        color: #ff6b6b;
    }
    
    .no-products {
        text-align: center;
        grid-column: 1 / -1;
        padding: 40px;
        color: #6c757d;
        font-size: 18px;
    }
</style>
</head>
<header>
    <?php include '../header.php'; ?>
</header>
<body>
<h1><?= htmlspecialchars($category['name']) ?></h1>
<div>
<div class="category-container">
    <div class="category-header">
    <div class="category-header-inner">
        <div class="subcategories">
            <?php foreach ($subcategories as $sub): ?>
                <a href="category.php?slug=<?= urlencode($sub['slug']) ?>">
                    <?= htmlspecialchars($sub['name']) ?>
                </a>
            <?php endforeach; ?>
        </div>
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
