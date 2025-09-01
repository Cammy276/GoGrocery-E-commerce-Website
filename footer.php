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
  </head>
  <script>
  function openWithFallback(appLink, webLink) {
    // Try to open the app deeplink
    const opened = window.open(appLink);

    // If the deeplink fails → open fallback URL in a new tab
    if (!opened) {
      window.open(webLink, "_blank");
    }

    // Prevent default link behavior
   
  }
</script>

  <body>
    <footer>
      <div class="footer-grid">

        <!-- Column 1: Logo -->
        <section class="logo" aria-label="GoGrocery logo">
          <img src="./images/logo/gogrocery_logo.png" alt="GoGrocery logo" loading="lazy" class="logo-img" />
        </section>

        <!-- Column 2: Company -->
        <section aria-labelledby="footer-company">
          <h2 id="footer-company">Company</h2>
          <p><a href="company/about">About Us</a></p>
        </section>

        <!-- Column 3: Products -->
        <section aria-labelledby="footer-products">
          <h2 id="footer-products">Products</h2>
          <p><a href="products-listing/fresh_produces">Fresh Produces</a></p>
          <p><a href="products-listing/chilled_frozen">Chilled & Frozen</a></p>
          <p><a href="products-listing/essentials_commodities">Food Essentials & Commodities</a></p>
          <p><a href="products-listing/snacks">Snacks</a></p>
          <p><a href="products-listing/beverages">Beverages</a></p>
          <p><a href="products-listing/household">Household Products</a></p>
          <p><a href="products-listing/beauty_health">Beauty & Health</a></p>
        </section>

        <!-- Column 4: Policies -->
        <section aria-labelledby="footer-policies">
          <h2 id="footer-policies">Policies</h2>
          <p><a href="policies/terms_conditions">Terms & Conditions</a></p>
          <p><a href="policies/privacy">Privacy Notice</a></p>
          <p><a href="policies/return_refund">Return & Refund Policy</a></p>
          <p><a href="policies/shipping">Shipping Policy</a></p>
        </section>

        <!-- Column 5: Help Center -->
        <section aria-labelledby="footer-help">
          <h2 id="footer-help">Help Center</h2>
          <p><a href="help/contact">Contact Us</a></p>
          <p><a href="help/faq">FAQs</a></p>
          <p><a href="help/live_chat">Live Chat</a></p>
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
            <a href="instagram://user?username=gogrocery"
              onclick="return openWithFallback(this.href, 'https://instagram.com/gogrocery')"
              aria-label="Instagram">
              <i class="bi bi-instagram"></i>
            </a>

            <a href="fb://page/61550590304"
              onclick="return openWithFallback(this.href, 'https://facebook.com/gogrocery')"
              aria-label="Facebook">
              <i class="bi bi-facebook"></i>
            </a>

            <a href="linkedin://company/gogrocery"
              onclick="return openWithFallback(this.href, 'https://linkedin.com/company/gogrocery')"
              aria-label="LinkedIn">
              <i class="bi bi-linkedin"></i>
            </a>

            <a href="snssdk1128://user/profile/gogrocery"
              onclick="return openWithFallback(this.href, 'https://tiktok.com/@gogrocery')"
              aria-label="TikTok">
              <i class="bi bi-tiktok"></i>
            </a>

            <a href="youtube://channel/gogrocery"
              onclick="return openWithFallback(this.href, 'https://youtube.com/@gogrocery')"
              aria-label="YouTube">
              <i class="bi bi-youtube"></i>
            </a>
          </nav>
        </section>
      </div>
      <hr />
      <div class="footer-bottom">
        <p>Copyright &copy; 2025 GoGrocery. All rights reserved.</p>
        <p>
          <a href="policies/terms_conditions">Terms & Conditions</a>  | 
          <a href="policies/privacy">Privacy Policy</a> | 
          <a href="policies/return_refund">Return & Refund Policy</a> | 
          <a href="policies/shipping">Shipping Policy</a>
        </p>      
    </footer>
  </body>
</html>