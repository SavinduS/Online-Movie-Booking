
<!-- partial/header.php -->
<!DOCTYPE html>
<html lang="en">
  <meta charset="UTF-8">

  <!-- Favicon -->
  <link rel="icon" type="image/png" href="images\favicon.png">

  <title>Swans Cinema | Home</title>

  <!-- Styles -->
  <link rel="stylesheet" href="partial\header.css">
  <link rel="stylesheet" href="partial\footer.css">
  <link rel="stylesheet" href="css/index.css">
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body class="body-savi">

<!-- Main Header -->
<header class="header-savi">
  <div class="nav-container-savi">

    <!-- Left Logo (Swan + Text) -->
    <div class="logo-savi">
  <i class="fa-solid fa-video"></i> Swans Cinema
</div>

    <!-- Center Navigation -->
    <nav class="center-nav-savi">
      <a href="index.php" class="active-savi">Home</a>
      <a href="NowShowing" >Now Showing</a>
      <a href="aboutus.php" >About Us</a>
      <a href="contactus.php" >Contact Us</a>
    </nav>

    <!-- Right Section (Login + Profile) -->
    <div class="right-nav-savi">
      <a href="#" class="login-btn-savi">Login</a>
      <a href="#"><i class="fas fa-circle-user profile-icon-savi"></i></a>
    </div>

  </div>
</header>

<body class="body-savi">

  <!-- Header will be inserted here -->
  <div id="header-container"></div>

  <!-- Hero Section -->
  <section class="hero-savi">
    <div class="hero-bg-savi"></div>
    <div class="hero-content-savi">
      <h1 class="hero-title-savi">Experience Movies Like Never Before</h1>
      <p class="hero-subtitle-savi">Find your favorite movies and book your seats in seconds.</p>
      <form action="search.html" method="GET" class="hero-search-savi">
        <input type="text" id="searchInput" placeholder="Search movies...">
        <button type="submit"><i class="fas fa-search"></i></button>
      </form>
    </div>
  </section>

<div class="category-filters-savi">
  <button data-category="all" class="filter-btn-savi active">All</button>
  <button data-category="Action" class="filter-btn-savi">Action</button>
  <button data-category="Drama" class="filter-btn-savi">Drama</button>
  <button data-category="Thriller" class="filter-btn-savi">Thriller</button>
  <button data-category="Sci-Fi" class="filter-btn-savi">Sci-Fi</button>
  <button data-category="Fantasy" class="filter-btn-savi">Fantasy</button>
  <button data-category="Animation" class="filter-btn-savi">Animation</button>
  <button data-category="Comedy" class="filter-btn-savi">Comedy</button>
  <button data-category="Rom-Com" class="filter-btn-savi">Rom-Com</button>
  <button data-category="Music" class="filter-btn-savi">Music</button>
