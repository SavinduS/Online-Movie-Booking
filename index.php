<?php include 'partial/header.php'; ?>
<?php include 'database/db.php'; ?>

<?php
// ✅ Reusable function to print movies
function displayMovies($connection, $status) {
  $sql = "SELECT * FROM films WHERE status = '$status' ORDER BY release_date DESC";
  $result = $connection->query($sql);

  if ($result && $result->num_rows > 0) {
    foreach ($result as $row) {
      echo '
      <div class="movie-card-savi" data-title="' . htmlspecialchars($row['title']) . '" data-category="' . htmlspecialchars($row['category']) . '">
        <div class="movie-poster-savi">
          <img src="' . htmlspecialchars($row['image']) . '" alt="' . htmlspecialchars($row['title']) . '">
          <div class="movie-overlay-savi">
            <button class="play-btn-savi"><i class="fas fa-play"></i></button>
            <button class="info-btn-savi"><i class="fas fa-info-circle"></i></button>
          </div>';

      if ($status === 'Upcoming') {
        echo '<div class="coming-soon-badge-savi">Coming Soon</div>';
      }

      echo '
        </div>
        <div class="movie-info-savi">
          <h3 class="movie-title-savi">' . htmlspecialchars($row['title']) . '</h3>
          <p class="movie-category-savi">' . htmlspecialchars($row['category']) . '</p>';

      if (!empty($row['rating'])) {
        echo '<div class="movie-rating-savi"><i class="fas fa-star"></i><span>' . htmlspecialchars($row['rating']) . '</span></div>';
      }

      echo '<div class="release-date-savi"><i class="fas fa-calendar"></i><span>' . htmlspecialchars($row['release_date']) . '</span></div>';

      echo '</div>
      </div>';
    }
  } else {
    echo '<p>No movies available in this section.</p>';
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Swans Cinema | Home </title>

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

  <!-- Custom CSS -->
  <link rel="stylesheet" href="css/index.css" />
</head>
<body>

  <!-- Hero Section -->
  <section class="hero-savi">
    <div class="hero-bg-savi"></div>
    <div class="hero-overlay-savi"></div>
    <div class="hero-content-savi">
      <h1 class="hero-title-savi">Experience Movies Like Never Before</h1>
      <p class="hero-subtitle-savi">Discover amazing films and book your perfect seats with ease</p>

      <form action="#container-savi" method="GET" class="hero-search-savi">
        <div class="search-wrapper-savi">
          <i class="fas fa-search search-icon-savi"></i>
          <input type="text" name="query" id="searchInput" placeholder="Search for movies or genres..." autocomplete="off">
          <button type="submit" class="search-btn-savi">
            <i class="fas fa-arrow-right"></i>
          </button>
        </div>
      </form>

      <div class="hero-stats-savi">
        <div class="stat-item-savi"><i class="fas fa-film"></i><span>500+ Movies</span></div>
        <div class="stat-item-savi"><i class="fas fa-couch"></i><span>Premium Seats</span></div>
        <div class="stat-item-savi"><i class="fas fa-ticket-alt"></i><span>Easy Booking</span></div>
      </div>
    </div>
  </section>

  <!-- Category Filters -->
  <div class="category-section-savi">
    <div class="container-savi">
      <h3 class="filter-title-savi">Browse by Category</h3>
      <div class="category-filters-savi">
        <button data-category="all" class="filter-btn-savi active"><i class="fas fa-star"></i> All Movies</button>
        <button data-category="Action" class="filter-btn-savi"><i class="fas fa-fist-raised"></i> Action</button>
        <button data-category="Drama" class="filter-btn-savi"><i class="fas fa-masks-theater"></i> Drama</button>
        <button data-category="Thriller" class="filter-btn-savi"><i class="fas fa-eye"></i> Thriller</button>
        <button data-category="Sci-Fi" class="filter-btn-savi"><i class="fas fa-rocket"></i> Sci-Fi</button>
        <button data-category="Fantasy" class="filter-btn-savi"><i class="fas fa-magic"></i> Fantasy</button>
        <button data-category="Animation" class="filter-btn-savi"><i class="fas fa-palette"></i> Animation</button>
        <button data-category="Comedy" class="filter-btn-savi"><i class="fas fa-laugh"></i> Comedy</button>
        <button data-category="Romance" class="filter-btn-savi"><i class="fas fa-heart"></i> Romance</button>
      </div>
    </div>
  </div>

  <!-- Movies Container -->
  <div class="all-movies-wrapper-savi" id="allMovies">

    <!-- 🔥 Trending Movies -->
    <section class="section-savi">
      <div class="container-savi">
        <div class="section-header-savi">
          <h2 class="section-title-savi"><i class="fas fa-fire"></i> Trending Movies</h2>
          <p class="section-subtitle-savi">Most popular movies this week</p>
        </div>
        <div class="scroll-container-wrapper-savi">
          <div class="scroll-container-savi" id="trending-carousel">
            <?php displayMovies($connection, 'trending'); ?>
          </div>
        </div>
      </div>
    </section>

    <!-- ⏳ Upcoming Movies -->
    <section class="section-savi">
      <div class="container-savi">
        <div class="section-header-savi">
          <h2 class="section-title-savi"><i class="fas fa-clock"></i> Upcoming Movies</h2>
          <p class="section-subtitle-savi">Coming soon to Swans Cinema</p>
        </div>
        <div class="scroll-container-wrapper-savi">
          <div class="scroll-container-savi" id="upcoming-carousel">
            <?php displayMovies($connection, 'Upcoming'); ?>
          </div>
        </div>
      </div>
    </section>

      <!-- 🔥 Now Showing -->
    <section class="section-savi">
      <div class="container-savi">
        <div class="section-header-savi">
          <h2 class="section-title-savi"><i class="fas fa-fire"></i> Now Showing</h2>
          <p class="section-subtitle-savi">Most popular movies this week</p>
        </div>
        <div class="scroll-container-wrapper-savi">
          <div class="scroll-container-savi" id="nowshowing-carousel">
            <?php displayMovies($connection, 'Now Showing'); ?>
          </div>
        </div>
      </div>
    </section>
  </div>

  <?php include 'partial/footer.php'; ?>
  <script src="js/index.js"></script>
</body>
</html>
