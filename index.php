<?php
include("header.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Home Page</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="./css/styles.css">
    <link rel="stylesheet" href="./css/home_styles.css">
</head>

<body>
    <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">

        <!-- Indicators -->
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active"
                aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1"
                aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2"
                aria-label="Slide 3"></button>
        </div>

        <!-- Carousel inner -->
        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="d-flex justify-content-center align-items-center w-100 h-100">
                    <img src="./images/ads/fresh_produces_ads.png" alt="Fresh Produces">
                </div>
            </div>
            <div class="carousel-item">
                <div class="d-flex justify-content-center align-items-center w-100 h-100">
                    <img src="./images/ads/new_user_ads.png" alt="New User Promo">
                </div>
            </div>
            <div class="carousel-item">
                <div class="d-flex justify-content-center align-items-center w-100 h-100">
                    <img src="./images/ads/skincare_ads.png" alt="Skincare Promo">
                </div>
            </div>
        </div>

        <!-- Prev button -->
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators"
            data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>

        <!-- Next button -->
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators"
            data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <?php 
        include("index_display.php")
    ?>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <?php 
        include("footer.php");
    ?>
</body>

</html>