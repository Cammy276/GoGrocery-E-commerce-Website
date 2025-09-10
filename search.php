<?php
include('./connect_db.php');

$q = $_GET['q'] ?? '';
$q = trim($q);

if (!$q) {
    die("Please enter a search keyword.");
}

// Products
$stmt = $conn->prepare("SELECT product_id, product_name, slug FROM products WHERE product_name LIKE CONCAT('%',?,'%')");
$stmt->bind_param("s", $q);
$stmt->execute();
$products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Brands
$stmt = $conn->prepare("SELECT brand_id, name, slug FROM brands WHERE name LIKE CONCAT('%',?,'%')");
$stmt->bind_param("s", $q);
$stmt->execute();
$brands = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Categories
$stmt = $conn->prepare("SELECT category_id, name, slug FROM categories WHERE name LIKE CONCAT('%',?,'%')");
$stmt->bind_param("s", $q);
$stmt->execute();
$categories = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<h1>Search Results for "<?= htmlspecialchars($q) ?>"</h1>

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
