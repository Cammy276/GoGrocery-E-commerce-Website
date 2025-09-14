<?php

require_once(__DIR__ . '/connect_db.php');
require_once(__DIR__ . '/header.php'); // Include the header

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
                    <button class="wishlist-icon <?= $in_wishlist ? 'filled' : '' ?>" data-product-id="<?= $p['product_id'] ?>">
                        <i class="bi bi-heart<?= $in_wishlist ? '-fill' : '' ?>"></i>
                    </button>
                </div>
                <?php endforeach; ?>
            </div>
            <button class="scroll-btn right" onclick="scrollRight(this)"><i class="bi bi-chevron-right"></i></button>
        </div>
    </div>
<?php endforeach; ?>
</div>

<style>
/* Minimal styling */
body { background:#f8f8f8; }
.category-section { margin-bottom:60px; }
.category-title { font-size:24px; margin-bottom:15px; }
.carousel-wrapper { position:relative; }
.scroll-container { display:flex; gap:15px; overflow-x:auto; scroll-behavior:smooth; padding-bottom:10px; }
.scroll-container::-webkit-scrollbar { display:none; }
.scroll-btn { display: none; position:absolute; top:40%; transform:translateY(-50%); background:rgba(255,255,255,0.8); border:none; padding:5px; border-radius:50%; cursor:pointer; z-index:10; }
.scroll-btn.left { left:-15px; }
.scroll-btn.right { right:-15px; }
.subcategories { display:flex; gap:10px; flex-wrap:nowrap; }
.subcategories a { flex: 0 0 auto; background-color: #FFFFE4; border: 1px solid #ddd; border-radius: 6px; padding: 6px 12px; font-size: 14px; color: #333; text-decoration: none; height:50px; line-height:1.1; display:flex; justify-content:center; align-items:center; transition: all 0.2s ease; }
.subcategories a:hover { background-color: #118997; color: #fff; }
.product-card, .product-item { flex:0 0 200px; border:1px solid #ddd; border-radius:8px; background:#fff; padding:10px; text-align:center; transition: transform 0.2s, background-color 0.2s, color 0.2s; position:relative; }
.product-card:hover, .product-item:hover { transform:scale(1.05); }
.product-card img, .product-item img { width:100%; height:150px; object-fit:cover; margin-bottom:10px; border-radius:4px; background:#fff; }
.product-card h3, .product-card p, .product-item h5, .product-item p { text-align:center; margin:5px 0; }
.wishlist-icon { position:absolute; top:10px; right:10px; background:none; border:none; cursor:pointer; transition: transform 0.2s, color 0.2s; }
.wishlist-icon i { font-size:20px; color:#dc3545; }
.wishlist-icon.filled i { color:#e60023; }
.wishlist-icon:hover { transform:scale(1.2); }
</style>

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
document.querySelectorAll('.wishlist-icon').forEach(btn=>{
    btn.addEventListener('click', function(e){
        e.preventDefault(); e.stopPropagation();
        const productId = this.dataset.productId;
        const icon = this.querySelector('i');
        <?php if(!$user_id): ?>
        window.location.href = '<?= BASE_URL ?>auth/login.php';
        return;
        <?php else: ?>
        const isFilled = this.classList.contains('filled');
        fetch('<?= BASE_URL ?>products-listing/wishlist_toggle.php', {
            method:'POST',
            headers:{ 'Content-Type':'application/json' },
            body: JSON.stringify({ product_id: productId, action: isFilled?'remove':'add' })
        })
        .then(res=>res.json())
        .then(data=>{
            if(data.success){
                this.classList.toggle('filled');
                icon.className = 'bi ' + (isFilled?'bi-heart':'bi-heart-fill');
                // update header wishlist count
                const counter = document.getElementById('wishlist-count');
                if(counter){
                    let count = parseInt(counter.textContent) || 0;
                    if(data.status === 'added') count++;
                    else if(data.status === 'removed' && count > 0) count--;
                    counter.textContent = count;
                }
            } else { alert('Failed to update wishlist.'); }
        });
        <?php endif; ?>
    });
});

// Live cart update
function updateCartCount() {
    <?php if($user_id): ?>
    fetch('<?= BASE_URL ?>cart/get_cart_info_inline.php') // we will use inline endpoint
    .then(res => res.json())
    .then(data => {
        if(data.success){
            const cartCountEl = document.getElementById('cart-count');
            const cartTotalEl = document.getElementById('cart-total');
            if(cartCountEl) cartCountEl.textContent = data.total_qty;
            if(cartTotalEl) cartTotalEl.textContent = 'RM ' + parseFloat(data.total_price).toFixed(2);
        }
    });
    <?php endif; ?>
}
</script>

<?php
// --- Inline endpoint for cart info (AJAX) ---
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])=='xmlhttprequest' && isset($_POST['action']) && $_POST['action']=='get_cart_info'){
    header('Content-Type: application/json');
    echo json_encode([
        'success'=>true,
        'total_qty'=>$cart_data['total_qty'],
        'total_price'=>$cart_data['total_price']
    ]);
    exit;
}
?>
