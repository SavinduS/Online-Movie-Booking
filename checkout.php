<?php
session_start();

// Check if booking details and selected seats exist
if (!isset($_SESSION['booking_details']) || !isset($_SESSION['selected_seats'])) {
    header('Location: booking.php');
    exit;
}

$booking_details = $_SESSION['booking_details'];
$selected_seats = json_decode($_SESSION['selected_seats'], true);
$total_amount = $_SESSION['total_amount'];

// Calculate service fee and taxes
$service_fee = 50;
$tax_rate = 0.08; // 8% tax
$tax_amount = ($total_amount * $tax_rate);
$final_amount = $total_amount + $service_fee + $tax_amount;

if ($_POST) {
    // Create full name
    $full_name = $_POST['first_name'] . ' ' . $_POST['last_name'];

    // Store everything into confirmation session
    $_SESSION['booking_confirmation'] = [
        'booking_id' => 'BK' . date('YmdHis') . rand(100, 999),
        'booking_details' => $booking_details,
        'selected_seats' => $selected_seats,
        'total_amount' => $final_amount,
        'payment_method' => $_POST['payment_method'],
        'booking_date' => date('Y-m-d H:i:s'),

        // ✅ New user fields
        'first_name' => $_POST['first_name'],
        'last_name' => $_POST['last_name'],
        'full_name' => $full_name,
        'email' => $_POST['email'],
        'phone' => $_POST['phone']
    ];

    // Clear temporary session
    unset($_SESSION['booking_details']);
    unset($_SESSION['selected_seats']);
    unset($_SESSION['total_amount']);

    header('Location: booking_confirmation.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<?php include 'partial/header.php'; ?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - <?php echo htmlspecialchars($booking_details['movie_title']); ?></title>
    <script src="js/checkout.js"></script>
    <link rel="stylesheet" href="css/checkout.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="checkout-container">
        <div class="checkout-wrapper">
            <!-- Header -->
            <div class="checkout-header">
                <div class="header-content">
                    <button onclick="history.back()" class="back-button">
                        <i class="fas fa-arrow-left"></i>
                    </button>
                    <div class="header-text">
                        <h1 class="page-title">Checkout</h1>
                        <p class="page-subtitle">
                            <i class="fas fa-credit-card"></i>
                            Complete your booking
                        </p>
                    </div>
                </div>
            </div>

            <div class="checkout-grid">
                <!-- Payment Form -->
                <div class="payment-section">
                    <!-- Customer Information -->
                    <div class="form-card">
                        <h2 class="card-title">
                            <i class="fas fa-user"></i>
                            Customer Information
                        </h2>
                        <form method="POST" id="checkout-form">
                            <div class="form-grid">
                                <div class="form-group">
                                    <label class="form-label">First Name</label>
                                    <input type="text" name="first_name" required class="form-input">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" name="last_name" required class="form-input">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" required class="form-input">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Phone</label>
                                    <input type="tel" name="phone" required class="form-input">
                                </div>
                            </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="form-card">
                        <h2 class="card-title">
                            <i class="fas fa-credit-card"></i>
                            Payment Method
                        </h2>
                        
                        <div class="payment-methods">
                            <!-- Credit/Debit Card -->
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="card" class="payment-radio" required>
                                <div class="payment-card">
                                    <div class="payment-info">
                                        <i class="fas fa-credit-card payment-icon"></i>
                                        <div class="payment-details">
                                            <div class="payment-title">Credit/Debit Card</div>
                                            <div class="payment-subtitle">Visa, MasterCard, American Express</div>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <!-- Digital Wallet -->
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="wallet" class="payment-radio">
                                <div class="payment-card">
                                    <div class="payment-info">
                                        <i class="fas fa-mobile-alt payment-icon"></i>
                                        <div class="payment-details">
                                            <div class="payment-title">Digital Wallet</div>
                                            <div class="payment-subtitle">eZ Cash, PayPal, Google Pay</div>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <!-- Bank Transfer -->
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="bank" class="payment-radio">
                                <div class="payment-card">
                                    <div class="payment-info">
                                        <i class="fas fa-university payment-icon"></i>
                                        <div class="payment-details">
                                            <div class="payment-title">Bank Transfer</div>
                                            <div class="payment-subtitle">Direct bank transfer</div>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <!-- Card Details (shown when card is selected) -->
                        <div id="card-details" class="card-details hidden">
                            <div class="card-form">
                                <div class="form-group">
                                    <label class="form-label">Card Number</label>
                                    <input type="text" placeholder="1234 5678 9012 3456" class="form-input">
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Expiry Date</label>
                                        <input type="text" placeholder="MM/YY" class="form-input">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">CVV</label>
                                        <input type="text" placeholder="123" class="form-input">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Cardholder Name</label>
                                    <input type="text" placeholder="John Doe" class="form-input">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="order-summary">
                    <h2 class="card-title">
                        <i class="fas fa-receipt"></i>
                        Order Summary
                    </h2>
                    
                    <div class="summary-details">
                        <div class="summary-item">
                            <span class="summary-label">Movie:</span>
                            <span class="summary-value"><?php echo htmlspecialchars($booking_details['movie_title']); ?></span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Cinema:</span>
                            <span class="summary-value"><?php echo htmlspecialchars($booking_details['hall_name']); ?></span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Date & Time:</span>
                            <span class="summary-value"><?php echo date('M d', strtotime($booking_details['date'])) . ' • ' . $booking_details['time']; ?></span>
                        </div>
                        
                        <div class="seats-section">
                            <h4 class="seats-title">Selected Seats:</h4>
                            <?php 
                            $seats_by_row = [];
                            foreach ($selected_seats as $seat) {
                                $seats_by_row[$seat['row']][] = $seat;
                            }
                            
                            foreach ($seats_by_row as $row => $seats): ?>
                            <div class="seat-row">
                                <span class="seat-numbers"><?php echo implode(', ', array_column($seats, 'id')); ?></span>
                                <span class="seat-price">Rs. <?php echo number_format(array_sum(array_column($seats, 'price'))); ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="price-breakdown">
                        <div class="price-item">
                            <span>Subtotal:</span>
                            <span>Rs. <?php echo number_format($total_amount); ?></span>
                        </div>
                        <div class="price-item">
                            <span>Service Fee:</span>
                            <span>Rs. <?php echo number_format($service_fee); ?></span>
                        </div>
                        <div class="price-item">
                            <span>Tax (8%):</span>
                            <span>Rs. <?php echo number_format($tax_amount); ?></span>
                        </div>
                        <div class="total-amount">
                            <span class="total-label">Total:</span>
                            <span class="total-price">Rs. <?php echo number_format($final_amount); ?></span>
                        </div>
                    </div>

                    <button type="submit" form="checkout-form" class="complete-payment-btn">
                        <i class="fas fa-lock"></i>
                        Complete Payment
                        <i class="fas fa-arrow-right"></i>
                    </button>

                    <p class="security-notice">
                        <i class="fas fa-shield-alt"></i>
                        Your payment information is secure and encrypted
                    </p>
                        </form>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'partial/footer.php'; ?>
</body>
</html>