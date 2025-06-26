<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    // User not logged in, redirect to login page
    header("Location: Login_index.php");
    exit();
}

// Check if booking confirmation exists
if (!isset($_SESSION['booking_confirmation'])) {
    header('Location: booking.php');
    exit;
}

$confirmation = $_SESSION['booking_confirmation'];
// Include your external DB connection
include 'database/db.php';

// Extract values
    $booking_id = $confirmation['booking_id'];
    $full_name = $connection->real_escape_string($confirmation['full_name']);
    $email = $connection->real_escape_string($confirmation['email']);
    $phone = $connection->real_escape_string($confirmation['phone']);
    $movie_title = $connection->real_escape_string($confirmation['booking_details']['movie_title']);
    $hall_name = $connection->real_escape_string($confirmation['booking_details']['hall_name']);
    $show_date = $confirmation['booking_details']['date'];
    $show_time = $confirmation['booking_details']['time'];
    $selected_seats = $connection->real_escape_string(implode(', ', array_column($confirmation['selected_seats'], 'id')));
    $number_of_tickets = count($confirmation['selected_seats']);
    $total_amount = $confirmation['total_amount'];
    $payment_method = $connection->real_escape_string($confirmation['payment_method']);
    $booking_date = date('Y-m-d H:i:s');

    // SQL Query
    $sql = "INSERT INTO bookings (
        booking_id, full_name, email, phone, movie_title, hall_name, show_date, show_time,
        selected_seats, number_of_tickets, total_amount, payment_method, booking_date
    ) VALUES (
        '$booking_id', '$full_name', '$email', '$phone', '$movie_title', '$hall_name',
        '$show_date', '$show_time', '$selected_seats', $number_of_tickets, $total_amount,
        '$payment_method', '$booking_date'
    )";

    //execute query
    $connection->query($sql)

?>

<!DOCTYPE html>
<html lang="en">

<?php include 'partial/header.php'; ?>
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Booking Confirmed - <?php echo htmlspecialchars($confirmation['booking_details']['movie_title']); ?></title>
  <script src="js/booking_confirmation"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="css/booking_confirmation.css" />
</head>
<body>
  <div class="container">
    <div class="content-wrapper">
      <!-- Success Message -->
      <div class="success-section">
        <div class="success-icon">
          <i class="fas fa-check"></i>
        </div>
        <h1 class="success-title">Booking Confirmed!</h1>
        <p class="success-subtitle">Your movie tickets have been successfully booked</p>
      </div>

      <!-- Booking Details -->
      <div class="booking-details-card">
        <div class="card-header">
          <h2 class="card-title">Booking Details</h2>
          <div class="booking-id-section">
            <div class="booking-id-label">Booking ID</div>
            <div class="booking-id-value"><?php echo $confirmation['booking_id']; ?></div>
          </div>
        </div>

        <div class="details-grid">
          <div class="details-column">
            <div class="detail-item">
              <div class="detail-label">Customer Name</div>
              <div class="detail-value"><?php echo htmlspecialchars($confirmation['full_name']); ?></div>
            </div>
            <div class="detail-item">
              <div class="detail-label">Movie</div>
              <div class="detail-value"><?php echo htmlspecialchars($confirmation['booking_details']['movie_title']); ?></div>
            </div>
            <div class="detail-item">
              <div class="detail-label">Cinema</div>
              <div class="detail-value"><?php echo htmlspecialchars($confirmation['booking_details']['hall_name']); ?></div>
            </div>
            <div class="detail-item">
              <div class="detail-label">Date & Time</div>
              <div class="detail-value">
                <?php echo date('l, F j, Y', strtotime($confirmation['booking_details']['date'])); ?> • 
                <?php echo $confirmation['booking_details']['time']; ?>
              </div>
            </div>
          </div>

          <div class="details-column">
            <div class="detail-item">
              <div class="detail-label">Selected Seats</div>
              <div class="detail-value">
                <?php echo implode(', ', array_column($confirmation['selected_seats'], 'id')); ?>
              </div>
            </div>
            <div class="detail-item">
              <div class="detail-label">Number of Tickets</div>
              <div class="detail-value"><?php echo count($confirmation['selected_seats']); ?> Ticket(s)</div>
            </div>
            <div class="detail-item">
              <div class="detail-label">Total Amount</div>
              <div class="detail-value total-amount">Rs. <?php echo number_format($confirmation['total_amount']); ?></div>
            </div>
            <div class="detail-item">
              <div class="detail-label">Payment Method</div>
              <div class="detail-value payment-method"><?php echo $confirmation['payment_method']; ?></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Important Information -->
      <div class="important-info-card">
        <h3 class="important-info-title">
          <i class="fas fa-exclamation-triangle"></i>
          Important Information
        </h3>
        <div class="important-info-list">
          <div class="info-item">
            <i class="fas fa-clock"></i>
            <span>Please arrive at the cinema at least 15 minutes before showtime</span>
          </div>
          <div class="info-item">
            <i class="fas fa-mobile-alt"></i>
            <span>Bring a valid ID and this booking confirmation</span>
          </div>
          <div class="info-item">
            <i class="fas fa-ban"></i>
            <span>No refunds or exchanges allowed 2 hours before showtime</span>
          </div>
          <div class="info-item">
            <i class="fas fa-utensils"></i>
            <span>Outside food and beverages are not permitted</span>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="action-buttons">
        <button onclick="window.print()" class="btn btn-primary btn-full">
          <i class="fas fa-print"></i>
          Print Tickets
        </button>

        <div class="btn-grid">
          <button class="btn btn-secondary">
            <i class="fas fa-download"></i>
            Download PDF
          </button>
          <button class="btn btn-secondary">
            <i class="fas fa-envelope"></i>
            Email Tickets
          </button>
        </div>

        <button onclick="window.location.href='index.php'" class="btn btn-outline btn-full">
          <i class="fas fa-home"></i>
          Back to Home
        </button>
      </div>

      <!-- Contact Support -->
      <div class="contact-support">
        <p class="contact-text">Need help with your booking?</p>
        <div class="contact-links">
          <a href="tel:+94112345678" class="contact-link">
            <i class="fas fa-phone"></i>
            +94 11 234 5678
          </a>
          <a href="mailto:support@swanscinema.lk" class="contact-link">
            <i class="fas fa-envelope"></i>
            support@swanscinema.lk
          </a>
        </div>
      </div>
    </div>
  </div>

  <?php include 'partial/footer.php'; ?>
</body>
</html>
