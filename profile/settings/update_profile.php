<?php
session_start();
include(__DIR__ . '/../../connect_db.php');

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Get current info (for deleting old image if needed)
$stmt = $conn->prepare("SELECT name, email, phone, profile_image_url FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($currentName, $currentEmail, $currentPhone, $currentProfileImage);
$stmt->fetch();
$stmt->close();

// Input values (fallback to current)
$name  = $_POST['name']  ?? $currentName;
$email = $_POST['email'] ?? $currentEmail;
$phone = $_POST['phone'] ?? $currentPhone;
$profileImagePath = $currentProfileImage;

// Handle image upload
if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = __DIR__ . '/../../images/users/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $ext = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));
    $safeUserName = preg_replace("/[^a-zA-Z0-9]/", "", ($name ?: "user"));
    $timestamp = time();

    $newFileName = $user_id . "_" . $safeUserName . "_profilepic_" . $timestamp . "." . $ext;
    $targetPath = $uploadDir . $newFileName;

    if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $targetPath)) {
        $profileImagePath = "/GoGrocery-E-commerce-Website/images/users/" . $newFileName;

        // Delete old image if not default
        if ($currentProfileImage && basename($currentProfileImage) !== "default.png") {
            $oldFilePath = __DIR__ . "/../.." . str_replace("/GoGrocery-E-commerce-Website", "", $currentProfileImage);
            if (file_exists($oldFilePath)) {
                unlink($oldFilePath);
            }
        }
    }
}

// Update database
$stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, phone = ?, profile_image_url = ? WHERE user_id = ?");
$stmt->bind_param("ssssi", $name, $email, $phone, $profileImagePath, $user_id);

if ($stmt->execute()) {
    $_SESSION['success'] = "Profile updated successfully!";
} else {
    $_SESSION['error'] = "Failed to update profile.";
}

$stmt->close();
$conn->close();

// Redirect back
header("Location: ./index.php");
exit;
