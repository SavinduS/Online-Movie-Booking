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
include 'database/db.php';

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
        $seats = json_decode($row['selected_seats'], true);
        if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Expecting comma-separated string like "A1, A2"
        $seats = explode(',', $row['selected_seats']);
        foreach ($seats as $seat) {
            $reserved_seats[] = trim($seat); // Remove any whitespace
        }
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
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Seats - <?php echo htmlspecialchars($booking_details['movie_title']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
        }
    </script>
</head>
<body class="bg-gray-50 font-poppins">
    <div class="min-h-screen py-4">
        <div class="max-w-7xl mx-auto px-4">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-lg p-4 mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <button onclick="history.back()" class="text-primary hover:text-purple-700 transition-colors">
                            <i class="fas fa-arrow-left text-xl"></i>
                        </button>
                        <div>
                            <h1 class="text-xl font-bold text-gray-800"><?php echo htmlspecialchars($booking_details['movie_title']); ?></h1>
                            <p class="text-sm text-gray-600">
                                <?php echo htmlspecialchars($booking_details['hall_name']); ?> • 
                                <?php echo htmlspecialchars($booking_details['date']); ?> • 
                                <?php echo htmlspecialchars($booking_details['time']); ?> • 
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Seat Selection Area -->
                <div class="lg:col-span-3 bg-white rounded-lg shadow-lg p-6">
                    <!-- Screen -->
                    <div class="mb-8">
                        <div class="w-full h-2 bg-gradient-to-r from-transparent via-primary to-transparent rounded-full mb-2"></div>
                        <div class="text-center text-sm text-gray-600 font-medium">SCREEN</div>
                    </div>

                    <!-- Balcony Section -->
                    <div class="mb-6">
                        <div class="text-center text-sm font-semibold text-gray-700 mb-3 bg-gray-100 py-2 rounded">
                            <i class="fas fa-star text-yellow-500 mr-1"></i>
                            BALCONY
                        </div>
                        <div class="flex justify-center">
                            <div class="grid gap-2">
                                <?php
                                $balcony_rows = ['L', 'K', 'J', 'I'];
                                foreach ($balcony_rows as $row):
                                    $seats_in_row = $seats_per_row[$row];
                                ?>
                                <div class="flex items-center gap-2 justify-center">
                                    <span class="w-8 text-center font-semibold text-gray-700"><?php echo $row; ?></span>
                                    <div class="flex gap-1">
                                        <?php for ($seat = 1; $seat <= $seats_in_row; $seat++): 
                                            $seat_id = $row . $seat;
                                            $is_reserved = in_array($seat_id, $reserved_seats);
                                            $is_unavailable = in_array($seat_id, $unavailable_seats);
                                            $price = $seat_prices[$row];
                                        ?>
                                        <button type="button" 
                                                class="seat-btn w-8 h-8 text-xs font-medium rounded transition-all duration-200 
                                                <?php if ($is_reserved): ?>
                                                    bg-red-500 text-white cursor-not-allowed
                                                <?php elseif ($is_unavailable): ?>
                                                    bg-pink-300 text-white cursor-not-allowed
                                                <?php else: ?>
                                                    bg-gray-200 hover:bg-primary hover:text-white cursor-pointer
                                                <?php endif; ?>"
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
                    <div class="mb-6">
                        <div class="text-center text-sm font-semibold text-gray-700 mb-3 bg-gray-100 py-2 rounded">
                            <i class="fas fa-crown text-primary mr-1"></i>
                            ODC (Premium)
                        </div>
                        <div class="flex justify-center">
                            <div class="grid gap-2">
                                <?php
                                $odc_rows = ['H', 'G', 'F', 'E'];
                                foreach ($odc_rows as $row):
                                    $seats_in_row = $seats_per_row[$row];
                                ?>
                                <div class="flex items-center gap-2 justify-center">
                                    <span class="w-8 text-center font-semibold text-gray-700"><?php echo $row; ?></span>
                                    <div class="flex gap-1">
                                        <?php for ($seat = 1; $seat <= $seats_in_row; $seat++): 
                                            $seat_id = $row . $seat;
                                            $is_reserved = in_array($seat_id, $reserved_seats);
                                            $is_unavailable = in_array($seat_id, $unavailable_seats);
                                            $price = $seat_prices[$row];
                                        ?>
                                        <button type="button" 
                                                class="seat-btn w-8 h-8 text-xs font-medium rounded transition-all duration-200 
                                                <?php if ($is_reserved): ?>
                                                    bg-red-500 text-white cursor-not-allowed
                                                <?php elseif ($is_unavailable): ?>
                                                    bg-pink-300 text-white cursor-not-allowed
                                                <?php else: ?>
                                                    bg-gray-200 hover:bg-primary hover:text-white cursor-pointer
                                                <?php endif; ?>"
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
                    <div class="mb-6">
                        <div class="text-center text-sm font-semibold text-gray-700 mb-3 bg-gray-100 py-2 rounded">
                            <i class="fas fa-chair text-blue-500 mr-1"></i>
                            Regular
                        </div>
                        <div class="flex justify-center">
                            <div class="grid gap-2">
                                <?php
                                $regular_rows = ['D', 'C', 'B', 'A'];
                                foreach ($regular_rows as $row):
                                    $seats_in_row = $seats_per_row[$row];
                                ?>
                                <div class="flex items-center gap-2 justify-center">
                                    <span class="w-8 text-center font-semibold text-gray-700"><?php echo $row; ?></span>
                                    <div class="flex gap-1">
                                        <?php for ($seat = 1; $seat <= $seats_in_row; $seat++): 
                                            $seat_id = $row . $seat;
                                            $is_reserved = in_array($seat_id, $reserved_seats);
                                            $is_unavailable = in_array($seat_id, $unavailable_seats);
                                            $price = $seat_prices[$row];
                                        ?>
                                        <button type="button" 
                                                class="seat-btn w-8 h-8 text-xs font-medium rounded transition-all duration-200 
                                                <?php if ($is_reserved): ?>
                                                    bg-red-500 text-white cursor-not-allowed
                                                <?php elseif ($is_unavailable): ?>
                                                    bg-pink-300 text-white cursor-not-allowed
                                                <?php else: ?>
                                                    bg-gray-200 hover:bg-primary hover:text-white cursor-pointer
                                                <?php endif; ?>"
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
                    <div class="border-t pt-4">
                        <div class="flex flex-wrap justify-center gap-6 text-sm">
                            <div class="flex items-center gap-2">
                                <div class="w-4 h-4 bg-primary rounded"></div>
                                <span>Selected</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-4 h-4 bg-gray-200 rounded"></div>
                                <span>Available</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-4 h-4 bg-red-500 rounded"></div>
                                <span>Reserved</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-4 h-4 bg-pink-300 rounded"></div>
                                <span>Unavailable</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Booking Summary -->
                <div class="bg-white rounded-lg shadow-lg p-6 h-fit">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-ticket-alt text-primary"></i>
                        Booking Summary
                    </h3>
                    
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Movie:</span>
                            <span class="font-medium"><?php echo htmlspecialchars($booking_details['movie_title']); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Cinema:</span>
                            <span class="font-medium"><?php echo htmlspecialchars($booking_details['hall_name']); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Date:</span>
                            <span class="font-medium"><?php echo date('M d, Y', strtotime($booking_details['date'])); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Time:</span>
                            <span class="font-medium"><?php echo htmlspecialchars($booking_details['time']); ?></span>
                        </div>
                        
                    </div>

                    <div class="border-t pt-4 mt-4">
                        <div id="selected-seats" class="mb-4">
                            <h4 class="font-medium text-gray-800 mb-2">Selected Seats:</h4>
                            <div id="seat-list" class="text-sm text-gray-600">
                                <p class="text-center italic">No seats selected</p>
                            </div>
                        </div>

                        <div class="border-t pt-4">
                            <div class="flex justify-between items-center mb-4">
                                <span class="text-lg font-semibold text-gray-800">Total:</span>
                                <span id="total-amount" class="text-xl font-bold text-primary">Rs. 0</span>
                            </div>

                            <form method="POST" id="booking-form">
                                <input type="hidden" name="selected_seats" id="selected-seats-input">
                                <input type="hidden" name="total_amount" id="total-amount-input">
                                
                                <button type="submit" 
                                        id="continue-btn"
                                        class="w-full bg-primary hover:bg-purple-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
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

    <script>
        let selectedSeats = [];
        let totalAmount = 0;

        document.addEventListener('DOMContentLoaded', function() {
            const seatButtons = document.querySelectorAll('.seat-btn:not([disabled])');
            const seatList = document.getElementById('seat-list');
            const totalAmountEl = document.getElementById('total-amount');
            const continueBtn = document.getElementById('continue-btn');
            const selectedSeatsInput = document.getElementById('selected-seats-input');
            const totalAmountInput = document.getElementById('total-amount-input');

            seatButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const seatId = this.dataset.seat;
                    const seatPrice = parseInt(this.dataset.price);
                    const seatRow = this.dataset.row;

                    if (this.classList.contains('bg-primary')) {
                        // Deselect seat
                        this.classList.remove('bg-primary', 'text-white');
                        this.classList.add('bg-gray-200');
                        
                        selectedSeats = selectedSeats.filter(seat => seat.id !== seatId);
                        totalAmount -= seatPrice;
                    } else {
                        // Select seat
                        this.classList.remove('bg-gray-200');
                        this.classList.add('bg-primary', 'text-white');
                        
                        selectedSeats.push({
                            id: seatId,
                            price: seatPrice,
                            row: seatRow
                        });
                        totalAmount += seatPrice;
                    }

                    updateBookingSummary();
                });
            });

            function updateBookingSummary() {
                if (selectedSeats.length === 0) {
                    seatList.innerHTML = '<p class="text-center italic">No seats selected</p>';
                    totalAmountEl.textContent = 'Rs. 0';
                    continueBtn.disabled = true;
                } else {
                    // Group seats by row
                    const seatsByRow = {};
                    selectedSeats.forEach(seat => {
                        if (!seatsByRow[seat.row]) {
                            seatsByRow[seat.row] = [];
                        }
                        seatsByRow[seat.row].push(seat.id);
                    });

                    let seatListHTML = '';
                    Object.keys(seatsByRow).sort().forEach(row => {
                        seatListHTML += `<div class="flex justify-between mb-1">
                            <span>${seatsByRow[row].join(', ')}</span>
                            <span>Rs. ${selectedSeats.filter(s => s.row === row).reduce((sum, s) => sum + s.price, 0)}</span>
                        </div>`;
                    });

                    seatList.innerHTML = seatListHTML;
                    totalAmountEl.textContent = `Rs. ${totalAmount.toLocaleString()}`;
                    continueBtn.disabled = false;
                }

                // Update hidden inputs
                selectedSeatsInput.value = JSON.stringify(selectedSeats);
                totalAmountInput.value = totalAmount;
            }
        });
    </script>
</body>
</html>