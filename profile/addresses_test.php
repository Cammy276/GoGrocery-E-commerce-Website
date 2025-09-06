<?php
// Database connection
$host = 'localhost';
$db   = 'gogrocery';
$user = 'gogrocery_customer';
$pass = 'StrongCustomerPassword123!';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Fetch ENUM values for state_territory dynamically
$enum_values = [];
$enum_result = $conn->query("SHOW COLUMNS FROM addresses LIKE 'state_territory'");
if ($enum_result) {
    $row = $enum_result->fetch_assoc();
    preg_match("/^enum\('(.*)'\)$/", $row['Type'], $matches);
    if (isset($matches[1])) $enum_values = explode("','", $matches[1]);
}

// Handle Insert
if (isset($_POST['insert'])) {
    $user_id = $_POST['user_id'];
    $label = $_POST['label'];
    $street = $_POST['street'];
    $apartment = $_POST['apartment'];
    $postcode = $_POST['postcode'];
    $city = $_POST['city'];
    $state_territory = $_POST['state_territory'];

    $stmt = $conn->prepare("INSERT INTO addresses (user_id, label, street, apartment, postcode, city, state_territory) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssss", $user_id, $label, $street, $apartment, $postcode, $city, $state_territory);
    $stmt->execute();
    $stmt->close();
}

// Handle Update
if (isset($_POST['update'])) {
    $address_id = $_POST['address_id'];
    $label = $_POST['label'];
    $street = $_POST['street'];
    $apartment = $_POST['apartment'];
    $postcode = $_POST['postcode'];
    $city = $_POST['city'];
    $state_territory = $_POST['state_territory'];

    $stmt = $conn->prepare("UPDATE addresses SET label=?, street=?, apartment=?, postcode=?, city=?, state_territory=? WHERE address_id=?");
    $stmt->bind_param("ssssssi", $label, $street, $apartment, $postcode, $city, $state_territory, $address_id);
    $stmt->execute();
    $stmt->close();
}

// Handle Delete
if (isset($_POST['delete'])) {
    $address_id = $_POST['address_id'];
    $stmt = $conn->prepare("DELETE FROM addresses WHERE address_id=?");
    $stmt->bind_param("i", $address_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch all addresses
$addresses = $conn->query("SELECT * FROM addresses");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Addresses Management</title>
    <style>
        table {border-collapse: collapse; width: 100%;}
        th, td {border: 1px solid #ccc; padding: 8px; text-align: left;}
        th {background-color: #f2f2f2;}
        input[type=text], select {width: 100%; padding: 6px;}
        .action-btn {margin: 2px;}
    </style>
</head>
<body>
<h2>Addresses Table</h2>

<table>
    <tr>
        <th>Address ID</th>
        <th>User ID</th>
        <th>Label</th>
        <th>Street</th>
        <th>Apartment</th>
        <th>Postcode</th>
        <th>City</th>
        <th>State/Territory</th>
        <th>Actions</th>
    </tr>
    <?php while($row = $addresses->fetch_assoc()): ?>
    <tr>
        <form method="POST">
            <td><?= $row['address_id'] ?></td>
            <td><?= $row['user_id'] ?></td>
            <td><input type="text" name="label" value="<?= $row['label'] ?>"></td>
            <td><input type="text" name="street" value="<?= $row['street'] ?>"></td>
            <td><input type="text" name="apartment" value="<?= $row['apartment'] ?>"></td>
            <td><input type="text" name="postcode" value="<?= $row['postcode'] ?>"></td>
            <td><input type="text" name="city" value="<?= $row['city'] ?>"></td>
            <td>
                <select name="state_territory">
                    <?php foreach($enum_values as $state): ?>
                        <option value="<?= $state ?>" <?= $state==$row['state_territory']?'selected':'' ?>><?= $state ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
            <td>
                <input type="hidden" name="address_id" value="<?= $row['address_id'] ?>">
                <button type="submit" name="update" class="action-btn">Update</button>
                <button type="submit" name="delete" class="action-btn" onclick="return confirm('Delete this address?')">Delete</button>
            </td>
        </form>
    </tr>
    <?php endwhile; ?>
</table>

<h2>Insert New Address</h2>
<form method="POST">
    <table>
        <tr>
            <td>User ID</td>
            <td><input type="text" name="user_id" required></td>
        </tr>
        <tr>
            <td>Label</td>
            <td><input type="text" name="label" required></td>
        </tr>
        <tr>
            <td>Street</td>
            <td><input type="text" name="street" required></td>
        </tr>
        <tr>
            <td>Apartment</td>
            <td><input type="text" name="apartment"></td>
        </tr>
        <tr>
            <td>Postcode</td>
            <td><input type="text" name="postcode" required></td>
        </tr>
        <tr>
            <td>City</td>
            <td><input type="text" name="city" required></td>
        </tr>
        <tr>
            <td>State/Territory</td>
            <td>
                <select name="state_territory" required>
                    <?php foreach($enum_values as $state): ?>
                        <option value="<?= $state ?>"><?= $state ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
    </table>
    <button type="submit" name="insert">Insert Address</button>
</form>

</body>
</html>
