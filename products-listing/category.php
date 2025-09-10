<?php
include(__DIR__ . '/../connect_db.php');

$slug = $_GET['slug'] ?? '';

if (!$slug) die("Category not specified.");

// --- Get category info ---
$stmt = $conn->prepare("SELECT category_id, name FROM categories WHERE slug = ?");
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

// --- Fetch products and their images under all these categories ---
$placeholders = implode(',', array_fill(0, count($all_category_ids), '?'));
$types = str_repeat('i', count($all_category_ids));

$sql = "SELECT p.*, pi.product_image_url, pi.alt_text
        FROM products p
        LEFT JOIN product_images pi ON p.product_id = pi.product_id
        WHERE p.category_id IN ($placeholders)";
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$all_category_ids);
$stmt->execute();
$products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($category['name']) ?></title>
<style>
body { font-family: Arial, sans-serif; background: #f8f8f8; margin:0; padding:20px; }
h1 { margin-bottom:20px; }

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 20px;
}

.product-item {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    overflow: hidden;
    text-align: center;
    transition: transform 0.2s, box-shadow 0.2s;
    cursor: pointer;
    display: flex;
    flex-direction: column;
    text-decoration: none;
    color: inherit;
}

.product-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.product-item img {
    width: 100%;
    height: 150px;
    object-fit: cover;
}

.product-item h3 {
    font-size: 16px;
    margin: 10px 0 5px;
}

.product-item p {
    font-size: 14px;
    color: #555;
    margin: 0 0 10px;
}
</style>
</head>
<body>

<h1><?= htmlspecialchars($category['name']) ?></h1>

<div class="products-grid">
<?php foreach ($products as $p): 
    $product_slug = $p['slug'] ?? $p['product_id']; 
    $product_link = "product.php?slug=" . urlencode($product_slug);
    $image_path = !empty($p['product_image_url']) ? $p['product_image_url'] : './images/products/placeholder.png';
    $alt_text = $p['alt_text'] ?? $p['product_name'];
?>
    <a class="product-item" href="<?= $product_link ?>">
        <img src="<?= htmlspecialchars($image_path) ?>" alt="<?= htmlspecialchars($alt_text) ?>">
        <h3><?= htmlspecialchars($p['product_name']) ?></h3>
        <p>RM <?= number_format($p['unit_price'], 2) ?> / <?= htmlspecialchars($p['weight_volume'] ?? '-') ?></p>
    </a>
<?php endforeach; ?>
</div>

</body>
</html>
