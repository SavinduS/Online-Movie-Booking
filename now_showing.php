<?php
include 'database/db.php'; // ✅ Reuse your existing mysqli connection

// Convert mysqli to PDO-style logic or update your logic to use mysqli:
$movies = [];
if ($connection) {
    $stmt = $connection->prepare("SELECT * FROM films WHERE status = 'Now Showing' ORDER BY rating DESC");
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $movies[] = $row;
    }
} else {
    $error_message = "Database connection failed.";
}


// ✅ Descriptions (optional, static for now)
$movie_descriptions = [
    'Frozen 2' => 'Anna, Elsa, Kristoff, Olaf and Sven leave Arendelle to travel to an ancient, autumn-bound forest of an enchanted land.',
    'Luca' => 'A young boy experiences an unforgettable summer filled with gelato, pasta and endless scooter rides.',
    'Elemental' => 'In a city where fire, water, land, and air residents live together...',
    'No Time To Die' => 'James Bond’s peace is short-lived when a friend from the CIA turns up asking for help.',
    'La La Land' => 'A jazz musician and an aspiring actress meet and fall in love in LA.',
    'The Whale' => 'A reclusive, morbidly obese English teacher tries to reconnect with his estranged daughter.'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Now Showing - Swans Cinema</title>
  <link rel="stylesheet" href="css/now_showing.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body>

<?php include 'partial/header.php'; ?>

<main class="main-content-savi">
  <div class="container-savi">
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
                     alt="<?php echo htmlspecialchars($movie['name']); ?>" 
                     class="movie-poster-savi" loading="lazy" />
                <div class="poster-overlay-savi">
                  <button class="book-now-btn-savi"><i class="fas fa-ticket-alt"></i> Book Now</button>
                </div>
                <div class="rating-badge-savi">
                  <i class="fas fa-star"></i>
                  <span><?php echo number_format($movie['rating'], 1); ?></span>
                </div>
              </div>
              <div class="movie-info-savi">
                <h3 class="movie-title-savi"><?php echo htmlspecialchars($movie['name']); ?></h3>
                <div class="movie-category-savi">
                  <i class="fas fa-tag"></i>
                  <span><?php echo htmlspecialchars($movie['category']); ?></span>
                </div>
                <?php if (!empty($movie_descriptions[$movie['name']])): ?>
                  <p class="movie-description-savi"><?php echo htmlspecialchars($movie_descriptions[$movie['name']]); ?></p>
                <?php endif; ?>
                <div class="movie-actions-savi">
                  <button class="btn-primary-savi book-ticket-btn-savi">
                    <i class="fas fa-calendar-alt"></i> Book Tickets
                  </button>
                  <button class="btn-secondary-savi view-details-btn-savi">
                    <i class="fas fa-info-circle"></i> Details
                  </button>
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
<script src="js/now_showing.js"></script>
</body>
</html>
