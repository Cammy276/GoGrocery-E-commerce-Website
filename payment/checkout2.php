<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Checkout Payment</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 40px; }
    form { max-width: 500px; margin: auto; padding: 20px; border: 1px solid #ccc; border-radius: 8px; }
    label { display: block; margin-top: 10px; font-weight: bold; }
    input, select, textarea { width: 100%; padding: 8px; margin-top: 5px; }
    button { margin-top: 20px; padding: 10px; width: 100%; background: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; }
    button:hover { background: #218838; }
    .error { color: red; font-size: 0.9em; }
    .hidden { display: none; }
  </style>
</head>
<body>
  <h2>Checkout Payment</h2>

  <form id="paymentForm" method="POST" action="process_payment.php" enctype="multipart/form-data">
    <label for="method">Select Payment Method</label>
    <select id="method" name="payment_method" required>
      <option value="">-- Select --</option>
      <option value="card">Credit/Debit Card</option>
      <option value="bank_transfer">Bank Transfer (Manual)</option>
      <option value="cod">Cash on Delivery (COD)</option>
      <option value="e_wallet">E-Wallet</option>
      <option value="grabpay">GrabPay</option>
      <option value="fpx">FPX Online Banking</option>
    </select>

    <!-- CARD -->
    <div id="cardFields" class="hidden">
      <label>Card Number</label>
      <input type="text" id="card_number" name="card_number" maxlength="16">
      <div id="error_card" class="error"></div>

      <label>Expiry (MM/YY)</label>
      <input type="text" id="expiry" name="expiry" placeholder="MM/YY">
      <div id="error_expiry" class="error"></div>

      <label>CVV</label>
      <input type="password" id="cvv" name="cvv" maxlength="4">
      <div id="error_cvv" class="error"></div>

      <label>Name on Card</label>
      <input type="text" id="name" name="name">
    </div>

    <!-- BANK TRANSFER MANUAL -->
    <div id="bankFields" class="hidden">
      <p>Please transfer the amount to:</p>
      <p><strong>Bank:</strong> Maybank</p>
      <p><strong>Account No:</strong> 123456789</p>
      <p><strong>Account Name:</strong> MyShop Sdn Bhd</p>

      <label>Reference Number</label>
      <input type="text" id="reference" name="reference">

      <label>Upload Receipt (jpg/png/pdf)</label>
      <input type="file" id="receipt" name="receipt" accept=".jpg,.jpeg,.png,.pdf">
      <div id="error_receipt" class="error"></div>
    </div>

    <!-- FPX -->
    <div id="fpxFields" class="hidden">
      <label>Select Bank</label>
      <select id="bank_list" name="bank_list">
        <option value="">-- Choose Your Bank --</option>
        <option value="maybank2u">Maybank2u</option>
        <option value="cimbclicks">CIMB Clicks</option>
        <option value="rhb">RHB Now</option>
        <option value="hongleong">Hong Leong Connect</option>
        <option value="ambank">AmBank</option>
        <option value="publicbank">Public Bank</option>
      </select>
      <div id="error_bank" class="error"></div>
    </div>

    <button type="submit">Confirm Payment</button>
  </form>

  <script>
    const methodSelect = document.getElementById("method");
    const cardFields = document.getElementById("cardFields");
    const bankFields = document.getElementById("bankFields");
    const fpxFields = document.getElementById("fpxFields");
    const form = document.getElementById("paymentForm");

    // Toggle fields
    methodSelect.addEventListener("change", function() {
      cardFields.classList.add("hidden");
      bankFields.classList.add("hidden");
      fpxFields.classList.add("hidden");

      if (this.value === "card") cardFields.classList.remove("hidden");
      if (this.value === "bank_transfer") bankFields.classList.remove("hidden");
      if (this.value === "fpx") fpxFields.classList.remove("hidden");
    });

    // Validation
    form.addEventListener("submit", function(e) {
      let valid = true;

      if (methodSelect.value === "card") {
        const card = document.getElementById("card_number").value;
        if (!/^\d{16}$/.test(card)) {
          document.getElementById("error_card").textContent = "Card number must be 16 digits.";
          valid = false;
        } else document.getElementById("error_card").textContent = "";

        const expiry = document.getElementById("expiry").value;
        if (!/^(0[1-9]|1[0-2])\/\d{2}$/.test(expiry)) {
          document.getElementById("error_expiry").textContent = "Invalid expiry format (MM/YY).";
          valid = false;
        } else document.getElementById("error_expiry").textContent = "";

        const cvv = document.getElementById("cvv").value;
        if (!/^\d{3,4}$/.test(cvv)) {
          document.getElementById("error_cvv").textContent = "CVV must be 3 or 4 digits.";
          valid = false;
        } else document.getElementById("error_cvv").textContent = "";
      }

      if (methodSelect.value === "bank_transfer") {
        const receipt = document.getElementById("receipt").files.length;
        if (receipt === 0) {
          document.getElementById("error_receipt").textContent = "Please upload payment receipt.";
          valid = false;
        } else document.getElementById("error_receipt").textContent = "";
      }

      if (methodSelect.value === "fpx") {
        const bank = document.getElementById("bank_list").value;
        if (bank === "") {
          document.getElementById("error_bank").textContent = "Please select your bank.";
          valid = false;
        } else document.getElementById("error_bank").textContent = "";
      }

      if (!valid) e.preventDefault();
    });
  </script>
</body>
</html>
