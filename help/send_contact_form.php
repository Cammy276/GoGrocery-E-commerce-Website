<?php
require __DIR__ . '/../vendor/mailer.php'; 
require __DIR__ . '/../init.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$success = false;
$user_id = $_SESSION['user_id'] ?? null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name    = trim($_POST['name']);
    $email   = trim($_POST['email']);
    $phone   = trim($_POST['phone']);
    $subject = trim($_POST['subject']);
    $comment = trim($_POST['comment']);

    // Server-side validation
    if ($name === "" || $email === "" || $phone === "" || $subject === "" || $comment === "") {
        header("Location: contact_form.php?status=error");
        exit;
    }
    if (!preg_match("/^[A-Za-z\s]+$/", $name) || 
        !filter_var($email, FILTER_VALIDATE_EMAIL) || 
        !preg_match("/^\+?60\d{9,10}$/", $phone) || 
        strlen($comment) > 500) {
        header("Location: contact_form.php?status=error");
        exit;
    }

    // Insert into DB first (to get message_id and created_at)
    $stmt = $conn->prepare(
        "INSERT INTO contact_messages 
        (user_id, name, email, phone, subject, comment, contact_image_url) 
        VALUES (?, ?, ?, ?, ?, ?, NULL)"
    );
    $stmt->bind_param("isssss", $user_id, $name, $email, $phone, $subject, $comment);

    if (!$stmt->execute()) {
        header("Location: contact_form.php?status=error");
        exit;
    }
    $message_id = $stmt->insert_id;
    $stmt->close();

    // Fetch created_at timestamp for filename
    $created_at = '';
    $result = $conn->prepare("SELECT created_at FROM contact_messages WHERE message_id = ?");
    $result->bind_param("i", $message_id);
    $result->execute();
    $result->bind_result($created_at);
    $result->fetch();
    $result->close();

    // Convert timestamp to safe string (e.g. 2025-09-08 21:35:22 → 20250908_213522)
    $timestamp_str = date("Ymd_His", strtotime($created_at));

    // Handle image upload
    $contact_image_url = null;
    if (isset($_FILES['contact_image_url']) && $_FILES['contact_image_url']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . "/uploads/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $safeName = preg_replace("/[^A-Za-z0-9_\-]/", "_", $name);
        $safeSubject = preg_replace("/[^A-Za-z0-9_\-]/", "_", $subject);
        $ext = pathinfo($_FILES['contact_image_url']['name'], PATHINFO_EXTENSION);

        // New format: <user_id>_<name>_<subject>_<timestamp>.<ext>
        $filename = ($user_id ?? "guest") . "_" . $safeName . "_" . $safeSubject . "_" . $timestamp_str . "." . $ext;
        $targetPath = $uploadDir . $filename;

        if (move_uploaded_file($_FILES['contact_image_url']['tmp_name'], $targetPath)) {
            $contact_image_url = "uploads/" . $filename;

            // Update record with image path
            $update = $conn->prepare("UPDATE contact_messages SET contact_image_url=? WHERE message_id=?");
            $update->bind_param("si", $contact_image_url, $message_id);
            $update->execute();
            $update->close();
        }
    }

    try {
        $mail = new PHPMailer(true);

        // ========= Send to Admin =========
        $mail->setFrom("meiq4336@gmail.com", "GoGrocery Support");
        $mail->addAddress("meiq4336@gmail.com", "GoGrocery Support");
        $mail->Subject = "New Contact Form Submission: " . htmlspecialchars($subject);
        $mail->isHTML(true);
        $mail->Body    = "
            <h3>New Contact Submission</h3>
            <p><strong>Name:</strong> " . htmlspecialchars($name) . "</p>
            <p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>
            <p><strong>Phone:</strong> " . htmlspecialchars($phone) . "</p>
            <p><strong>Subject:</strong> " . htmlspecialchars($subject) . "</p>
            <p><strong>Comment:</strong><br>" . nl2br(htmlspecialchars($comment)) . "</p>
            " . ($contact_image_url ? "<p><a href='$contact_image_url'>View Attachment</a></p>" : "") . "
        ";
        $mail->AltBody = "Name: $name\nEmail: $email\nPhone: $phone\nSubject: $subject\nComment: $comment";
        if ($contact_image_url) {
            $mail->addAttachment(__DIR__ . "/" . $contact_image_url);
        }
        $mail->send();

        // ========= Auto-reply to Customer =========
        $mail->clearAllRecipients();
        $mail->setFrom("meiq4336@gmail.com", "GoGrocery Support");
        $mail->addAddress($email, $name);
        $mail->Subject = "We received your message";
        $mail->isHTML(true);
        $mail->Body    = "
            <p>Hi " . htmlspecialchars($name) . ",</p>
            <p>Thank you for contacting us. We received your message:</p>
            <blockquote><strong>" . nl2br(htmlspecialchars($comment)) . "</strong></blockquote>
            <p>We will get back to you as soon as possible.</p>
            <p>Thank you for using GoGrocery-E-commerce-Website.</p>
            <p>Regards,<br>Customer Service Team</p>
        ";
        $mail->AltBody = "Dear $name,\n\nThank you for contacting us.\n\nMessage:\n$comment\n\n- GoGrocery Support";
        $mail->send();

        $success = true;
    } catch (Exception $e) {
        $success = false;
    }
}

// Redirect back with status
header("Location: contact_form.php?status=" . ($success ? "success" : "error"));
exit;
