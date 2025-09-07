<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About Us</title>
  <link rel="stylesheet" href="../css/styles.css">
  <link rel="stylesheet" href="../css/about_styles.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
      <section class="experience-section">
        <div class="experience-content">
            <h1>Over 10+ Years of <br>Fresh Grocery Service</h1>
            <p>
                With over a decade of experience, we bring fresh, high-quality groceries straight to your doorstep. 
                Our dedication to quality and service makes us your trusted grocery partner.
            </p>
        <div class="experience-highlights">
            <div class="highlight">
                <i class="bi bi-check-circle-fill"></i>
                <div>
                    <h3>Fresh & Quality Products</h3>
                    <p>We carefully select every product to ensure freshness, quality
                        and value for you and your family.</p>
                </div>
            </div>
            <div class="highlight">
                <i class="bi bi-check-circle-fill"></i>
                <div>
                    <h3>Friendly & Knowledgeable Team</h3>
                    <p>Our team is passionate about helping you find the best groceries 
                        and offering helpful advice whenever you need it.</p>
                </div>
            </div>
        </div>
        <a href="#about-container" class="btn-experience">More About Us</a>
        </div>
        <div class="experience-image">
            <img src="../images/company/experience.jpg" alt="Experience Image">
        </div>
  </section>
    <section class="about-section">
    <div class="about-us-container" id="about-container">
    <div class="about-container">
      <h2>About Us</h2>
      <p id="about-description">
        GoGrocery is a trusted online grocery store based in Kuala Lumpur, Malaysia, 
        offering everything from fresh produce, chilled & frozen items, food essentials, 
        snacks, beverages, household goods, to beauty and health products. 
        With fast delivery and 24/7 customer support, we make shopping simple, 
        convenient and reliable for every home.
      </p>
    </div>

    <div class="mission-container">
      <h2>Our Mission</h2>
      <p id="about-mission">
        At GoGrocery, our mission is to provide a seamless and convenient 
        online grocery shopping experience, delivering high-quality products 
        right to your doorstep. We are committed to exceptional customer service, 
        competitive pricing, and a wide selection of fresh and essential items 
        to meet the diverse needs of our customers.
      </p>
    </div>

    <div class="values-container">
    <h2>Our Values</h2>
    <div class="about-card">
        <ul id="about-values">
        <li id="value-guarantee-freshness"><strong>G – Guarantee Freshness:</strong> Always delivering high-quality and fresh products.</li>
        <li id="value-reliability"><strong>R – Reliability:</strong> On-time delivery and dependable customer support.</li>
        <li id="value-outstanding-care"><strong>O – Outstanding Care:</strong> Putting customer satisfaction at the heart of everything.</li>
        <li id="value-convenience"><strong>C – Convenience:</strong> Making shopping simple, fast, and hassle-free.</li>
        <li id="value-eco-friendly"><strong>E – Eco-Friendly:</strong> Practicing sustainability and reducing waste.</li>
        <li id="value-reinvent"><strong>R – Reinvent (Innovation):</strong> Continuously improving with smarter solutions.</li>
        <li id="value-your-trust"><strong>Y – Your Trust:</strong> Building long-term relationships through honesty and care.</li>
        </ul>
    </div>
    </div>
  </div>
</section>

<section class="process-section">
    <h2>We Follow The Process</h2>
    <div class="process-flow">
      <div class="step slide-left">
        <div class="icon">
          <img src="../images/company/smartphone.png" alt="Browse">
        </div>
        <h3>Browse & Select</h3>
        <p>Easily explore our wide range of fresh groceries and add your favorites to the cart</p>
      </div>

      <div class="step slide-left">
        <div class="icon">
          <img src="../images/company/confirmation.png" alt="Place Order">
        </div>
        <h3>Place Order</h3>
        <p>Confirm your order and proceed to checkout for a seamless shopping experience</p>
      </div>

      <div class="step slide-right">
        <div class="icon">
          <img src="../images/company/details.png" alt="Order Confirmation">
        </div>
        <h3>Order Confirmation</h3>
        <p>Receive instant confirmation of your order with all details for peace of mind</p>
      </div>

      <div class="step slide-right">
        <div class="icon">
          <img src="../images/company/delivery.png" alt="Delivery & Enjoy">
        </div>
        <h3>Delivery & Enjoy</h3>
        <p>Sit back as we deliver fresh groceries to your doorstep, ready for you to enjoy</p>
      </div>
    </div>
  </section>
  <section class="stats-section">
    <h2>Our Achievements</h2>

    <div class="stats-container">
      <div class="stat-card">
        <div class="icon">🚚</div>
        <h3>500K+</h3>
        <p>Orders Delivered</p>
      </div>

      <div class="stat-card">
        <div class="icon">🛒</div>
        <h3>99%</h3>
        <p>Fresh Products Sold</p>
      </div>

      <div class="stat-card">
        <div class="icon">🏅</div>
        <h3>10+</h3>
        <p>Years of Service</p>
      </div>

      <div class="stat-card">
        <div class="icon">⏱️</div>
        <h3>24/7</h3>
        <p>Service Availability</p>
      </div>
    </div>

    <div class="cta">
      <a href="../products-listing/fresh_produces.php" class="shopping-btn">Shop Now</a>
    </div>
  </section>
  <footer>
  <?php include '../footer.php'; ?>
</footer>
    <script>
    document.querySelectorAll("#about-values-title span").forEach(span => {
        span.addEventListener("mouseenter", () => {
        const targetId = span.getAttribute("data-target");
        document.getElementById(targetId).classList.add("highlight");
        });
        span.addEventListener("mouseleave", () => {
        const targetId = span.getAttribute("data-target");
        document.getElementById(targetId).classList.remove("highlight");
        });
    });
      document.addEventListener("DOMContentLoaded", () => {
        const contents = document.querySelectorAll(".experience-content, .experience-image");

        const observer = new IntersectionObserver((entries) => {
          entries.forEach(entry => {
            if (entry.isIntersecting) {
              entry.target.classList.add("animate");
            } else {
              entry.target.classList.remove("animate"); // so it replays when scrolling up
            }
          });
        }, { threshold: 0.2 }); // trigger when 20% visible

        contents.forEach(el => observer.observe(el));
      });
      document.addEventListener("DOMContentLoaded", () => {
        const contents = document.querySelectorAll(
          ".experience-content, .experience-image, .about-us-container, .stat-card"
        );

        const observer = new IntersectionObserver((entries) => {
          entries.forEach(entry => {
            if (entry.isIntersecting) {
              entry.target.classList.add("animate");
            } else {
              entry.target.classList.remove("animate"); // replay when scrolling up
            }
          });
        }, { threshold: 0.2 });

        contents.forEach(el => observer.observe(el));
      });
      document.addEventListener("DOMContentLoaded", () => {
      const contents = document.querySelectorAll(
        ".experience-content, .experience-image, .about-us-container, .stat-card, .step"
      );

      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add("animate");
          } else {
            entry.target.classList.remove("animate"); // replay when scrolling back
          }
        });
      }, { threshold: 0.2 });

      contents.forEach(el => observer.observe(el));
    });

</script>
</body>
</html>
