<?php
require __DIR__ . '/../init.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Contact Us</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="/../css/styles.css">
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
  <div class="container my-5">
  <h1 class="mb-4 text-center">Contact Us</h1>

  <!-- Map Section -->
  <h2 class="mb-3">Find Us Here</h2>
  <div class="mb-5">
    <!-- Google Map on top -->
    <iframe
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3983.7897823942497!2d101.70301857472514!3d3.1760303532541745!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31cc482bbec2207b%3A0x84a930a594d5c8e6!2s30%2C%20Jalan%201%2F65a%2C%20Titiwangsa%2C%2053000%20Kuala%20Lumpur%2C%20Wilayah%20Persekutuan%20Kuala%20Lumpur%2C%20Malaysia!5e0!3m2!1sen!2smy!4v1694253600000!5m2!1sen!2smy"
        class="w-100 rounded"
        style="height: 350px;"
        allowfullscreen=""
        loading="lazy"
        referrerpolicy="no-referrer-when-downgrade">
    </iframe>
  </div>

  <!-- Bottom: Contact Info & Form -->
  <div class="row g-4" style="min-height: calc(100vh - 450px);">

  <!-- Left: Contact Info -->
  <div class="col-md-5">
    <h2 class="mb-2">Contact Info</h2>
    <div class="d-flex flex-column gap-3">
        <!-- Address -->
        <div class="card shadow-sm p-3">
            <h5 class="card-title"><i class="bi bi-geo-alt-fill me-2"></i>Address</h5>
            <p class="card-text mb-0">30, Jalan 1/65A, Titiwangsa, Kuala Lumpur,<br>Federal Territory of Kuala Lumpur, Malaysia</p>
        </div>
        <!-- Operating Hours -->
        <div class="card shadow-sm p-3">
            <h5 class="card-title"><i class="bi bi-clock-fill me-2"></i>Operating Hours</h5>
            <p class="card-text mb-0">8.00 AM - 10.00 PM (Monday - Sunday)</p>
        </div>
        <!-- Careline Number -->
        <div class="card shadow-sm p-3">
            <h5 class="card-title"><i class="bi bi-telephone-fill me-2"></i>Careline Number</h5>
            <p class="card-text mb-0"><a href="tel:+601300121234" class="text-dark">1300-12-1234</a></p>
        </div>
        <!-- Email -->
        <div class="card shadow-sm p-3">
            <h5 class="card-title"><i class="bi bi-envelope-fill me-2"></i>Email</h5>
            <p class="card-text mb-0"><a href="mailto:customer.careline@gogrocery.com.my" class="text-dark">customer.careline@gogrocery.com.my</a></p>
        </div>
        <!-- Social Media -->
        <div class="card shadow-sm p-3">
            <h5 class="card-title"><i class="bi bi-share-fill me-2"></i>Follow Us</h5>
            <nav class="social-icons" aria-label="Social Media Links">
                <a href="https://www.instagram.com" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                <a href="https://www.facebook.com" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                <a href="https://www.linkedin.com" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
                <a href="https://www.tiktok.com" aria-label="TikTok"><i class="bi bi-tiktok"></i></a>
                <a href="https://www.youtube.com" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
            </nav>
        </div>
    </div>
  </div>

  <!-- Right: Contact Form -->
  <div class="col-md-7">
    <h2 class="mb-2">Contact Form</h2>
    <div class="card shadow-sm h-100">
      <div class="card-body">
        <?php include("contact_form.php"); ?>
      </div>
    </div>
  </div>
</div>

  <!-- Bootstrap 5 JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
