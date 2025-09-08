<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include(__DIR__ . '/../connect_db.php'); // $conn is available
$mail = require __DIR__ . '/../vendor/mailer.php'; // returns configured PHPMailer

use PHPMailer\PHPMailer\Exception;

// Get user ID from session
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: ../help/contact.php?status=error");
    exit;
}

$success = false;
$errors = [];

// Only process POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name    = trim($_POST['name']);
    $email   = trim($_POST['email']);
    $phone   = trim($_POST['phone']);
    $subject = trim($_POST['subject']);
    $comment = trim($_POST['comment']);

    // Server-side validation (subject & comment only)
    if ($subject === "") {
        $errors['subject'] = "Subject is required";
    }

    if ($comment === "") {
        $errors['comment'] = "Comment is required";
    } elseif (strlen($comment) > 500) {
        $errors['comment'] = "Comment cannot exceed 500 characters";
    }

    // If validation fails, redirect with errors in query string
    if (!empty($errors)) {
        $query = http_build_query(array_merge(['status' => 'error'], $errors));
        header("Location: ../help/contact.php?$query");
        exit;
    }

    // Handle optional image upload
    $contact_image_url = null;
    if (isset($_FILES['contact_image_url']) && $_FILES['contact_image_url']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . "/uploads/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $safeName    = preg_replace("/[^A-Za-z0-9_\-]/", "_", $name);
        $safeSubject = preg_replace("/[^A-Za-z0-9_\-]/", "_", $subject);
        $timestamp   = date("Ymd_His");
        $ext         = pathinfo($_FILES['contact_image_url']['name'], PATHINFO_EXTENSION);
        $filename    = $user_id . "_" . $safeName . "_" . $safeSubject . "_" . $timestamp . "." . $ext;
        $targetPath  = $uploadDir . $filename;

        if (move_uploaded_file($_FILES['contact_image_url']['tmp_name'], $targetPath)) {
            $baseURL = (isset($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
            $contact_image_url = $baseURL . "/uploads/" . $filename;
        }
    }

    // Insert into database
    $stmt = $conn->prepare(
        "INSERT INTO contact_messages 
        (user_id, name, email, phone, subject, comment, contact_image_url) 
        VALUES (?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param("issssss", $user_id, $name, $email, $phone, $subject, $comment, $contact_image_url);
    if (!$stmt->execute()) {
        $stmt->close();
        header("Location: ../help/contact.php?status=error");
        exit;
    }
    $stmt->close();

    // Disable SMTP debug output
    $mail->SMTPDebug = 0;

    // Send emails
    try {
        // --- Send to Admin ---
        $mail->clearAllRecipients();
        $mail->setFrom("meiq4336@gmail.com", "GoGrocery Support");
        $mail->addAddress("meiq4336@gmail.com", "GoGrocery Support");
        $mail->Subject = "New Contact Form Submission: " . htmlspecialchars($subject);
        $mail->isHTML(true);
        $mail->Body = "
            <h3>New Contact Submission</h3>
            <p><strong>Subject:</strong> " . htmlspecialchars($subject) . "</p>
            <p><strong>Name:</strong> " . htmlspecialchars($name) . "</p>
            <p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>
            <p><strong>Phone:</strong> " . htmlspecialchars($phone) . "</p>
            <p><strong>Comment:</strong><br>" . nl2br(htmlspecialchars($comment)) . "</p>
            " . ($contact_image_url ? "<p><a href='$contact_image_url' target='_blank'>View Attachment</a></p>" : "") . "
        ";
        $mail->AltBody = "Subject: $subject\nName: $name\nEmail: $email\nPhone: $phone\nComment: $comment";

        if ($contact_image_url) {
            $absolutePath = __DIR__ . "/uploads/" . basename($contact_image_url);
            if (file_exists($absolutePath)) $mail->addAttachment($absolutePath);
        }

        $mail->send();

        // --- Auto-reply to Customer ---
        $mail->clearAllRecipients();
        $mail->setFrom("meiq4336@gmail.com", "GoGrocery Support");
        $mail->addAddress($email, $name);
        $mail->Subject = "We received your message";
        $mail->isHTML(true);
        $mail->Body = "
            <p>Hi " . htmlspecialchars($name) . ",</p>
            <p>Thank you for contacting us. We received your message on the subject of \"". htmlspecialchars($subject) ."\":</p>
            <blockquote><strong>" . nl2br(htmlspecialchars($comment)) . "</strong></blockquote>
            <p>We will get back to you as soon as possible.</p>
            <p>Thank you for using GoGrocery-E-commerce-Website.</p>
            <p>Regards,<br>Customer Service Team</p>
        ";
        $mail->AltBody = "Dear $name,\n\nThank you for contacting us on the subject of \"$subject\".\n\nMessage:\n$comment\n\n- GoGrocery Support";

        $mail->send();
        $success = true;
    } catch (Exception $e) {
        error_log("PHPMailer Error: " . $mail->ErrorInfo);
        $success = false;
    }
}

// Redirect back to contact.php with status
header("Location: ../help/contact.php?status=" . ($success ? "success" : "error"));
exit;
