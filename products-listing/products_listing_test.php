<?php
include(__DIR__ . '/../connect_db.php');

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // INSERT product
    if (isset($_POST['insert'])) {
        $sku = $_POST['sku'];
        $name = $_POST['product_name'];
        $brand_id = $_POST['brand_id'] ?: null;
        $category_id = $_POST['category_id'] ?: null;
        $weight = $_POST['weight_volume'] ?: null;
        $price = $_POST['unit_price'];
        $desc = $_POST['product_description'] ?: null;
        $nutrition = $_POST['nutritional_info'] ?: null;
        $discount = $_POST['discount_percent'] ?: null;
        $offer = $_POST['special_offer_label'] ?: null;
        $img_url = $_POST['product_image_url'] ?: null;
        $alt_text = $_POST['alt_text'] ?: null;

        $stmt = $conn->prepare("INSERT INTO products 
            (sku, product_name, brand_id, category_id, weight_volume, unit_price, product_description, nutritional_info, discount_percent, special_offer_label)
            VALUES (?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("ssiiddssds", $sku, $name, $brand_id, $category_id, $weight, $price, $desc, $nutrition, $discount, $offer);
        $stmt->execute();
        $product_id = $stmt->insert_id;

        if ($img_url) {
            $stmt2 = $conn->prepare("INSERT INTO product_images (product_id, product_image_url, alt_text) VALUES (?,?,?)");
            $stmt2->bind_param("iss", $product_id, $img_url, $alt_text);
            $stmt2->execute();
        }
    }

    // UPDATE product
    if (isset($_POST['update'])) {
        $id = $_POST['product_id'];
        $sku = $_POST['sku'];
        $name = $_POST['product_name'];
        $brand_id = $_POST['brand_id'] ?: null;
        $category_id = $_POST['category_id'] ?: null;
        $weight = $_POST['weight_volume'] ?: null;
        $price = $_POST['unit_price'];
        $desc = $_POST['product_description'] ?: null;
        $nutrition = $_POST['nutritional_info'] ?: null;
        $discount = $_POST['discount_percent'] ?: null;
        $offer = $_POST['special_offer_label'] ?: null;
        $img_url = $_POST['product_image_url'] ?: null;
        $alt_text = $_POST['alt_text'] ?: null;

        $stmt = $conn->prepare("UPDATE products SET sku=?, product_name=?, brand_id=?, category_id=?, weight_volume=?, unit_price=?, product_description=?, nutritional_info=?, discount_percent=?, special_offer_label=? WHERE product_id=?");
        $stmt->bind_param("ssiiddssdsi", $sku, $name, $brand_id, $category_id, $weight, $price, $desc, $nutrition, $discount, $offer, $id);
        $stmt->execute();

        // Update or insert image
        $imgCheck = $conn->query("SELECT * FROM product_images WHERE product_id=$id");
        if ($imgCheck->num_rows > 0) {
            $stmt2 = $conn->prepare("UPDATE product_images SET product_image_url=?, alt_text=? WHERE product_id=?");
            $stmt2->bind_param("ssi", $img_url, $alt_text, $id);
            $stmt2->execute();
        } else if ($img_url) {
            $stmt2 = $conn->prepare("INSERT INTO product_images (product_id, product_image_url, alt_text) VALUES (?,?,?)");
            $stmt2->bind_param("iss", $id, $img_url, $alt_text);
            $stmt2->execute();
        }
    }

    // DELETE product
    if (isset($_POST['delete'])) {
        $id = $_POST['product_id'];
        $stmt = $conn->prepare("DELETE FROM products WHERE product_id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
}

// Fetch products with images
$products = $conn->query("SELECT p.*, pi.product_image_url, pi.alt_text, b.name AS brand_name, c.name AS category_name 
                          FROM products p
                          LEFT JOIN product_images pi ON p.product_id = pi.product_id
                          LEFT JOIN brands b ON p.brand_id = b.brand_id
                          LEFT JOIN categories c ON p.category_id = c.category_id");

$brands = $conn->query("SELECT brand_id, name FROM brands");
$categories = $conn->query("SELECT category_id, name FROM categories");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Products & Images CRUD</title>
    <p>Can only view the product details & image</p>
    <style>
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; vertical-align: top; }
        input, select { width: 100%; }
        form { display: inline; }
        button { padding: 5px 10px; }
        img { max-width: 100px; height: auto; display:block; margin-top:5px; }
    </style>
</head>
<body>
<h2>Products & Images</h2>

<h3>Add New Product</h3>
<form method="POST">
    <input type="text" name="sku" placeholder="SKU" required>
    <input type="text" name="product_name" placeholder="Product Name" required>
    <select name="brand_id">
        <option value="">--Select Brand--</option>
        <?php while ($b = $brands->fetch_assoc()) {
            echo "<option value='{$b['brand_id']}'>{$b['name']}</option>";
        } ?>
    </select>
    <select name="category_id">
        <option value="">--Select Category--</option>
        <?php
        $categories->data_seek(0);
        while ($c = $categories->fetch_assoc()) {
            echo "<option value='{$c['category_id']}'>{$c['name']}</option>";
        }
        ?>
    </select>
    <input type="text" name="weight_volume" placeholder="Weight/Volume">
    <input type="text" name="unit_price" placeholder="Unit Price" required>
    <input type="text" name="product_description" placeholder="Description">
    <input type="text" name="nutritional_info" placeholder="Nutritional Info">
    <input type="text" name="discount_percent" placeholder="Discount %">
    <input type="text" name="special_offer_label" placeholder="Special Offer Label">
    <input type="text" name="product_image_url" placeholder="Image URL">
    <input type="text" name="alt_text" placeholder="Alt Text">
    <button type="submit" name="insert">Insert</button>
</form>

<h3>Existing Products</h3>
<table>
<tr>
    <th>ID</th><th>SKU</th><th>Name</th><th>Brand</th><th>Category</th><th>Weight</th><th>Price</th><th>Description</th>
    <th>Nutrition</th><th>Discount %</th><th>Offer</th><th>Image</th><th>Actions</th>
</tr>
<?php while ($p = $products->fetch_assoc()) { ?>
<tr>
<form method="POST">
    <td><?php echo $p['product_id']; ?><input type="hidden" name="product_id" value="<?php echo $p['product_id']; ?>"></td>
    <td><input type="text" name="sku" value="<?php echo $p['sku']; ?>"></td>
    <td><input type="text" name="product_name" value="<?php echo $p['product_name']; ?>"></td>
    <td>
        <select name="brand_id">
            <option value="">--None--</option>
            <?php
            $brands->data_seek(0);
            while ($b = $brands->fetch_assoc()) {
                $sel = ($p['brand_id']==$b['brand_id']) ? 'selected' : '';
                echo "<option value='{$b['brand_id']}' $sel>{$b['name']}</option>";
            }
            ?>
        </select>
    </td>
    <td>
        <select name="category_id">
            <option value="">--None--</option>
            <?php
            $categories->data_seek(0);
            while ($c = $categories->fetch_assoc()) {
                $sel = ($p['category_id']==$c['category_id']) ? 'selected' : '';
                echo "<option value='{$c['category_id']}' $sel>{$c['name']}</option>";
            }
            ?>
        </select>
    </td>
    <td><input type="text" name="weight_volume" value="<?php echo $p['weight_volume']; ?>"></td>
    <td><input type="text" name="unit_price" value="<?php echo $p['unit_price']; ?>"></td>
    <td><input type="text" name="product_description" value="<?php echo $p['product_description']; ?>"></td>
    <td><input type="text" name="nutritional_info" value="<?php echo $p['nutritional_info']; ?>"></td>
    <td><input type="text" name="discount_percent" value="<?php echo $p['discount_percent']; ?>"></td>
    <td><input type="text" name="special_offer_label" value="<?php echo $p['special_offer_label']; ?>"></td>
    <td>
        <input type="text" name="product_image_url" value="<?php echo $p['product_image_url']; ?>"><br>
        <input type="text" name="alt_text" value="<?php echo $p['alt_text']; ?>"><br>
        <?php 
        // Display the image exactly as stored in the database; fallback if empty
        $img_path = $p['product_image_url'] ?: 'images/default.png';
        echo '<img src="' . htmlspecialchars($img_path) . '" alt="' . htmlspecialchars($p['alt_text'] ?: 'No image') . '">';
        ?>
    </td>
    <td>
        <button type="submit" name="update">Update</button>
        <button type="submit" name="delete" onclick="return confirm('Delete product?')">Delete</button>
    </td>
</form>
</tr>
<?php } ?>
</table>
</body>
</html>