</div>

 <div class="all-movies-wrapper-savi" id="allMovies">

  <!-- 🔥 Trending Movies -->
  <section class="section-savi">
    <h2 class="section-title-savi"><i class="fas fa-fire"></i> Trending Movies</h2>
    <div class="scroll-container-wrapper-savi">
      <div class="scroll-container-savi auto-scroll-savi" id="trending-carousel">

        <div class="movie-card-savi" data-title="Avengers" data-category="Action">
          <img src="images/1.jpeg" alt="Avengers">
          <h3 class="movie-title-savi">Avengers</h3>
          <p class="movie-category-savi">Action</p>
        </div>

        <div class="movie-card-savi" data-title="Inception" data-category="Sci-Fi">
          <img src="images/1.jpeg" alt="Inception">
          <h3 class="movie-title-savi">Inception</h3>
          <p class="movie-category-savi">Sci-Fi</p>
        </div>

        <div class="movie-card-savi" data-title="John Wick" data-category="Thriller">
          <img src="images/1.jpeg" alt="John Wick">
          <h3 class="movie-title-savi">John Wick</h3>
          <p class="movie-category-savi">Thriller</p>
        </div>

        <div class="movie-card-savi" data-title="Interstellar" data-category="Adventure">
          <img src="images/1.jpeg" alt="Interstellar">
          <h3 class="movie-title-savi">Interstellar</h3>
          <p class="movie-category-savi">Adventure</p>
        </div>

        <div class="movie-card-savi" data-title="Dark Knight" data-category="Crime">
          <img src="images/1.jpeg" alt="Dark Knight">
          <h3 class="movie-title-savi">Dark Knight</h3>
          <p class="movie-category-savi">Crime</p>
        </div>

        <div class="movie-card-savi" data-title="Joker" data-category="Drama">
          <img src="images/1.jpeg" alt="Joker">
          <h3 class="movie-title-savi">Joker</h3>
          <p class="movie-category-savi">Drama</p>
        </div>

      </div>
    </div>
  </section>

  <!-- ⏳ Upcoming Movies -->
  <section class="section-savi">
    <h2 class="section-title-savi"><i class="fas fa-clock"></i> Upcoming Movies</h2>
    <div class="scroll-container-wrapper-savi">
      <div class="scroll-container-savi auto-scroll-savi" id="upcoming-carousel">

        <div class="movie-card-savi" data-title="Dune 2" data-category="Sci-Fi">
          <img src="images/1.jpeg" alt="Dune 2">
          <h3 class="movie-title-savi">Dune 2</h3>
          <p class="movie-category-savi">Sci-Fi</p>
        </div>

        <div class="movie-card-savi" data-title="Deadpool 3" data-category="Comedy">
          <img src="images/1.jpeg" alt="Deadpool 3">
          <h3 class="movie-title-savi">Deadpool 3</h3>
          <p class="movie-category-savi">Comedy</p>
        </div>

        <div class="movie-card-savi" data-title="MI: Reckoning" data-category="Action">
          <img src="images/1.jpeg" alt="MI: Reckoning">
          <h3 class="movie-title-savi">MI: Reckoning</h3>
          <p class="movie-category-savi">Action</p>
        </div>

        <div class="movie-card-savi" data-title="Blade" data-category="Fantasy">
          <img src="images/1.jpeg" alt="Blade">
          <h3 class="movie-title-savi">Blade</h3>
          <p class="movie-category-savi">Fantasy</p>
        </div>

        <div class="movie-card-savi" data-title="Kraven" data-category="Action">
          <img src="images/1.jpeg" alt="Kraven">
          <h3 class="movie-title-savi">Kraven</h3>
          <p class="movie-category-savi">Action</p>
        </div>

        <div class="movie-card-savi" data-title="Barbie 2" data-category="Rom-Com">
          <img src="images/1.jpeg" alt="Barbie 2">
          <h3 class="movie-title-savi">Barbie 2</h3>
          <p class="movie-category-savi">Rom-Com</p>
        </div>

      </div>
    </div>
  </section>

  <!-- 🎥 Other Films -->
  <section class="section-savi">
    <h2 class="section-title-savi"><i class="fas fa-film"></i> Other Films</h2>
    <div class="scroll-container-wrapper-savi">
      <div class="scroll-container-savi auto-scroll-savi" id="otherfilms-carousel">

        <div class="movie-card-savi" data-title="Frozen 2" data-category="Animation">
          <img src="images/1.jpeg" alt="Frozen 2">
          <h3 class="movie-title-savi">Frozen 2</h3>
          <p class="movie-category-savi">Animation</p>
        </div>

        <div class="movie-card-savi" data-title="Luca" data-category="Family">
          <img src="images/1.jpeg" alt="Luca">
          <h3 class="movie-title-savi">Luca</h3>
          <p class="movie-category-savi">Family</p>
        </div>

        <div class="movie-card-savi" data-title="Elemental" data-category="Fantasy">
          <img src="images/1.jpeg" alt="Elemental">
          <h3 class="movie-title-savi">Elemental</h3>
          <p class="movie-category-savi">Fantasy</p>
        </div>

        <div class="movie-card-savi" data-title="No Time To Die" data-category="Thriller">
          <img src="images/1.jpeg" alt="No Time To Die">
          <h3 class="movie-title-savi">No Time To Die</h3>
          <p class="movie-category-savi">Thriller</p>
        </div>

        <div class="movie-card-savi" data-title="Sing 2" data-category="Music">
          <img src="images/1.jpeg" alt="Sing 2">
          <h3 class="movie-title-savi">Sing 2</h3>
          <p class="movie-category-savi">Music</p>
        </div>

        <div class="movie-card-savi" data-title="The Whale" data-category="Drama">
          <img src="images/1.jpeg" alt="The Whale">
          <h3 class="movie-title-savi">The Whale</h3>
          <p class="movie-category-savi">Drama</p>
        </div>

      </div>
    </div>
  </section>

</div>


  <!-- partial/footer.php -->
<footer class="footer-savi">
     <!-- Styles -->
  <link rel="stylesheet" href="footer.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

  <div class="footer-container-savi">

    <!-- Quick Links -->
    <div class="footer-section-savi">
      <h3 class="footer-heading-savi">Quick Links</h3>
      <ul>
        <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
        <li><a href="#"><i class="fas fa-film"></i> Now Showing</a></li>
        <li><a href="#"><i class="fas fa-info-circle"></i> About Us</a></li>
        <li><a href="#"><i class="fas fa-phone"></i> Contact Us</a></li>
      </ul>
    </div>

    <!-- Follow Us -->
    <div class="footer-section-savi">
      <h3 class="footer-heading-savi">Follow Us</h3>
      <div class="footer-social-savi">
        <a href="#"><i class="fab fa-facebook"></i></a>
        <a href="#"><i class="fab fa-instagram"></i></a>
        <a href="#"><i class="fab fa-twitter"></i></a>
        <a href="#"><i class="fab fa-youtube"></i></a>
      </div>
    </div>

    <!-- Support -->
    <div class="footer-section-savi">
      <h3 class="footer-heading-savi">Support</h3>
      <p><i class="fas fa-envelope"></i> support@swanscinema.com</p>
      <p><i class="fas fa-phone"></i> +94 77 123 4567</p>
      <p><i class="fas fa-map-marker-alt"></i> No.123, Main Street, Colombo</p>
    </div>

    <!-- Payment -->
    <div class="footer-section-savi">
      <h3 class="footer-heading-savi">We Accept</h3>
      <div class="footer-pay-savi">
        <i class="fab fa-cc-visa"></i>
        <i class="fab fa-cc-mastercard"></i>
      </div>
    </div>
    
  </div>

  <div class="footer-bottom-savi">
    &copy; <?php echo date("Y"); ?> Swans Cinema. All Rights Reserved.
  </div>
</footer>


<script src="js/index.js"></script>
</body>
</html>
