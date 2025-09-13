<html>
  <head>
    <title>GoGrocery Footer</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Inter font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="./css/styles.css">
    <link rel="stylesheet" href="./css/footer_styles.css">
  </head>
    <script>
      function openWithFallback(webLink) {
        // Open official website in a new tab
        window.open(webLink, "_blank");
        return false; // Prevent default link behavior
      }
  </script>
  <body>
    <footer>
      <div class="footer-grid">

        <!-- Column 1: Logo -->
        <section class="logo" aria-label="GoGrocery logo">
          <a href="index.php"><img src="<?= BASE_URL ?>/images/logo/gogrocery_logo.png" alt="GoGrocery logo" loading="lazy" class="logo-img" />
        </section>

        <!-- Column 2: Company -->
        <section aria-labelledby="footer-company">
          <h2 id="footer-company">Company</h2>
          <p><a href="<?= BASE_URL ?>company/about">About Us</a></p>
        </section>

        <!-- Column 3: Products -->
        <section aria-labelledby="footer-products">
          <h2 id="footer-products">Products</h2>
          <p><a href="<?= BASE_URL ?>products-listing/fresh_produces">Fresh Produces</a></p>
          <p><a href="<?= BASE_URL ?>products-listing/chilled_frozen">Chilled & Frozen</a></p>
          <p><a href="<?= BASE_URL ?>products-listing/essentials_commodities">Food Essentials & Commodities</a></p>
          <p><a href="<?= BASE_URL ?>products-listing/snacks">Snacks</a></p>
          <p><a href="<?= BASE_URL ?>products-listing/beverages">Beverages</a></p>
          <p><a href="<?= BASE_URL ?>products-listing/household">Household Products</a></p>
          <p><a href="<?= BASE_URL ?>products-listing/beauty_health">Beauty & Health</a></p>
        </section>

        <!-- Column 4: Policies -->
        <section aria-labelledby="footer-policies">
          <h2 id="footer-policies">Policies</h2>
          <p><a href="<?= BASE_URL ?>policies/terms_conditions">Terms & Conditions</a></p>
          <p><a href="<?= BASE_URL ?>policies/privacy_notice">Privacy Notice</a></p>
          <p><a href="<?= BASE_URL ?>policies/return_refund">Return & Refund Policy</a></p>
          <p><a href="<?= BASE_URL ?>policies/shipping_policies">Shipping Policy</a></p>
        </section>

        <!-- Column 5: Help Center -->
        <section aria-labelledby="footer-help">
          <h2 id="footer-help">Help Center</h2>
          <p><a href="<?= BASE_URL ?>help/contact">Contact Us</a></p>
          <p><a href="<?= BASE_URL ?>help/faq">FAQs</a></p>
          <p><a href="<?= BASE_URL ?>help/live_chat">Live Chat</a></p>
        </section>

        <!-- Column 6: Contact Details + Social Icons -->
        <section class="contact-details" aria-labelledby="footer-contact">
          <h2 id="footer-contact">Contact Details</h2>

          <p id="address">Address: 30, Jalan 1/65A, Titiwangsa, Kuala Lumpur, Federal Territory of Kuala Lumpur, Malaysia</p>

          <p id="operating-hours">Operating Hours: 8.00 AM - 10.00 PM (Monday - Sunday)</p>

          <p id="careline-number"><a href="tel:+601300121234">1300-12-1234</a></p>

          <p id="email"><a href="mailto:customer.careline@gogrocery.com.my">customer.careline@gogrocery.com.my</a></p>

          <!-- Social Icons -->
          <nav class="social-icons" aria-label="Social Media Links">

            <!-- Instagram -->
            <a href="https://www.instagram.com"
              onclick="return openWithFallback(this.href)"
              aria-label="Instagram"
              title="Visit Instagram">
              <i class="bi bi-instagram"></i>
            </a>

            <!-- Facebook -->
            <a href="https://www.facebook.com"
              onclick="return openWithFallback(this.href)"
              aria-label="Facebook"
              title="Visit Facebook">
              <i class="bi bi-facebook"></i>
            </a>

            <!-- LinkedIn -->
            <a href="https://www.linkedin.com"
              onclick="return openWithFallback(this.href)"
              aria-label="LinkedIn"
              title="Visit LinkedIn">
              <i class="bi bi-linkedin"></i>
            </a>

            <!-- TikTok -->
            <a href="https://www.tiktok.com"
              onclick="return openWithFallback(this.href)"
              aria-label="TikTok"
              title="Visit TikTok">
              <i class="bi bi-tiktok"></i>
            </a>

            <!-- YouTube -->
            <a href="https://www.youtube.com"
              onclick="return openWithFallback(this.href)"
              aria-label="YouTube"
              title="Visit YouTube">
              <i class="bi bi-youtube"></i>
            </a>

          </nav>
        </section>
      </div>
      <hr />
      <div class="footer-bottom">
        <p>Copyright &copy; 2025 GoGrocery. All rights reserved.</p>
        <p>
          <a href="<?= BASE_URL ?>policies/terms_conditions">Terms & Conditions</a>  | 
          <a href="<?= BASE_URL ?>policies/privacy_notice">Privacy Policy</a> | 
          <a href="<?= BASE_URL ?>policies/return_refund">Return & Refund Policy</a> | 
          <a href="<?= BASE_URL ?>policies/shipping_policies">Shipping Policy</a>
        </p>      
    </footer>
  </body>
</html>