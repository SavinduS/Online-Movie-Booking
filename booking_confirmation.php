<?php
session_start();

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
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Booking Confirmed - <?php echo htmlspecialchars($confirmation['booking_details']['movie_title']); ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#b793d2'
          },
          fontFamily: {
            'poppins': ['Poppins', 'sans-serif']
          }
        }
      }
    };
  </script>
</head>
<body class="bg-gray-50 font-poppins">
  <div class="min-h-screen py-8">
    <div class="max-w-2xl mx-auto px-4">
      <!-- Success Message -->
      <div class="text-center mb-8">
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <i class="fas fa-check text-3xl text-green-500"></i>
        </div>
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Booking Confirmed!</h1>
        <p class="text-gray-600">Your movie tickets have been successfully booked</p>
      </div>

      <!-- Booking Details -->
      <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-xl font-semibold text-gray-800">Booking Details</h2>
          <div class="text-right">
            <div class="text-sm text-gray-600">Booking ID</div>
            <div class="font-bold text-primary"><?php echo $confirmation['booking_id']; ?></div>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="space-y-3">
            <div>
              <div class="text-sm text-gray-600">Customer Name</div>
              <div class="font-semibold text-gray-800"><?php echo htmlspecialchars($confirmation['full_name']); ?></div>
            </div>
            <div>
              <div class="text-sm text-gray-600">Movie</div>
              <div class="font-semibold text-gray-800"><?php echo htmlspecialchars($confirmation['booking_details']['movie_title']); ?></div>
            </div>
            <div>
              <div class="text-sm text-gray-600">Cinema</div>
              <div class="font-semibold text-gray-800"><?php echo htmlspecialchars($confirmation['booking_details']['hall_name']); ?></div>
            </div>
            <div>
              <div class="text-sm text-gray-600">Date & Time</div>
              <div class="font-semibold text-gray-800">
                <?php echo date('l, F j, Y', strtotime($confirmation['booking_details']['date'])); ?> • 
                <?php echo $confirmation['booking_details']['time']; ?>
              </div>
            </div>
            
          </div>

          <div class="space-y-3">
            <div>
              <div class="text-sm text-gray-600">Selected Seats</div>
              <div class="font-semibold text-gray-800">
                <?php echo implode(', ', array_column($confirmation['selected_seats'], 'id')); ?>
              </div>
            </div>
            <div>
              <div class="text-sm text-gray-600">Number of Tickets</div>
              <div class="font-semibold text-gray-800"><?php echo count($confirmation['selected_seats']); ?> Ticket(s)</div>
            </div>
            <div>
              <div class="text-sm text-gray-600">Total Amount</div>
              <div class="font-semibold text-primary text-lg">Rs. <?php echo number_format($confirmation['total_amount']); ?></div>
            </div>
            <div>
              <div class="text-sm text-gray-600">Payment Method</div>
              <div class="font-semibold text-gray-800 capitalize"><?php echo $confirmation['payment_method']; ?></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Important Information -->
      <div class="bg-amber-50 border border-amber-200 rounded-lg p-6 mb-6">
        <h3 class="text-lg font-semibold text-amber-800 mb-3 flex items-center gap-2">
          <i class="fas fa-exclamation-triangle text-amber-600"></i>
          Important Information
        </h3>
        <div class="space-y-2 text-sm text-amber-700">
          <div class="flex items-start gap-2">
            <i class="fas fa-clock text-amber-600 mt-0.5"></i>
            <span>Please arrive at the cinema at least 15 minutes before showtime</span>
          </div>
          <div class="flex items-start gap-2">
            <i class="fas fa-mobile-alt text-amber-600 mt-0.5"></i>
            <span>Bring a valid ID and this booking confirmation</span>
          </div>
          <div class="flex items-start gap-2">
            <i class="fas fa-ban text-amber-600 mt-0.5"></i>
            <span>No refunds or exchanges allowed 2 hours before showtime</span>
          </div>
          <div class="flex items-start gap-2">
            <i class="fas fa-utensils text-amber-600 mt-0.5"></i>
            <span>Outside food and beverages are not permitted</span>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="space-y-3">
        <button onclick="window.print()" class="w-full bg-primary hover:bg-purple-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors flex items-center justify-center gap-2">
          <i class="fas fa-print"></i>
          Print Tickets
        </button>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
          <button class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold py-3 px-6 rounded-lg transition-colors flex items-center justify-center gap-2">
            <i class="fas fa-download"></i>
            Download PDF
          </button>
          <button class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold py-3 px-6 rounded-lg transition-colors flex items-center justify-center gap-2">
            <i class="fas fa-envelope"></i>
            Email Tickets
          </button>
        </div>

        <button onclick="window.location.href='index.php'" class="w-full bg-white hover:bg-gray-50 text-primary border-2 border-primary font-semibold py-3 px-6 rounded-lg transition-colors flex items-center justify-center gap-2">
          <i class="fas fa-home"></i>
          Back to Home
        </button>
      </div>

      <!-- Contact Support -->
      <div class="text-center mt-8 p-4 bg-white rounded-lg shadow-lg">
        <p class="text-sm text-gray-600 mb-2">Need help with your booking?</p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 text-sm">
          <a href="tel:+94112345678" class="flex items-center gap-2 text-primary hover:text-purple-700 transition-colors">
            <i class="fas fa-phone"></i>
            +94 11 234 5678
          </a>
          <a href="mailto:support@swanscinema.lk" class="flex items-center gap-2 text-primary hover:text-purple-700 transition-colors">
            <i class="fas fa-envelope"></i>
            support@swanscinema.lk
          </a>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Auto-clear the session after showing confirmation
    setTimeout(function () {
      fetch('clear_session.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
      });
    }, 5000);
  </script>
</body>
</html>
