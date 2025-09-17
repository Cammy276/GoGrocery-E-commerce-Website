<?php
include('./connect_db.php');

$q = $_GET['q'] ?? '';
$q = trim($q);

if (!$q) {
    die("Please enter a search keyword.");
}

// ------------------- Products -------------------
$stmt = $conn->prepare("
    SELECT product_name, slug, product_description 
    FROM products 
    WHERE product_name LIKE CONCAT('%', ?, '%')
       OR slug LIKE CONCAT('%', ?, '%')
       OR product_description LIKE CONCAT('%', ?, '%')
");
$stmt->bind_param("sss", $q, $q, $q);
$stmt->execute();
$products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// ------------------- Brands -------------------
$stmt = $conn->prepare("
    SELECT name, slug 
    FROM brands 
    WHERE name LIKE CONCAT('%', ?, '%')
       OR slug LIKE CONCAT('%', ?, '%')
");
$stmt->bind_param("ss", $q, $q);
$stmt->execute();
$brands = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// ------------------- Categories -------------------
$stmt = $conn->prepare("
    SELECT name, slug 
    FROM categories 
    WHERE name LIKE CONCAT('%', ?, '%')
       OR slug LIKE CONCAT('%', ?, '%')
");
$stmt->bind_param("ss", $q, $q);
$stmt->execute();
$categories = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Results for "<?= htmlspecialchars($q) ?>"</title>
    <link rel="stylesheet" href="./css/styles.css">
    <link rel="stylesheet" href="./css/search_styles.css">
    <link rel="stylesheet" href="./css/header_styles.css">
    <link rel="stylesheet" href="./css/footer_styles.css">
    <link rel="stylesheet" href="./css/search_styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
<header>
    <?php include 'header.php'; ?>
</header>
</br>
<h1>Search Results for "<?= htmlspecialchars($q) ?>"</h1>
</br>
<div class="result-section">
    </br>
    <h2>Products</h2>
    <?php if ($products): ?>
        <?php foreach ($products as $p): ?>
            <div class="result-card">
                <a href="<?= BASE_URL ?>products-listing/product.php?slug=<?= str_replace(' ', '_', $p['slug']) ?>">
                    <?= htmlspecialchars($p['product_name']) ?>
                </a>
                <?php if ($p['product_description']): ?>
                    <p class="description"><?= htmlspecialchars($p['product_description']) ?></p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="no-result">No result found.</p>
    <?php endif; ?>
</div>

<div class="result-section">
    </br>
    <h2>Brands</h2>
    <?php if ($brands): ?>
        <?php foreach ($brands as $b): ?>
            <div class="result-card">
                <a href="<?= BASE_URL ?>brand.php?slug=<?= str_replace(' ', '_', $b['slug']) ?>">
                    <?= htmlspecialchars($b['name']) ?>
                </a>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="no-result">No result found.</p>
    <?php endif; ?>
</div>

<div class="result-section">
    </br>
    <h2>Categories</h2>
    <?php if ($categories): ?>
        <?php foreach ($categories as $c): ?>
            <div class="result-card">
                <a href="<?= BASE_URL ?>products-listing/category.php?slug=<?= str_replace(' ', '_', $c['slug']) ?>">
                    <?= htmlspecialchars($c['name']) ?>
                </a>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="no-result">No result found.</p>
    <?php endif; ?>
</div>
</body>
<?php include 'footer.php'; ?>
</html>