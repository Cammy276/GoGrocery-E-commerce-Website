<?php

// Fetch top-level categories (parent_id IS NULL)
$topCategories = $conn->query("SELECT category_id, name, slug FROM categories WHERE parent_id IS NULL LIMIT 7");

// Prepare array to hold subcategories and products
$categoryData = [];

while ($cat = $topCategories->fetch_assoc()) {
    $catId = $cat['category_id'];

    // Fetch subcategories
    $subcatsResult = $conn->query("SELECT category_id, name, slug FROM categories WHERE parent_id = $catId");
    $subcategories = $subcatsResult->fetch_all(MYSQLI_ASSOC);

    // Fetch products for this top category (all products directly under this top category)
    $sqlProducts = "
        SELECT p.product_id, p.product_name, p.unit_price, p.slug, pi.product_image_url
        FROM products p
        LEFT JOIN product_images pi ON p.product_id = pi.product_id
        WHERE p.category_id = $catId
        ORDER BY p.created_at DESC
    ";
    $productsResult = $conn->query($sqlProducts);
    $products = $productsResult->fetch_all(MYSQLI_ASSOC);

    $categoryData[] = [
        'category_id' => $catId,
        'category_name' => $cat['name'],
        'category_slug' => $cat['slug'],
        'subcategories' => $subcategories,
        'products' => $products
    ];
}

// Reset pointer
$topCategories->data_seek(0);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Categories & Products</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/bootstrap.min.css">
    <style>
        body { background: #f8f8f8; }
        .top-categories { overflow-x: auto; white-space: nowrap; padding: 15px 0; margin-bottom: 30px; }
        .top-categories a { display: inline-block; margin-right: 10px; }
        .category-section { margin-bottom: 50px; }
        .category-title { font-size: 24px; margin-bottom: 10px; }
        .subcategories { margin-bottom: 15px; }
        .subcategories a { margin-right: 10px; }
        .product-card { border: 1px solid #ddd; padding: 10px; border-radius: 8px; text-align: center; transition: transform 0.2s; cursor: pointer; background: #fff; }
        .product-card:hover { transform: scale(1.05); }
        .product-card img { width: 100%; height: 150px; object-fit: cover; margin-bottom: 10px; }
    </style>
</head>
<body>
<div class="container my-4">

    <!-- Top Categories -->
    <div class="top-categories">
        <?php foreach ($categoryData as $cat): ?>
            <a href="<?= BASE_URL ?>products-listing/category.php?slug=<?= $cat['category_slug'] ?>" class="btn btn-outline-primary"><?= htmlspecialchars($cat['category_name']) ?></a>
        <?php endforeach; ?>
    </div>

    <!-- Categories & Products -->
    <?php foreach ($categoryData as $cat): ?>
        <div class="category-section">
            <h3 class="category-title">
                <a href="<?= BASE_URL ?>products-listing/category.php?slug=<?= $cat['category_slug'] ?>"><?= htmlspecialchars($cat['category_name']) ?></a>
            </h3>

            <!-- Subcategories -->
            <?php if (!empty($cat['subcategories'])): ?>
                <div class="subcategories">
                    <?php foreach ($cat['subcategories'] as $subcat): ?>
                        <a href="<?= BASE_URL ?>products-listing/category.php?slug=<?= $subcat['slug'] ?>" class="btn btn-sm btn-outline-secondary"><?= htmlspecialchars($subcat['name']) ?></a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Products under top category -->
            <div class="row">
                <?php if (!empty($cat['products'])): ?>
                    <?php foreach ($cat['products'] as $product): ?>
                        <div class="col-6 col-md-3 mb-4">
                            <div class="product-card" onclick="location.href='<?= BASE_URL ?>products-listing/category.php?slug=<?= $cat['category_slug'] ?>'">
                                <img src="<?= htmlspecialchars(!empty($product['product_image_url']) ? BASE_URL . ltrim($product['product_image_url'], '/') : BASE_URL . 'images/default-product.png') ?>" alt="<?= htmlspecialchars($product['product_name']) ?>">
                                <p>RM <?= number_format($product['unit_price'], 2) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <p>No products available in this category.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>

</div>
</body>
</html>
