<?php
session_start();

include 'database/db.php';

// Check if booking details exist
if (!isset($_SESSION['booking_details'])) {
    header('Location: booking.php');
    exit;
}

$booking_details = $_SESSION['booking_details'];

// Sample seat layout
$total_rows = 12;
$seats_per_row = ['A' => 7, 'B' => 7, 'C' => 7, 'D' => 7, 'E' => 7, 'F' => 7, 'G' => 7, 'H' => 7, 'I' => 12, 'J' => 10, 'K' => 14, 'L' => 14];

// Fetch reserved seats from database
$reserved_seats = [];

$hall = $connection->real_escape_string($booking_details['hall_name']);
$date = $connection->real_escape_string($booking_details['date']);
$time = $connection->real_escape_string($booking_details['time']);

$sql = "SELECT selected_seats FROM bookings 
        WHERE hall_name = '$hall' 
        AND show_date = '$date' 
        AND show_time = '$time'";

$result = $connection->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Handle both JSON and comma-separated formats
        $seats_data = $row['selected_seats'];
        
        // Try to decode as JSON first
        $json_seats = json_decode($seats_data, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($json_seats)) {
            // If it's JSON, extract seat IDs
            foreach ($json_seats as $seat) {
                if (is_array($seat) && isset($seat['id'])) {
                    $reserved_seats[] = $seat['id'];
                } else {
                    $reserved_seats[] = $seat;
                }
            }
        } else {
            // If not JSON, treat as comma-separated string
            $seats = explode(',', $seats_data);
            foreach ($seats as $seat) {
                $reserved_seats[] = trim($seat);
            }
        }
    }
}

// Unavailable seats 
$unavailable_seats = ['G1', 'F1', 'F2', 'E6', 'E7'];

// Seat prices
$seat_prices = [
    'A' => 800, 'B' => 800, 'C' => 800, 'D' => 800, // Regular
    'E' => 1000, 'F' => 1000, 'G' => 1000, 'H' => 1000, // Premium
    'I' => 1200, 'J' => 1200, 'K' => 1200, 'L' => 1200 // VIP
];

