<?php
include('./connect_db.php');

$q = $_GET['q'] ?? '';
$q = trim($q);

$brand = $_GET['brand'] ?? '';
$category = $_GET['category'] ?? '';
$min_price = $_GET['min_price'] ?? '';
$max_price = $_GET['max_price'] ?? '';
$discount_only = $_GET['discount'] ?? '';
$sort = $_GET['sort'] ?? '';

$sql = "
    SELECT p.product_id, p.product_name, p.slug, p.unit_price, p.discount_percent, p.special_offer_label, 
            pi.product_image_url, b.name AS brand_name, c.name AS category_name 
    FROM products p
    LEFT JOIN product_images pi ON p.product_id = pi.product_id
    LEFT JOIN brands b ON p.brand_id = b.brand_id
    LEFT JOIN categories c ON p.category_id = c.category_id
    WHERE 1=1
";

// Parameters array
$params = [];
$types = "";

// Search keyword
if (!empty($q)) {
    $sql .= " AND p.product_name LIKE CONCAT('%', ?, '%')";
    $params[] = $q;
    $types .= "s";
}

// Filter: brand
if (!empty($brand)) {
    $sql .= " AND p.brand_id = ?";
    $params[] = $brand;
    $types .= "i";
}

// Filter: category
if (!empty($category)) {
    $sql .= " AND p.category_id = ?";
    $params[] = $category;
    $types .= "i";
}

// Filter: price range
if (!empty($min_price)) {
    $sql .= " AND p.unit_price >= ?";
    $params[] = $min_price;
    $types .= "d";
}
if (!empty($max_price)) {
    $sql .= " AND p.unit_price <= ?";
    $params[] = $max_price;
    $types .= "d";
}

// Filter: discount only
if($discount_only === "1") {
    $sql .= " AND p.discount_percent > 0";
}

// Sorting
switch ($sort) {
    case "price_asc":
        $sql .= " ORDER BY p.unit_price ASC";
        break;
    case "price_desc":
        $sql .= " ORDER BY p.unit_price DESC";
        break;
    case "newest":
        $sql .= " ORDER BY p.created_at DESC";
        break;
    default:
        $sql .= " ORDER BY p.product_name ASC";
}

// Prepare and execute
$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

?>

<h1>Search Results for "<?= htmlspecialchars($q) ?>"</h1>

<?php if (empty($results)): ?>
    <p>No product found.</p>
<?php else: ?>
    <?php foreach ($results as $p): ?>
        <div class="product-card">
            <img src="<?= htmlspecialchars($p['product_image_url'] ?? '/images/no-image.png') ?>"
                alt="<?= htmlspecialchars($p['product_name']) ?>" width="100">
            <h3><a href="product.php?id=<?= $p['product_id'] ?>">
                <?= htmlspecialchars($p['product_name']) ?>
            </a></h3>
            <p>Brand: <?= htmlspecialchars($p['brand_name'] ?? '-') ?></p>
            <p>Category: <?= htmlspecialchars($p['category_name'] ?? '-') ?></p>
            <p>Price: RM <?= number_format($p['unit_price'], 2) ?></p>
            <?php if (!empty($p['discount_percent'])): ?>
                <p class="discount">Discount: <?= $p['discount_percent'] ?>%</p>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>


<h2>Products</h2>
<?php foreach ($products as $p): ?>
    <div><a href="product.php?id=<?= $p['product_id'] ?>"><?= htmlspecialchars($p['product_name']) ?></a></div>
<?php endforeach; ?>

<h2>Brands</h2>
<?php foreach ($brands as $b): ?>
    <div><a href="brand.php?slug=<?= urlencode($b['slug']) ?>"><?= htmlspecialchars($b['name']) ?></a></div>
<?php endforeach; ?>

<h2>Categories</h2>
<?php foreach ($categories as $c): ?>
    <div><a href="category.php?slug=<?= urlencode($c['slug']) ?>"><?= htmlspecialchars($c['name']) ?></a></div>
<?php endforeach; ?>
