<?php
include 'database/db.php';

$message = '';
$messageType = '';

// Fetch all films
$movies = [];
$sql = "SELECT * FROM films ORDER BY id DESC";
$result = $connection->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $movies[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin - Movie Management</title>
  <link rel="stylesheet" href="css/admin_movies.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <div class="container">
    <header class="header">
      <a href="admin_dashboard.php" class="back-btn">
        <i class="fas fa-arrow-left"></i> Back to Dashboard
      </a>
      <h1><i class="fas fa-film"></i> Movie Management</h1>
      <button class="add-btn" onclick="openModal('addModal')">
        <i class="fas fa-plus"></i> Add Movie
      </button>
    </header>

    <?php if (!empty($message)): ?>
      <div class="alert alert-<?= $messageType ?>">
        <i class="fas fa-<?= $messageType === 'success' ? 'check-circle' : 'exclamation-triangle' ?>"></i>
        <?= htmlspecialchars($message) ?>
      </div>
    <?php endif; ?>

    <div class="movies-grid">
      <?php if (empty($movies)): ?>
        <div class="no-movies">
          <i class="fas fa-film"></i>
          <h3>No movies found</h3>
          <p>Start by adding your first movie!</p>
        </div>
      <?php else: ?>
        <?php foreach ($movies as $movie): ?>
          <div class="movie-card">
            <div class="movie-poster">
              <?php if (!empty($movie['image']) && file_exists($movie['image'])): ?>
                <img src="<?= htmlspecialchars($movie['image']) ?>" alt="<?= htmlspecialchars($movie['title']) ?>">
              <?php else: ?>
                <div class="no-poster">
                  <i class="fas fa-image"></i>
                  <span>No Image</span>
                </div>
              <?php endif; ?>
            </div>
            
            <div class="movie-info">
              <h3 class="movie-title"><?= htmlspecialchars($movie['title']) ?></h3>
              <div class="movie-details">
                <span class="movie-category">
                  <i class="fas fa-tag"></i>
                  <?= htmlspecialchars($movie['category']) ?>
                </span>
                <span class="movie-rating">
                  <i class="fas fa-star"></i>
                  <?= htmlspecialchars($movie['rating']) ?>/10
                </span>
                <span class="movie-status">
                  <i class="fas fa-circle"></i>
                  <?= htmlspecialchars($movie['status']) ?>
                </span>
                <span class="movie-release">
                  <i class="fas fa-calendar"></i>
                  <?= date("M d, Y", strtotime($movie['release_date'])) ?>
                </span>
              </div>
            </div>

            <div class="movie-actions">
              <button class="btn btn-edit" onclick="openEditModal(<?= htmlspecialchars(json_encode($movie)) ?>)">
                <i class="fas fa-edit"></i> Edit
              </button>
              <a href="delete_movie.php?id=<?= $movie['id'] ?>" 
                 class="btn btn-delete" 
                 onclick="return confirm('Are you sure you want to delete this movie?')">
                <i class="fas fa-trash"></i> Delete
              </a>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  <!-- Add Movie Modal -->
  <div id="addModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2><i class="fas fa-plus"></i> Add New Movie</h2>
        <span class="close" onclick="closeModal('addModal')">&times;</span>
      </div>
      <form action="add_movie.php" method="POST" enctype="multipart/form-data" class="movie-form">
        <div class="form-group">
          <label for="title"><i class="fas fa-film"></i> Title:</label>
          <input type="text" id="title" name="title" required>
        </div>

        <div class="form-group">
          <label for="category"><i class="fas fa-tag"></i> Category:</label>
          <select id="category" name="category" required>
            <option value="">Select Category</option>
            <option value="Action">Action</option>
            <option value="Sci-Fi">Sci-Fi</option>
            <option value="Thriller">Thriller</option>
            <option value="Drama">Drama</option>
            <option value="Comedy">Comedy</option>
            <option value="Fantasy">Fantasy</option>
            <option value="Animation">Animation</option>
            <option value="Romance">Romance</option>
          </select>
        </div>

        <div class="form-group">
          <label for="rating"><i class="fas fa-star"></i> Rating:</label>
          <input type="number" id="rating" name="rating" step="0.1" min="0" max="10" required>
        </div>

        <div class="form-group">
          <label for="status"><i class="fas fa-circle"></i> Status:</label>
          <select id="status" name="status" required>
            <option value="">Select Status</option>
            <option value="Now Showing">Now Showing</option>
            <option value="Trending">Trending</option>
            <option value="Upcoming">Upcoming</option>
          </select>
        </div>

        <div class="form-group">
          <label for="release_date"><i class="fas fa-calendar"></i> Release Date:</label>
          <input type="date" id="release_date" name="release_date" required>
        </div>

        <div class="form-group">
          <label for="image"><i class="fas fa-image"></i> Movie Image:</label>
          <input type="file" id="image" name="image" accept="image/*">
          <small>Supported formats: JPG, PNG, GIF (Max: 5MB)</small>
        </div>

        <div class="form-actions">
          <button type="button" class="btn btn-cancel" onclick="closeModal('addModal')">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Save Movie
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Edit Movie Modal -->
  <div id="editModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2><i class="fas fa-edit"></i> Edit Movie</h2>
        <span class="close" onclick="closeModal('editModal')">&times;</span>
      </div>
      <form action="edit_movie.php" method="POST" enctype="multipart/form-data" class="movie-form">
        <input type="hidden" id="edit_id" name="id">
        
        <div class="form-group">
          <label for="edit_title"><i class="fas fa-film"></i> Title:</label>
          <input type="text" id="edit_title" name="title" required>
        </div>

        <div class="form-group">
          <label for="edit_category"><i class="fas fa-tag"></i> Category:</label>
          <select id="edit_category" name="category" required>
            <option value="Action">Action</option>
            <option value="Sci-Fi">Sci-Fi</option>
            <option value="Thriller">Thriller</option>
            <option value="Drama">Drama</option>
            <option value="Comedy">Comedy</option>
            <option value="Fantasy">Fantasy</option>
            <option value="Animation">Animation</option>
            <option value="Romance">Romance</option>
          </select>
        </div>

        <div class="form-group">
          <label for="edit_rating"><i class="fas fa-star"></i> Rating:</label>
          <input type="number" id="edit_rating" name="rating" step="0.1" min="0" max="10" required>
        </div>

        <div class="form-group">
          <label for="edit_status"><i class="fas fa-circle"></i> Status:</label>
          <select id="edit_status" name="status" required>
            <option value="Now Showing">Now Showing</option>
            <option value="Trending">Trending</option>
            <option value="Upcoming">Upcoming</option>
          </select>
        </div>

        <div class="form-group">
          <label for="edit_release_date"><i class="fas fa-calendar"></i> Release Date:</label>
          <input type="date" id="edit_release_date" name="release_date" required>
        </div>

        <div class="form-group">
          <label for="edit_image"><i class="fas fa-image"></i> New Image:</label>
          <input type="file" id="edit_image" name="image" accept="image/*">
          <small>Leave empty to keep current image</small>
        </div>

        <div class="form-actions">
          <button type="button" class="btn btn-cancel" onclick="closeModal('editModal')">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Update Movie
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function openModal(modalId) {
      document.getElementById(modalId).style.display = 'block';
      document.body.style.overflow = 'hidden';
    }

    function closeModal(modalId) {
      document.getElementById(modalId).style.display = 'none';
      document.body.style.overflow = 'auto';
    }

    function openEditModal(movie) {
      document.getElementById('edit_id').value = movie.id;
      document.getElementById('edit_title').value = movie.title;
      document.getElementById('edit_category').value = movie.category;
      document.getElementById('edit_rating').value = movie.rating;
      document.getElementById('edit_status').value = movie.status;
      
      // Format date for input field
      const releaseDate = new Date(movie.release_date);
      const formattedDate = releaseDate.toISOString().slice(0, 10);
      document.getElementById('edit_release_date').value = formattedDate;
      
      openModal('editModal');
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
      const modals = document.getElementsByClassName('modal');
      for (let modal of modals) {
        if (event.target === modal) {
          closeModal(modal.id);
        }
      }
    }

    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
      setTimeout(() => {
        alert.style.opacity = '0';
        setTimeout(() => {
          alert.remove();
        }, 300);
      }, 5000);
    });
  </script>
</body>
</html>