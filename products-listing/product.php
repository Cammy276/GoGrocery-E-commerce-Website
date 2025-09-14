<?php
// ======================== PHP DATA FETCH ========================
include(__DIR__ . '/../connect_db.php');
include(__DIR__ . '/../header.php');

$user_id = $_SESSION['user_id'] ?? null;

// --- Fetch cart totals ---
$cart_totals = ['total_qty' => 0, 'total_price' => 0.00];
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
    $cart_totals = $stmt->get_result()->fetch_assoc();
}

// --- Fetch product info ---
$slug = $_GET['slug'] ?? '';
if (!$slug) die("Product not specified.");

$stmt = $conn->prepare("
    SELECT p.*, pi.product_image_url, pi.alt_text, 
           c.name AS category_name, b.name AS brand_name, c.category_id AS product_category_id
    FROM products p
    LEFT JOIN product_images pi ON p.product_id = pi.product_id
    LEFT JOIN categories c ON p.category_id = c.category_id
    LEFT JOIN brands b ON p.brand_id = b.brand_id
    WHERE p.slug = ?
");
$stmt->bind_param("s", $slug);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
if (!$product) die("Product not found.");

// --- Get current quantity for this product ---
$current_qty = 1; 
if ($user_id && $product) {
    $stmt = $conn->prepare("SELECT quantity FROM cart_items WHERE user_id=? AND product_id=?");
    $stmt->bind_param("ii", $user_id, $product['product_id']);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    if ($result) $current_qty = intval($result['quantity']);
}

// --- Check if in wishlist ---
$in_wishlist = false;
if ($user_id && $product) {
    $stmt = $conn->prepare("SELECT 1 FROM wishlist WHERE user_id=? AND product_id=?");
    $stmt->bind_param("ii", $user_id, $product['product_id']);
    $stmt->execute();
    $in_wishlist = $stmt->get_result()->num_rows > 0;
}

// --- Helper function for image URL ---
if (!function_exists('getImageUrl')) {
    function getImageUrl($image_path){
        return !empty($image_path) ? BASE_URL . ltrim($image_path,'/') : BASE_URL . 'images/products/placeholder.png';
    }
}

// --- Find top-level category ---
$current_category_id = $product['product_category_id'];
$top_category_id = $current_category_id;
$last_valid_id = $current_category_id;

while ($top_category_id !== null) {
    $stmt = $conn->prepare("SELECT parent_id FROM categories WHERE category_id = ?");
    $stmt->bind_param("i", $top_category_id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();

    if ($res && $res['parent_id'] !== null) {
        $last_valid_id = $res['parent_id'];
        $top_category_id = $res['parent_id'];
    } else {
        break;
    }
}
$top_category_id = $last_valid_id ?? $current_category_id;

// --- Fetch all descendant categories of top-level ---
$descendant_ids = [$top_category_id];
$stmt = $conn->prepare("
    WITH RECURSIVE cat_tree AS (
        SELECT category_id FROM categories WHERE category_id = ?
        UNION ALL
        SELECT c.category_id
        FROM categories c
        INNER JOIN cat_tree ct ON c.parent_id = ct.category_id
    )
    SELECT category_id FROM cat_tree
");
$stmt->bind_param("i", $top_category_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) $descendant_ids[] = intval($row['category_id']);
$descendant_ids = array_unique($descendant_ids);

// --- Fetch recommended products ---
$recommended_products = [];
if (!empty($descendant_ids)) {
    $placeholders = implode(',', array_fill(0, count($descendant_ids), '?'));
    $types = str_repeat('i', count($descendant_ids) + 1);
    $sql = "
        SELECT p.*, pi.product_image_url, pi.alt_text, p.slug
        FROM products p
        LEFT JOIN product_images pi ON p.product_id = pi.product_id
        WHERE p.category_id IN ($placeholders) AND p.product_id != ?
        LIMIT 10
    ";
    $stmt = $conn->prepare($sql);
    $bind_params = array_merge($descendant_ids, [$product['product_id']]);
    $stmt->bind_param($types, ...$bind_params);
    $stmt->execute();
    $recommended_products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// --- Fetch user's wishlist items ---
$wishlist_items = [];
if ($user_id) {
    $res = $conn->query("SELECT product_id FROM wishlist WHERE user_id = $user_id");
    while ($row = $res->fetch_assoc()) $wishlist_items[] = $row['product_id'];
}
?>
<?php
include __DIR__ . '/../livechat/chat_UI.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($product['product_name']) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="../css/styles.css">
<link rel="stylesheet" href="../css/header_styles.css">
<link rel="stylesheet" href="../css/footer_styles.css">
<link rel="stylesheet" href="../css/product_styles.css">
</head>
<body>

<!-- ======================== PRODUCT LAYOUT ======================== -->
<div class="container product-container">
    <div class="product-images">
        <img src="<?= htmlspecialchars(getImageUrl($product['product_image_url'])) ?>" alt="<?= htmlspecialchars($product['alt_text'] ?? $product['product_name']) ?>">
    </div>

    <div class="product-info">
        <h1><?= htmlspecialchars($product['product_name']) ?></h1>
        <?php if(!empty($product['brand_name'])): ?><p><strong>Brand:</strong> <?= htmlspecialchars($product['brand_name']) ?></p><?php endif; ?>
        <?php if(!empty($product['category_name'])): ?><p><strong>Category:</strong> <?= htmlspecialchars($product['category_name']) ?></p><?php endif; ?>
        <p><strong>Weight/Volume:</strong> <?= htmlspecialchars($product['weight_volume'] ?? '-') ?></p>
        <p><strong>Price:</strong> RM <?= number_format($product['unit_price'],2) ?></p>
        <?php if(!empty($product['discount_percent'])): ?><p><strong>Discount:</strong> <?= htmlspecialchars($product['discount_percent']) ?>%</p><?php endif; ?>
        <?php if(!empty($product['special_offer_label'])): ?><p><strong>Special Offer:</strong> <?= htmlspecialchars($product['special_offer_label']) ?></p><?php endif; ?>
        <?php if(!empty($product['product_description'])): ?><p><strong>Description:</strong><br><?= nl2br(htmlspecialchars($product['product_description'])) ?></p><?php endif; ?>
        <?php if(!empty($product['nutritional_info'])): ?><p><strong>Nutritional Info:</strong><br><?= nl2br(htmlspecialchars($product['nutritional_info'])) ?></p><?php endif; ?>
        
        <!-- Cart Controls -->
        <div class="cart-controls">
            <div class="long-container qty-control">
                <button type="button" class="cart-minus">-</button>
                <input type="text" value="<?= $current_qty ?>" class="cart-qty">
                <button type="button" class="cart-plus">+</button>
            </div>

            <button class="cart-icon" data-product-id="<?= $product['product_id'] ?>">
                <p>Add to Cart</p>
                <i class="bi bi-cart-fill"></i>
            </button>

            <button class="action-icon wishlist-icon <?= $in_wishlist ? 'filled' : '' ?>" data-product-id="<?= $product['product_id'] ?>">
                <i class="bi bi-heart-fill"></i>
            </button>

            <button class="action-icon forward-icon" id="shareBtn">
                <i class="bi bi-share-fill"></i>
            </button>
        </div>

        <!-- Dark overlay -->
        <div class="overlay" id="overlay"></div>

        <!-- Share Modal -->
        <div class="share-modal" id="shareModal">
            <button class="close-btn" id="closeShare">&times;</button>
            <h3>Share this product</h3>

            <div class="social-icons share-product">
                <a href="https://wa.me/?text=<?= urlencode(BASE_URL.'products-listing/product.php?slug='.$product['slug']) ?>" target="_blank" title="WhatsApp"><i class="bi bi-whatsapp"></i></a>
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(BASE_URL.'products-listing/product.php?slug='.$product['slug']) ?>" target="_blank" title="Facebook"><i class="bi bi-facebook"></i></a>
                <a href="https://line.me/R/msg/text/?<?= urlencode(BASE_URL.'products-listing/product.php?slug='.$product['slug']) ?>" target="_blank" title="Line"><i class="bi bi-line"></i></a>
                <a href="https://twitter.com/intent/tweet?url=<?= urlencode(BASE_URL.'products-listing/product.php?slug='.$product['slug']) ?>" target="_blank" title="X (Twitter)"><i class="bi bi-twitter-x"></i></a>
                <a href="https://www.instagram.com/" target="_blank" title="Instagram"><i class="bi bi-instagram"></i></a>
                <a href="https://t.me/share/url?url=<?= urlencode(BASE_URL.'products-listing/product.php?slug='.$product['slug']) ?>" target="_blank" title="Telegram"><i class="bi bi-telegram"></i></a>
            </div>

            <div class="share-link">
                <input type="text" value="<?= BASE_URL.'products-listing/product.php?slug='.$product['slug'] ?>" id="shareLink" readonly>
                <button id="copyBtn">Copy Link</button>
            </div>
        </div>
    </div>
</div>
<!-- Recommended Products -->
<h2 style="margin-top:50px;">Recommended Products</h2>
<div class="recommended-products" style="display:flex; flex-wrap:wrap; gap:20px; margin-top:20px;">
    <?php if (!empty($recommended_products)): ?>
        <?php foreach ($recommended_products as $rec):
            $rec_slug = $rec['slug'] ?? $rec['product_id'];
            $rec_link = "product.php?slug=" . urlencode($rec_slug);
            $rec_image = getImageUrl($rec['product_image_url']);
            $rec_alt = $rec['alt_text'] ?? $rec['product_name'];
            $in_wishlist = in_array($rec['product_id'], $wishlist_items);
        ?>
            <div class="product-card" style="width:180px; border:1px solid #ddd; border-radius:8px; padding:10px; text-align:center;">
                <a href="<?= $rec_link ?>" style="text-decoration:none; color:inherit;">
                    <img src="<?= htmlspecialchars($rec_image) ?>" alt="<?= htmlspecialchars($rec_alt) ?>" style="width:100%; height:150px; object-fit:contain; border-bottom:1px solid #eee; margin-bottom:8px;">
                    <h3 style="font-size:14px; margin:0 0 5px 0;"><?= htmlspecialchars($rec['product_name']) ?></h3>
                    <p style="font-size:13px; margin:0;">RM <?= number_format($rec['unit_price'],2) ?></p>
                </a>
                <button class="wishlist-icon <?= $in_wishlist ? 'filled' : '' ?>" data-product-id="<?= $rec['product_id'] ?>" style="border:none; background:none; cursor:pointer; margin-top:5px;">
                    <i class="bi <?= $in_wishlist ? 'bi-heart-fill' : 'bi-heart' ?>"></i>
                </button>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="font-style:italic;">No recommended products available.</p>
    <?php endif; ?>
</div>

<!-- Toast -->
<div class="toast" id="toast"></div>

<!-- ======================== JS SCRIPTS ======================== -->
<script>
// ======================== TOAST ==========================
function showToast(message, duration = 2000) {
    const toast = document.getElementById("toast");
    toast.textContent = message;
    toast.classList.add("show");
    setTimeout(() => { toast.classList.remove("show"); }, duration);
}

// ======================== CART ==========================
const qtyInput = document.querySelector('.cart-qty');
const minusBtn = document.querySelector('.cart-minus');
const plusBtn = document.querySelector('.cart-plus');
const cartBtn = document.querySelector('.cart-icon');
const productId = cartBtn.dataset.productId;

function updateCart(qty) {
    fetch('<?= BASE_URL ?>products-listing/update_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ product_id: productId, action: qty > 0 ? "set" : "remove", quantity: qty })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast(data.message || (qty > 0 ? "Cart updated" : "Item removed"));
            const cartBadge = document.getElementById('cart-count');
            if (cartBadge && data.total_qty !== undefined) cartBadge.textContent = data.total_qty;
            const cartTotal = document.getElementById('cart-total');
            if (cartTotal && data.total_price !== undefined) cartTotal.textContent = "RM " + parseFloat(data.total_price).toFixed(2);
        } else showToast("Failed to update cart");
    });
}

minusBtn.addEventListener('click', () => { qtyInput.value = Math.max(0, (parseInt(qtyInput.value) || 0) - 1); });
plusBtn.addEventListener('click', () => { qtyInput.value = (parseInt(qtyInput.value) || 0) + 1; });
cartBtn.addEventListener('click', () => { updateCart(parseInt(qtyInput.value) || 0); });
qtyInput.addEventListener('input', () => { let val = parseInt(qtyInput.value); if (isNaN(val) || val < 0) qtyInput.value = 0; else qtyInput.value = val; });

// ======================== WISHLIST ==========================
document.querySelectorAll('.wishlist-icon').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const productId = this.dataset.productId;
        <?php if(!$user_id): ?>
            window.location.href = '<?= BASE_URL ?>auth/login.php';
            return;
        <?php else: ?>
            const isFilled = this.classList.contains('filled');
            fetch('<?= BASE_URL ?>products-listing/wishlist_toggle.php', {
                method:'POST',
                headers:{'Content-Type':'application/json'},
                body: JSON.stringify({ product_id: productId, action: isFilled ? 'remove' : 'add' })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    this.classList.toggle('filled');
                    showToast(data.status === 'added' ? "Added to Wishlist" : "Removed from Wishlist");
                    const counter = document.getElementById('wishlist-count');
                    if (counter) {
                        let count = parseInt(counter.textContent) || 0;
                        if (data.status === 'added') count++;
                        else if (data.status === 'removed' && count > 0) count--;
                        counter.textContent = count;
                    }
                } else showToast("Failed to update wishlist");
            })
            .catch(() => showToast("Error updating wishlist"));
        <?php endif; ?>
    });
});

// ======================== SHARE ==========================
document.getElementById("shareBtn").addEventListener("click", () => { document.getElementById("shareModal").style.display = "block"; });
document.getElementById("closeShare").addEventListener("click", () => { document.getElementById("shareModal").style.display = "none"; });
window.addEventListener("click", (e) => { if(e.target === document.getElementById("shareModal")) document.getElementById("shareModal").style.display = "none"; });
document.getElementById("copyBtn").addEventListener("click", () => {
    const linkInput = document.getElementById("shareLink");
    linkInput.select();
    linkInput.setSelectionRange(0, 99999);
    document.execCommand("copy");
    showToast("Link copied!");
});
</script>

<?php include '../footer.php'; ?>
</body>
</html>