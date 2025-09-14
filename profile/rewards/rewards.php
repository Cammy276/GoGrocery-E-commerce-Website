<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../auth/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Vouchers</title>
    <link rel="stylesheet" href="../../css/rewards_styles.css">
    <link rel="stylesheet" href="../../css/styles.css">
    <link rel="stylesheet" href="../../css/header_styles.css">
    <link rel="stylesheet" href="../../css/footer_styles.css">
</head>
<body>
    <h1>Vouchers</h1>

    <div class="voucher-container">
        <!-- Tabs -->
        <div class="tabs">
            <div class="tab active" data-target="active">Active</div>
            <div class="tab" data-target="upcoming">Upcoming</div>
            <div class="tab" data-target="past">Past</div>
        </div>

        <!-- Voucher lists (AJAX loads here) -->
        <div id="voucher-content">
            <p class="loading">Loading vouchers...</p>
        </div>
    </div>

    <script>
        function loadVouchers(type = "active") {
            const xhr = new XMLHttpRequest();
            xhr.open("GET", "rewards_display.php?type=" + type, true);
            xhr.onload = function() {
                if (this.status === 200) {
                    document.getElementById("voucher-content").innerHTML = this.responseText;
                } else {
                    document.getElementById("voucher-content").innerHTML = "<p>Error loading vouchers.</p>";
                }
            };
            xhr.send();
        }

        // Initial load
        loadVouchers("active");

        // Tab switching
        const tabs = document.querySelectorAll(".tab");
        tabs.forEach(tab => {
            tab.addEventListener("click", () => {
                tabs.forEach(t => t.classList.remove("active"));
                tab.classList.add("active");
                loadVouchers(tab.dataset.target);
            });
        });
    </script>
</body>
</html>
