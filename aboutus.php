<?php
include 'partial/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Swans Cinema | About Us</title>
    <link rel="stylesheet" href="css/aboutus.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <main>
        <section class="hero-savi">
            <div class="hero-content-savi">
                <h1 id="section-title-savi">Experience Cinema Like Never Before 🎬</h1>
                <p>Welcome to Swans Cinema, where every visit is an unforgettable journey into the world of film. We are dedicated to providing the ultimate cinematic experience.</p>
                <a href="#our-story-savi" class="button-savi">Discover Our Story</a>
            </div>
        </section>

        <section id="our-story-savi" class="about-section-savi">
            <div class="container-savi">
                <h2 class="section-heading-savi">Our Story: A Legacy of Entertainment</h2>
                <div class="content-wrapper-savi">
                    <div class="text-content-savi">
                        <p>Swans Cinema began with a vision to create a haven for movie lovers. From humble beginnings, we've grown into a beloved local landmark, committed to bringing you the latest blockbusters and timeless classics in unparalleled comfort and style.</p>
                        <p>Our passion for cinema drives us to constantly innovate, offering state-of-the-art projection, immersive sound, and a welcoming atmosphere that makes every visit special. We believe in the magic of movies and strive to share that magic with our community.</p>
                    </div>
                    <div class="image-content-savi reveal-on-scroll-savi">
                        <img src="images/a4.jpg" alt="Swans Cinema Exterior" class="image-savi">
                    </div>
                </div>
            </div>
        </section>

        <section class="features-section-savi">
            <div class="container-savi">
                <h2 class="section-heading-savi">Why Choose Swans Cinema? </h2>
                <div class="features-grid-savi">
                    <div class="feature-card-savi reveal-on-scroll-savi">
                        <i class="fas fa-couch icon-savi"></i>
                        <h3>Luxurious Seating</h3>
                        <p>Sink into our plush, ergonomic seats designed for maximum comfort during even the longest films.</p>
                    </div>
                    <div class="feature-card-savi reveal-on-scroll-savi">
                        <i class="fas fa-volume-up icon-savi"></i>
                        <h3>Immersive Sound</h3>
                        <p>Experience every whisper and explosion with our cutting-edge surround sound systems.</p>
                    </div>
                    <div class="feature-card-savi reveal-on-scroll-savi">
                        <i class="fas fa-video icon-savi"></i>
                        <h3>Crystal Clear Projection</h3>
                        <p>Enjoy stunning visuals with our high-definition projectors, bringing every detail to life.</p>
                    </div>
                    
                </div>
            </div>
        </section>

        <section class="team-section-savi">
            <div class="container-savi">
                <h2 class="section-heading-savi">Our Halls </h2>
                <div class="halls-grid-savi">
                    <div class="hall-card-savi reveal-on-scroll-savi">
                        <img src="images/a5.avif" alt="Cinema Hall 1" class="hall-image-savi">
                        <div class="hall-info-savi">
                            <h3>Grand Sapphire Hall</h3>
                            <p>Our largest hall, perfect for blockbusters and premiere events.</p>
                        </div>
                    </div>
                    <div class="hall-card-savi reveal-on-scroll-savi">
                        <img src="images/a2.avif" alt="Cinema Hall 2" class="hall-image-savi">
                        <div class="hall-info-savi">
                            <h3>Lavender Lounge</h3>
                            <p>An intimate setting for a more private and luxurious viewing experience.</p>
                        </div>
                    </div>
                    <div class="hall-card-savi reveal-on-scroll-savi">
                        <img src="images/a3.avif" alt="Cinema Hall 3" class="hall-image-savi">
                        <div class="hall-info-savi">
                            <h3>Emerald Elite</h3>
                            <p>Equipped with special accessible seating and enhanced sound for all.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="cta-section-savi">
            <div class="container-savi">
                <h2 class="cta-heading-savi">Ready to Experience the Magic?</h2>
                <p class="cta-text-savi">Book your tickets today and become part of the Swans Cinema family.</p>
                <a href="now_showing.php" class="button-savi primary-button-savi">View Showtimes</a>
            </div>
        </section>
    </main>

    <script src="js/aboutus.js"></script>
</body>
</html>

<?php
include 'partial/footer.php';
?>