<?php
include(__DIR__ . '/../connect_db.php');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $user_id = $_POST['user_id'];
    $address_id = $_POST['address_id'];
    $voucher_id = $_POST['voucher_id'] ?: null; // optional
    $payment_method = $_POST['payment_method'];
    $delivery_duration = $_POST['delivery_duration'] ?: null;

    // Insert order
    if (isset($_POST['place_order'])) {

        // Calculate subtotal and discount_total from selected products
        $products = $_POST['products']; // array: product_id => quantity
        $subtotal = 0;
        $discount_total = 0;

        foreach ($products as $product_id => $qty) {
            $p = $conn->query("SELECT unit_price, discount_percent FROM products WHERE product_id=$product_id")->fetch_assoc();
            $unit_price = $p['unit_price'];
            $line_discount = $p['discount_percent'] ? $unit_price * ($p['discount_percent']/100) * $qty : 0;
            $subtotal += $unit_price * $qty;
            $discount_total += $line_discount;
        }

        // Apply voucher if exists
        if ($voucher_id) {
            $voucher = $conn->query("SELECT discount_type, discount_value, max_discount FROM vouchers WHERE voucher_id=$voucher_id")->fetch_assoc();
            if ($voucher['discount_type'] === 'PERCENT') {
                $voucher_discount = $subtotal * ($voucher['discount_value']/100);
                if ($voucher['max_discount'] !== null) $voucher_discount = min($voucher_discount, $voucher['max_discount']);
            } else {
                $voucher_discount = $voucher['discount_value'];
            }
            $discount_total += $voucher_discount;
        }

        $shipping_fee = 5.00; // fixed shipping for example
        $grand_total = $subtotal - $discount_total + $shipping_fee;

        // Insert into orders table
        $stmt = $conn->prepare("INSERT INTO orders (user_id, address_id, status, payment_method, voucher_id, subtotal, discount_total, shipping_fee, delivery_duration) VALUES (?,?,?,?,?,?,?,?,?)");
        $status = 'paid';
        $stmt->bind_param("iissiddds", $user_id, $address_id, $status, $payment_method, $voucher_id, $subtotal, $discount_total, $shipping_fee, $delivery_duration);
        $stmt->execute();
        $order_id = $stmt->insert_id;

        // Insert order_items
        foreach ($products as $product_id => $qty) {
            $p = $conn->query("SELECT product_name, unit_price, discount_percent, sku FROM products WHERE product_id=$product_id")->fetch_assoc();
            $unit_price = $p['unit_price'];
            $line_discount = $p['discount_percent'] ? $unit_price * ($p['discount_percent']/100) * $qty : 0;

            $stmt2 = $conn->prepare("INSERT INTO order_items (order_id, product_id, product_name, sku, unit_price, quantity, line_discount) VALUES (?,?,?,?,?,?,?)");
            $stmt2->bind_param("iisdiid", $order_id, $product_id, $p['product_name'], $p['sku'], $unit_price, $qty, $line_discount);
            $stmt2->execute();
        }

        // Record voucher usage
        if ($voucher_id) {
            $stmt3 = $conn->prepare("INSERT INTO voucher_usages (voucher_id, user_id) VALUES (?, ?)");
            $stmt3->bind_param("ii", $voucher_id, $user_id);
            $stmt3->execute();
        }

        echo "<p>Order placed successfully!</p>";
    }

    // Delete order
    if (isset($_POST['delete_order'])) {
        $order_id = $_POST['order_id'];
        $conn->query("DELETE FROM orders WHERE order_id=$order_id");
    }
}

// Fetch users, addresses, products, vouchers
$users = $conn->query("SELECT * FROM users");
$products = $conn->query("SELECT * FROM products");
$addresses = $conn->query("SELECT * FROM addresses");
$vouchers_all = $conn->query("SELECT * FROM vouchers WHERE is_active=1");

