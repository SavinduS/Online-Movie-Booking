<?php
include 'database/db.php'; // ✅ mysqli connection

// Load "Now Showing" films from database
$movies = [];

if ($connection) {
    $stmt = $connection->prepare("SELECT * FROM films WHERE status = 'Now Showing'");
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $movies[] = $row;
    }
} else {
    $error_message = "Database connection failed.";
}
?>

<?php include 'partial/header.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Swans Cinema | Now Showing</title>
  <link rel="stylesheet" href="css/now_showing.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body>

<main class="main-content-savi">
  <div class="container-savi">
    <!-- Page Heading -->
    <section class="page-header-savi">
      <div class="header-content-savi">
        <h1 class="page-title-savi">
          <i class="fas fa-film icon-savi"></i> Now Showing
        </h1>
        <p class="page-subtitle-savi">Experience the magic of cinema with our current movie selection</p>
      </div>
      <div class="header-decoration-savi">
        <div class="decoration-line-savi"></div>
      </div>
    </section>

    <!-- Movies Section -->
    <section class="movies-section-savi">
      <?php if (isset($error_message)): ?>
        <div class="error-message-savi">
          <i class="fas fa-exclamation-triangle"></i>
          <p><?php echo $error_message; ?></p>
        </div>
      <?php elseif (empty($movies)): ?>
        <div class="no-movies-savi">
          <i class="fas fa-film"></i>
          <h3>No Movies Currently Showing</h3>
          <p>Check back soon for our latest movie selections!</p>
        </div>
      <?php else: ?>
        <div class="movies-grid-savi">
          <?php foreach ($movies as $movie): ?>
            <div class="movie-card-savi" data-movie-id="<?php echo $movie['id']; ?>">
              <div class="movie-poster-container-savi">
                <img src="<?php echo htmlspecialchars($movie['image']); ?>" 
                     alt="<?php echo htmlspecialchars($movie['title']); ?>" 
                     class="movie-poster-savi" loading="lazy" />
                <div class="poster-overlay-savi">
                  <a href="booking.php?movie_id=<?php echo $movie['id']; ?>&movie_title=<?php echo urlencode($movie['title']); ?>" class="book-now-btn-savi" style="text-decoration: none;">
                    <i class="fas fa-ticket-alt"></i> Book Now
                  </a>

                </div>
                <div class="rating-badge-savi">
                  <i class="fas fa-star"></i>
                  <span><?php echo number_format($movie['rating'], 1); ?></span>
                </div>
              </div>

              <div class="movie-info-savi">
                <h3 class="movie-title-savi"><?php echo htmlspecialchars($movie['title']); ?></h3>
                <div class="movie-category-savi">
                  <i class="fas fa-tag"></i>
                  <span><?php echo htmlspecialchars($movie['category']); ?></span>
                </div>

                <div class="movie-actions-savi">
                  <a href="booking.php?movie_id=<?php echo $movie['id']; ?>&movie_title=<?php echo urlencode($movie['title']); ?>" class="btn-primary-savi book-ticket-btn-savi" style="text-decoration: none;">
                    <i class="fas fa-calendar-alt"></i> Book Tickets
                  </a>

                  <a href="movie_details.php?movie_id=<?php echo $movie['id']; ?>" class="btn-secondary-savi view-details-btn-savi" style="text-decoration: none;">
                    <i class="fas fa-info-circle"></i> Details
                  </a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>
  </div>
</main>

<?php include 'partial/footer.php'; ?>

</body>
</html>
