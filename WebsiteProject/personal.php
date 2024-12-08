<?php include("repeat/navbar.php"); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Hi:D</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="stylesheet" href="style2.css"> <!-- couldnt open style.css because of boostrap -->

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>

<body>
    <p class="h1"> WELCOME BACK TO THE CAROUSEL </p>
    <!-- Flex Container -->
    <div class="d-flex justify-content-around align-items-start mt-4">
        <!-- Carousel Section -->
        <div id="carouselExampleSlidesOnly" class="carousel slide" data-ride="carousel" data-interval="1500" data-pause="false">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="imgs/150.jpg" class="d-block w-150" alt="First slide">
                </div>
                <div class="carousel-item">
                    <img src="imgs/bf0.jpg" class="d-block w-150" alt="Second slide">
                </div>
                <div class="carousel-item">
                    <img src="imgs/d4f.jpg" class="d-block w-150" alt="Third slide">
                </div>
                <div class="carousel-item">
                    <img src="imgs/f25.jpg" class="d-block w-150" alt="Fourth slide">
                </div>
            </div>
        </div>
        <div class="container px-4">
            <h1>Personal Page</h1>
            <p>About me</p>
        </div>
    </div>
</body>

</html>