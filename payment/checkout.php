<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Checkout Payment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
        }

        form {
            max-width: 450px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }

        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }

        input,
        select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
        }

        button {
            margin-top: 20px;
            padding: 10px;
            width: 100%;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background: #218838;
        }

        .error {
            color: red;
            font-size: 0.9em;
        }

        .hidden {
            display: none;
        }
    </style>
</head>

<body>
    <h2>Checkout Payment</h2>

    <form id="paymentForm" method="POST" action="process_payment.php">
        <label for="method">Select Payment Method</label>
        <select id="method" name="payment_method" required>
            <option value="">-- Select --</option>
            <option value="card">Credit/Debit Card</option>
            <option value="bank_transfer">Bank Transfer</option>
            <option value="cod">Cash on Delivery (COD)</option>
            <option value="e_wallet">E-Wallet</option>
            <option value="grabpay">GrabPay</option>
            <option value="fp_x">FPX</option>
        </select>

        <!-- Card fields (only show if method = card) -->
        <div id="cardFields" class="hidden">
            <label for="card_number">Card Number</label>
            <input type="text" id="card_number" name="card_number" maxlength="16">
            <div id="error_card" class="error"></div>

            <label for="expiry">Expiry (MM/YY)</label>
            <input type="text" id="expiry" name="expiry" placeholder="MM/YY">
            <div id="error_expiry" class="error"></div>

            <label for="cvv">CVV</label>
            <input type="password" id="cvv" name="cvv" maxlength="4">
            <div id="error_cvv" class="error"></div>

            <label for="name">Name on Card</label>
            <input type="text" id="name" name="name">
        </div>

        <button type="submit">Confirm Payment</button>
    </form>

    <script>
        const methodSelect = document.getElementById("method");
        const cardFields = document.getElementById("cardFields");
        const form = document.getElementById("paymentForm");

        // Show/hide card fields
        methodSelect.addEventListener("change", function () {
            if (this.value === "card") {
                cardFields.classList.remove("hidden");
            } else {
                cardFields.classList.add("hidden");
            }
        });

        // Validate only if card payment
        form.addEventListener("submit", function (e) {
            if (methodSelect.value === "card") {
                let valid = true;

                // Card number check
                const card = document.getElementById("card_number").value;
                if (!/^\d{16}$/.test(card)) {
                    document.getElementById("error_card").textContent = "Card number must be 16 digits.";
                    valid = false;
                } else document.getElementById("error_card").textContent = "";

                // Expiry check
                const expiry = document.getElementById("expiry").value;
                if (!/^(0[1-9]|1[0-2])\/\d{2}$/.test(expiry)) {
                    document.getElementById("error_expiry").textContent = "Invalid expiry format (MM/YY).";
                    valid = false;
                } else document.getElementById("error_expiry").textContent = "";

                // CVV check
                const cvv = document.getElementById("cvv").value;
                if (!/^\d{3,4}$/.test(cvv)) {
                    document.getElementById("error_cvv").textContent = "CVV must be 3 or 4 digits.";
                    valid = false;
                } else document.getElementById("error_cvv").textContent = "";

                if (!valid) e.preventDefault();
            }
        });
    </script>
</body>

</html>