
<?php

// Include the database connection
include(__DIR__ . '/../connect_db.php');

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // INSERT
    if (isset($_POST['insert'])) {
        $name  = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $password_hash = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $profile_image_url = trim($_POST['profile_image_url']);

        if ($profile_image_url === '') {
            // omit column to use default
            $stmt = $conn->prepare("INSERT INTO users (name,email,phone,password_hash) VALUES (?,?,?,?)");
            $stmt->bind_param("ssss", $name, $email, $phone, $password_hash);
        } else {
            $stmt = $conn->prepare("INSERT INTO users (name,email,phone,profile_image_url,password_hash) VALUES (?,?,?,?,?)");
            $stmt->bind_param("sssss", $name, $email, $phone, $profile_image_url, $password_hash);
        }

        $stmt->execute();
    }

    // UPDATE
    if (isset($_POST['update'])) {
        $user_id = $_POST['user_id'];
        $name    = $_POST['name'];
        $email   = $_POST['email'];
        $phone   = $_POST['phone'];
        $profile_image_url = trim($_POST['profile_image_url']);

        if ($profile_image_url === '') {
            $stmt = $conn->prepare("UPDATE users SET name=?, email=?, phone=? WHERE user_id=?");
            $stmt->bind_param("sssi", $name, $email, $phone, $user_id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET name=?, email=?, phone=?, profile_image_url=? WHERE user_id=?");
            $stmt->bind_param("ssssi", $name, $email, $phone, $profile_image_url, $user_id);
        }

        $stmt->execute();
    }

    // DELETE
    if (isset($_POST['delete'])) {
        $user_id = $_POST['user_id'];
        $stmt = $conn->prepare("DELETE FROM users WHERE user_id=?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Users CRUD</title>
    <style>
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        input[type=text], input[type=password] { width: 100%; }
        form { display: inline; }
        button { padding: 5px 10px; }
    </style>
</head>
<body>
    <h2>Users Table</h2>
    <p>Cannot delete users because the gogrocery_customer doesn't have permission to do so</p>
    <p>Since we do not have delete account feature, hence this is not allowed</p>
    <!-- Display Table -->
    <h3>Existing Users</h3>
    <table>
        <tr>
            <th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Profile Image</th><th>Password Hash</th><th>Actions</th>
        </tr>
        <?php
        $result = $conn->query("SELECT * FROM users");
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<form method='POST'>";
            echo "<td>{$row['user_id']}<input type='hidden' name='user_id' value='{$row['user_id']}'></td>";
            echo "<td><input type='text' name='name' value='{$row['name']}'></td>";
            echo "<td><input type='text' name='email' value='{$row['email']}'></td>";
            echo "<td><input type='text' name='phone' value='{$row['phone']}'></td>";
            $profile_val = $row['profile_image_url'] ?? '';
            echo "<td><input type='text' name='profile_image_url' value='{$profile_val}'></td>";
            echo "<td>{$row['password_hash']}</td>";
            echo "<td>
                    <button type='submit' name='update'>Update</button>
                    <button type='submit' name='delete' onclick=\"return confirm('Delete user?')\">Delete</button>
                  </td>";
            echo "</form>";
            echo "</tr>";
        }
        ?>
    </table>
    <!-- Insert Form -->
    <h3>Add New User</h3>
    <form method="POST">
        <input type="text" name="name" placeholder="Name" required>
        <input type="text" name="email" placeholder="Email" required>
        <input type="text" name="phone" placeholder="Phone" required>
        <input type="text" name="profile_image_url" placeholder="Profile Image URL (optional)">
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="insert">Insert</button>
    </form>
</body>
</html>
