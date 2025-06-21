<?php
$conn = new mysqli("localhost", "root", "", "movie_booking");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "
  SELECT 
    b.id,
    u.name AS user_name,
    m.title AS movie_title,
    m.show_time,
    b.seat_numbers,
    b.amount,
    b.status
  FROM bookings b
  JOIN users u ON b.user_id = u.id
  JOIN movies m ON b.movie_id = m.id
  ORDER BY m.show_time ASC
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Bookings</title>
  <link rel="stylesheet" href="css/wish_css_bookings.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body id="wish-body">

  <div id="wish-wrapper">
    
    <!-- Header -->
    <header id="wish-header">
      <h1><i class="fas fa-ticket-alt"></i> All Bookings</h1>
    </header>

    <!-- Booking Table -->
    <section id="wish-booking-section">
      <table id="wish-booking-table">
        <thead>
          <tr>
            <th>User</th>
            <th>Movie</th>
            <th>Showtime</th>
            <th>Seats</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars($row['user_name']) ?></td>
                <td><?= htmlspecialchars($row['movie_title']) ?></td>
                <td><?= date("Y-m-d g:i A", strtotime($row['show_time'])) ?></td>
                <td><?= htmlspecialchars($row['seat_numbers']) ?></td>
                <td>$<?= number_format($row['amount'], 2) ?></td>
                <td><span class="wish-status paid"><?= htmlspecialchars($row['status']) ?></span></td>
                <td>
                  <a href="delete_booking.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure?')">
                    <i class="fas fa-trash-alt" style="color: red;"></i>
                  </a>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="7">No bookings found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </section>
  </div>

</body>
</html>