if ($_POST && isset($_POST['selected_seats'])) {
    $_SESSION['selected_seats'] = $_POST['selected_seats'];
    $_SESSION['total_amount'] = $_POST['total_amount'];
    header('Location: checkout.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<?php include 'partial/header.php'; ?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Seats - <?php echo htmlspecialchars($booking_details['movie_title']); ?></title>
    <script src="js/seat_booking.js"></script>
    <link rel="stylesheet" href="css/seat_booking.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Seat button styles */
        .seat-btn {
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        /* Available seats */
        .bg-gray-200 {
            background-color: #e5e7eb;
            color: #374151;
        }
        
        /* Selected seats */
        .bg-primary {
            background-color: #3b82f6 !important;
            color: white !important;
            border-color: #2563eb;
        }
        
        .text-white {
            color: white !important;
        }
        
        /* Reserved seats */
        .seat-reserved {
            background-color: #ef4444;
            color: white;
            cursor: not-allowed;
        }
        
        /* Unavailable seats */
        .seat-unavailable {
            background-color: #9ca3af;
            color: #6b7280;
            cursor: not-allowed;
        }
        
        /* Hover effect for available seats */
        .seat-available:hover:not(.bg-primary) {
            background-color: #d1d5db;
            transform: scale(1.05);
        }
        
        /* Legend colors */
        .legend-color {
            width: 20px;
            height: 20px;
            border-radius: 4px;
            display: inline-block;
        }
        
        .legend-color.selected {
            background-color: #3b82f6;
        }
        
        .legend-color.available {
            background-color: #e5e7eb;
        }
        
        .legend-color.reserved {
            background-color: #ef4444;
        }
        
        .legend-color.unavailable {
            background-color: #9ca3af;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="main-wrapper">
            <!-- Header -->
            <div class="header-card">
                <div class="header-content">
                    <div class="header-info">
                        <button onclick="history.back()" class="back-btn">
                            <i class="fas fa-arrow-left"></i>
                        </button>
                        <div class="movie-info">
                            <h1 class="movie-title"><?php echo htmlspecialchars($booking_details['movie_title']); ?></h1>
                            <p class="movie-details">
                                <?php echo htmlspecialchars($booking_details['hall_name']); ?> • 
                                <?php echo htmlspecialchars($booking_details['date']); ?> • 
                                <?php echo htmlspecialchars($booking_details['time']); ?> • 
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="main-grid">
                <!-- Seat Selection Area -->
                <div class="seat-selection-area">
                    <!-- Screen -->
                    <div class="screen-section">
                        <div class="screen-line"></div>
                        <div class="screen-label">SCREEN</div>
                    </div>

                    <!-- Balcony Section -->
                    <div class="seating-section">
                        <div class="section-header balcony-header">
                            <i class="fas fa-star"></i>
                            BALCONY
                        </div>
                        <div class="seats-container">
                            <div class="seats-grid">
                                <?php
                                $balcony_rows = ['L', 'K', 'J', 'I'];
                                foreach ($balcony_rows as $row):
                                    $seats_in_row = $seats_per_row[$row];
                                ?>
                                <div class="seat-row">
                                    <span class="row-label"><?php echo $row; ?></span>
                                    <div class="seats-in-row">
                                        <?php for ($seat = 1; $seat <= $seats_in_row; $seat++): 
                                            $seat_id = $row . $seat;
                                            $is_reserved = in_array($seat_id, $reserved_seats);
                                            $is_unavailable = in_array($seat_id, $unavailable_seats);
                                            $price = $seat_prices[$row];
                                            
                                            $seat_class = 'seat-btn';
                                            if ($is_reserved) {
                                                $seat_class .= ' seat-reserved';
                                            } elseif ($is_unavailable) {
                                                $seat_class .= ' seat-unavailable';
                                            } else {
                                                $seat_class .= ' seat-available bg-gray-200';
                                            }
                                        ?>
                                        <button type="button" 
                                                class="<?php echo $seat_class; ?>"
                                                data-seat="<?php echo $seat_id; ?>"
                                                data-price="<?php echo $price; ?>"
                                                data-row="<?php echo $row; ?>"
                                                <?php if ($is_reserved || $is_unavailable): ?>disabled<?php endif; ?>>
                                            <?php echo $seat; ?>
                                        </button>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- ODC Section -->
                    <div class="seating-section">
                        <div class="section-header odc-header">
                            <i class="fas fa-crown"></i>
                            ODC (Premium)
                        </div>
                        <div class="seats-container">
                            <div class="seats-grid">
                                <?php
                                $odc_rows = ['H', 'G', 'F', 'E'];
                                foreach ($odc_rows as $row):
                                    $seats_in_row = $seats_per_row[$row];
                                ?>
                                <div class="seat-row">
                                    <span class="row-label"><?php echo $row; ?></span>
                                    <div class="seats-in-row">
                                        <?php for ($seat = 1; $seat <= $seats_in_row; $seat++): 
                                            $seat_id = $row . $seat;
                                            $is_reserved = in_array($seat_id, $reserved_seats);
                                            $is_unavailable = in_array($seat_id, $unavailable_seats);
                                            $price = $seat_prices[$row];
                                            
                                            $seat_class = 'seat-btn';
                                            if ($is_reserved) {
                                                $seat_class .= ' seat-reserved';
                                            } elseif ($is_unavailable) {
                                                $seat_class .= ' seat-unavailable';
                                            } else {
                                                $seat_class .= ' seat-available bg-gray-200';
                                            }
                                        ?>
                                        <button type="button" 
                                                class="<?php echo $seat_class; ?>"
                                                data-seat="<?php echo $seat_id; ?>"
                                                data-price="<?php echo $price; ?>"
                                                data-row="<?php echo $row; ?>"
                                                <?php if ($is_reserved || $is_unavailable): ?>disabled<?php endif; ?>>
                                            <?php echo $seat; ?>
                                        </button>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Regular Section -->
                    <div class="seating-section">
                        <div class="section-header regular-header">
                            <i class="fas fa-chair"></i>
                            Regular
                        </div>
                        <div class="seats-container">
                            <div class="seats-grid">
                                <?php
                                $regular_rows = ['D', 'C', 'B', 'A'];
                                foreach ($regular_rows as $row):
                                    $seats_in_row = $seats_per_row[$row];
                                ?>
                                <div class="seat-row">
                                    <span class="row-label"><?php echo $row; ?></span>
                                    <div class="seats-in-row">
                                        <?php for ($seat = 1; $seat <= $seats_in_row; $seat++): 
                                            $seat_id = $row . $seat;
                                            $is_reserved = in_array($seat_id, $reserved_seats);
                                            $is_unavailable = in_array($seat_id, $unavailable_seats);
                                            $price = $seat_prices[$row];
                                            
                                            $seat_class = 'seat-btn';
                                            if ($is_reserved) {
                                                $seat_class .= ' seat-reserved';
                                            } elseif ($is_unavailable) {
                                                $seat_class .= ' seat-unavailable';
                                            } else {
                                                $seat_class .= ' seat-available bg-gray-200';
                                            }
                                        ?>
                                        <button type="button" 
                                                class="<?php echo $seat_class; ?>"
                                                data-seat="<?php echo $seat_id; ?>"
                                                data-price="<?php echo $price; ?>"
                                                data-row="<?php echo $row; ?>"
                                                <?php if ($is_reserved || $is_unavailable): ?>disabled<?php endif; ?>>
                                            <?php echo $seat; ?>
                                        </button>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Legend -->
                    <div class="legend-section">
                        <div class="legend-items">
                            <div class="legend-item">
                                <div class="legend-color selected"></div>
                                <span>Selected</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color available"></div>
                                <span>Available</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color reserved"></div>
                                <span>Reserved</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color unavailable"></div>
                                <span>Unavailable</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Booking Summary -->
                <div class="booking-summary">
                    <h3 class="summary-title">
                        <i class="fas fa-ticket-alt"></i>
                        Booking Summary
                    </h3>
                    
                    <div class="summary-details">
                        <div class="detail-row">
                            <span class="detail-label">Movie:</span>
                            <span class="detail-value"><?php echo htmlspecialchars($booking_details['movie_title']); ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Cinema:</span>
                            <span class="detail-value"><?php echo htmlspecialchars($booking_details['hall_name']); ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Date:</span>
                            <span class="detail-value"><?php echo date('M d, Y', strtotime($booking_details['date'])); ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Time:</span>
                            <span class="detail-value"><?php echo htmlspecialchars($booking_details['time']); ?></span>
                        </div>
                    </div>

                    <div class="selected-seats-section">
                        <div id="selected-seats">
                            <h4 class="selected-seats-title">Selected Seats:</h4>
                            <div id="seat-list" class="seat-list">
                                <p class="text-center italic">No seats selected</p>
                            </div>
                        </div>

                        <div class="total-section">
                            <div class="total-row">
                                <span class="total-label">Total:</span>
                                <span id="total-amount" class="total-amount">Rs. 0</span>
                            </div>

                            <form method="POST" id="booking-form">
                                <input type="hidden" name="selected_seats" id="selected-seats-input">
                                <input type="hidden" name="total_amount" id="total-amount-input">
                                
                                <button type="submit" 
                                        id="continue-btn"
                                        class="continue-btn"
                                        disabled>
                                    <i class="fas fa-credit-card"></i>
                                    Continue to Payment
                                    <i class="fas fa-arrow-right"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'partial/footer.php'; ?>
</body>
</html>