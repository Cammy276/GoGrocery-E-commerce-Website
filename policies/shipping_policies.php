<?php
  include __DIR__ . '/../livechat/chat_UI.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Shipping Policy</title>
  <header>
    <?php include '../header.php'; ?>
  </header>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/header_styles.css">
    <link rel="stylesheet" href="../css/shipping_policies_styles.css">
    <link rel="stylesheet" href="../css/footer_styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
  <section class="shipping-section">
  <div class="shipping-container">
    <h1>Shipping Policy</h1>
    <p class="subtitle">Welcome to GoGrocery! Please read the following Shipping Policies carefully before using our services.</p>
    <div class="policy-section">
      <div class="tnc-card">
        <h3>1. Delivery Coverage</h3>
        <p>
          GoGrocery currently delivers within designated service areas in Malaysia, including both Peninsular Malaysia 
          and East Malaysia. Our delivery network is constantly being reviewed and expanded to serve more customers. 
          From time to time, we may add or remove coverage areas depending on courier availability, logistics capacity, 
          and operational feasibility.
        </p>
        <p>
          Before placing an order, customers are encouraged to check whether their location is within our active 
          delivery zones. Orders placed outside of our coverage may be automatically canceled or subject to additional 
          delivery arrangements.
        </p>
      </div>

      <div class="tnc-card">
        <h3>2. Delivery Timeframe</h3>
        <p>
          We strive to process and dispatch all confirmed orders as quickly as possible. Under normal circumstances, 
          delivery timelines are as follows:
        </p>
        <ul>
          <li><strong>Klang Valley:</strong> 1–3 working days after order confirmation.</li>
          <li><strong>Other Peninsular Malaysia regions:</strong> 2–5 working days.</li>
          <li><strong>East Malaysia (Sabah & Sarawak):</strong> 3–7 working days, depending on courier schedules.</li>
        </ul>
        <p>
          Please note that delivery times are estimates and may vary during peak seasons (e.g., festive periods, 
          mega sales, public holidays) or due to factors outside of our control, such as severe weather conditions, 
          traffic restrictions, or delays by third-party courier partners.
        </p>
        <p>
          In rare cases, unexpected stock shortages or payment verification issues may also extend delivery times. 
          Customers will be notified if such delays occur.
        </p>
      </div>

      <div class="tnc-card">
        <h3>3. Delivery Fees</h3>
        <p>
          Shipping fees are calculated based on the delivery location, order weight/volume, and total order value. 
          During certain promotional campaigns, free shipping or discounted shipping may be offered for eligible orders.
        </p>
        <p>
          For transparency, applicable delivery charges will be displayed at checkout before payment is confirmed. 
          Customers are advised to review these charges carefully. Any additional surcharges imposed by third-party 
          courier services (e.g., remote area fees) will also be reflected in the final delivery cost.
        </p>
      </div>

      <div class="tnc-card">
        <h3>4. Failed Deliveries</h3>
        <p>
          It is the customer’s responsibility to ensure that accurate and complete delivery details (including recipient 
          name, contact number, and delivery address) are provided at the time of order.
        </p>
        <p>
          If a delivery attempt fails due to incomplete/incorrect details or the recipient being unavailable, additional 
          re-delivery or storage charges may apply. In some cases, orders may be returned to GoGrocery. Re-delivery 
          arrangements will be made upon request, but extra costs will be borne by the customer.
        </p>
        <p>
          Repeated failed deliveries or refusal to accept parcels may result in order cancellation and partial or full 
          forfeiture of payments, depending on circumstances.
        </p>
      </div>

      <div class="tnc-card">
        <h3>5. Order Tracking</h3>
        <p>
          Once an order has been shipped, customers will receive a tracking number (if available) via email or SMS. 
          This tracking number can be used to monitor the progress of the delivery on the respective courier’s tracking portal.
        </p>
        <p>
          Please be advised that tracking information may take several hours to update after shipment. GoGrocery will 
          provide assistance in the event of tracking errors or issues, but once the package is handed over to a 
          third-party courier, the responsibility for tracking accuracy lies with the courier service.
        </p>
      </div>

      <div class="tnc-card">
        <h3>6. Risk of Loss</h3>
        <p>
          All goods purchased from GoGrocery are delivered pursuant to a shipment contract. This means that the risk 
          of damage or loss passes to the customer once the order is successfully delivered to the address provided 
          during checkout.
        </p>
        <p>
          Customers are advised to check their parcels immediately upon receipt. If the package is visibly damaged, 
          customers should refuse to accept delivery and notify GoGrocery within 24 hours so that a claim can be 
          raised with the courier. For accepted parcels, any discrepancies or damages must be reported to GoGrocery’s 
          customer service team within 48 hours, along with supporting photo evidence.
        </p>
      </div>
    </div>
  </div>
</section>
<footer>
    <?php include '../footer.php'; ?>
</footer>
</body>
</html>