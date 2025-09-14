<?php
include __DIR__ . '/../livechat/chat_UI.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GoGrocery FAQ</title>
  <header>
    <?php include '../header.php'; ?>
  </header>
  <link rel="stylesheet" href="../css/styles.css">
  <link rel="stylesheet" href="../css/header_styles.css">
  <link rel="stylesheet" href="../css/footer_styles.css">
  <link rel="stylesheet" href="../css/faq_styles.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
  <h1>Frequently Asked Questions</h1>
  <div class="faq-container">
    <div class="faq-item">
    <div class="faq-question">
      <span>What is GoGrocery?</span>
      <span class="icon"><i class="bi bi-chevron-down"></i></span>
    </div>
      <div class="faq-answer">
        GoGrocery is an online grocery platform that delivers fresh, high-quality products to your doorstep with ease and convenience.
      </div>
    </div>

    <div class="faq-item">
      <div class="faq-question">
        <span>How do I place an order?</span>
        <span class="icon"><i class="bi bi-chevron-down"></i></span>
      </div>
      <div class="faq-answer">
        Simply browse products, add them to your cart, and proceed to checkout. You’ll receive an order confirmation once payment is completed.
      </div>
    </div>

    <div class="faq-item">
      <div class="faq-question">
        <span>What payment methods do you accept?</span>
        <span class="icon"><i class="bi bi-chevron-down"></i></span>
      </div>
      <div class="faq-answer">
        We accept credit/debit cards, online banking, e-wallets, and other secure digital payment options.
      </div>
    </div>

    <div class="faq-item">
      <div class="faq-question">
        <span>How do I know my groceries are fresh?</span>
        <span class="icon"><i class="bi bi-chevron-down"></i></span>
      </div>
      <div class="faq-answer">
        We guarantee freshness by carefully selecting, storing, and delivering products straight from trusted suppliers.
      </div>
    </div>

    <div class="faq-item">
      <div class="faq-question">
        <span>How can I track my order?</span>
        <span class="icon"><i class="bi bi-chevron-down"></i></span>
      </div>
      <div class="faq-answer">
        After checkout, you’ll receive a tracking number via email or SMS, so you can easily track your groceries and know exactly when to expect them.
      </div>
    </div>

    <div class="faq-item">
      <div class="faq-question">
        <span>What is your return or refund policy?</span>
        <span class="icon"><i class="bi bi-chevron-down"></i></span>
      </div>
      <div class="faq-answer">
        If you receive damaged, expired, or wrong items, you can request a return or refund within 24 hours of delivery.
      </div>
    </div>

    <div class="faq-item">
      <div class="faq-question">
        <span>How do I contact customer support?</span>
        <span class="icon"><i class="bi bi-chevron-down"></i></span>
      </div>
      <div class="faq-answer">

        You can reach our customer support team via email, phone, or live chat on our website. We’re here to help with any questions or concerns you may have.
        <br>
        Address: 30, Jalan 1/65A, Titiwangsa, Kuala Lumpur, Federal Territory of Kuala Lumpur, Malaysia<br>
        Operating Hours: 8.00 AM – 10.00 PM (Monday – Sunday)<br>
        Hotline: 1300-12-1234<br>
        Email: customer.careline@gogrocery.com.my
 
      </div>
    </div>
  </div>

  <script>
    const items = document.querySelectorAll(".faq-item");

    items.forEach(item => {
      const question = item.querySelector(".faq-question");
      question.addEventListener("click", () => {
        item.classList.toggle("active");
      });
    });
  </script>
  <footer>
  <?php include '../footer.php'; ?>
</footer>
</body>
</html>