// Fetch existing orders
$orders = $conn->query("SELECT o.*, u.name AS user_name, a.street AS address_street, v.code AS voucher_code
                        FROM orders o
                        LEFT JOIN users u ON o.user_id = u.user_id
                        LEFT JOIN addresses a ON o.address_id = a.address_id
                        LEFT JOIN vouchers v ON o.voucher_id = v.voucher_id");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Orders & Order Items</title>
    <style>
        table { border-collapse: collapse; width: 100%; margin-top: 20px;}
        th, td { border:1px solid #ccc; padding:8px; text-align:left; vertical-align:top;}
        input, select { width: 100%; }
        form { display: inline; }
        button { padding:5px 10px; }
    </style>
</head>
<body>
<h2>Place New Order</h2>
<form method="POST">
    <label>User:</label>
    <select name="user_id" required>
        <option value="">--Select User--</option>
        <?php while($u=$users->fetch_assoc()): ?>
            <option value="<?= $u['user_id'] ?>"><?= htmlspecialchars($u['name']) ?></option>
        <?php endwhile; ?>
    </select><br>

    <label>Address:</label>
    <select name="address_id" required>
        <option value="">--Select Address--</option>
        <?php
        $addresses->data_seek(0);
        while($a=$addresses->fetch_assoc()):
        ?>
            <option value="<?= $a['address_id'] ?>"><?= htmlspecialchars($a['street'].', '.$a['city']) ?></option>
        <?php endwhile; ?>
    </select><br>

    <label>Products:</label>
    <table>
        <tr><th>Product</th><th>Unit Price</th><th>Quantity</th></tr>
        <?php
        $products->data_seek(0);
        while($p=$products->fetch_assoc()):
        ?>
            <tr>
                <td><?= htmlspecialchars($p['product_name']) ?></td>
                <td><?= $p['unit_price'] ?></td>
                <td><input type="number" name="products[<?= $p['product_id'] ?>]" value="0" min="0"></td>
            </tr>
        <?php endwhile; ?>
    </table><br>

    <label>Voucher:</label>
    <select name="voucher_id">
        <option value="">--No Voucher--</option>
        <?php
        $vouchers_all->data_seek(0);
        while($v=$vouchers_all->fetch_assoc()):
            $used = $conn->query("SELECT * FROM voucher_usages WHERE voucher_id={$v['voucher_id']} AND user_id=".(int)($_POST['user_id'] ?? 0))->num_rows;
            if ($used) continue; // skip used vouchers
        ?>
            <option value="<?= $v['voucher_id'] ?>"><?= $v['code'] ?></option>
        <?php endwhile; ?>
    </select><br>

    <label>Payment Method:</label>
    <select name="payment_method" required>
        <option value="card">Card</option>
        <option value="bank_transfer">Bank Transfer</option>
        <option value="e_wallet">E-wallet</option>
        <option value="grabpay">GrabPay</option>
        <option value="fpx">FPX</option>
    </select><br>

    <label>Delivery Duration:</label>
    <input type="text" name="delivery_duration"><br>

    <button type="submit" name="place_order">Place Order</button>
</form>

<h2>Existing Orders</h2>
<table>
<tr>
    <th>Order ID</th><th>User</th><th>Address</th><th>Voucher</th><th>Subtotal</th><th>Discount</th><th>Shipping</th><th>Grand Total</th><th>Action</th>
</tr>
<?php while($o=$orders->fetch_assoc()): ?>
<tr>
    <td><?= $o['order_id'] ?></td>
    <td><?= htmlspecialchars($o['user_name']) ?></td>
    <td><?= htmlspecialchars($o['address_street']) ?></td>
    <td><?= htmlspecialchars($o['voucher_code'] ?: '-') ?></td>
    <td><?= $o['subtotal'] ?></td>
    <td><?= $o['discount_total'] ?></td>
    <td><?= $o['shipping_fee'] ?></td>
    <td><?= $o['grand_total'] ?></td>
    <td>
        <form method="POST">
            <input type="hidden" name="order_id" value="<?= $o['order_id'] ?>">
            <button type="submit" name="delete_order" onclick="return confirm('Delete this order?')">Delete</button>
        </form>
    </td>
</tr>
<tr>
    <td colspan="9">
        <strong>Items:</strong>
        <ul>
        <?php
        $items = $conn->query("SELECT * FROM order_items WHERE order_id=".$o['order_id']);
        while($i=$items->fetch_assoc()):
            echo "<li>{$i['product_name']} ({$i['quantity']} x {$i['unit_price']}) - Discount: {$i['line_discount']}</li>";
        endwhile;
        ?>
        </ul>
    </td>
</tr>
<?php endwhile; ?>
</table>
</body>
</html>
