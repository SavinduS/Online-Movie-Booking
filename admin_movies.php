<?php
// Connect to MySQL
$conn = new mysqli("localhost", "root", "12345", "movie_booking");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Movies</title>
  <link rel="stylesheet" href="css/wish_css_movies.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body id="wish-body">

  <div id="wish-wrapper">

    <!-- Header -->
    <header id="wish-header">
      <h1><i class="fas fa-film"></i> Manage Movies</h1>
      <button id="wish-add-btn" onclick="document.getElementById('wish-form').style.display='block'">Add New Movie</button>
    </header>

    <!-- Movie Table -->
    <section id="wish-movie-section">
      <table id="wish-movie-table">
        <thead>
          <tr>
            <th>Title</th>
            <th>Type</th>
            <th>Price</th>
            <th>Time</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $sql = "SELECT * FROM movies ORDER BY show_time ASC";
          $result = $conn->query($sql);
          if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
              echo "<tr>";
              echo "<td>" . htmlspecialchars($row['title']) . "</td>";
              echo "<td>" . $row['type'] . "</td>";
              echo "<td>$" . number_format($row['price'], 2) . "</td>";
              echo "<td>" . date("Y-m-d g:i A", strtotime($row['show_time'])) . "</td>";
              echo "<td>
                      <a href='edit_movie.php?id={$row['id']}'><i class='fas fa-pen-to-square' style='color:#b793d2'></i></a>
                      <a href='delete_movie.php?id={$row['id']}' onclick=\"return confirm('Are you sure?')\">
                        <i class='fas fa-trash-alt' style='color:#b793d2; margin-left:10px;'></i>
                      </a>
                    </td>";
              echo "</tr>";
            }
          } else {
            echo "<tr><td colspan='5'>No movies found.</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </section>

    <!-- Add Movie Form (Popup) -->
    <div id="wish-form">
      <div class="wish-form-box">
        <h2>Add Movie</h2>
        <form action="add_movie.php" method="post">
          <label>Title:</label>
          <input type="text" name="title" required>

          <label>Type:</label>
          <select name="type" required>
            <option value="2D">2D</option>
            <option value="3D">3D</option>
            <option value="IMAX">IMAX</option>
          </select>

          <label>Price ($):</label>
          <input type="number" name="price" step="0.01" required>

          <label>Showtime:</label>
          <input type="datetime-local" name="show_time" required>

          <div class="wish-btns">
            <button type="submit">Save</button>
            <button type="button" onclick="document.getElementById('wish-form').style.display='none'">Cancel</button>
          </div>
        </form>
      </div>
    </div>

  </div>

</body>
</html>
