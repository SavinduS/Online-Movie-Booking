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
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - <?php echo htmlspecialchars($booking_details['movie_title']); ?></title>
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
    <div class="min-h-screen py-8">
        <div class="max-w-4xl mx-auto px-4">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <div class="flex items-center gap-4">
                    <button onclick="history.back()" class="text-primary hover:text-purple-700 transition-colors">
                        <i class="fas fa-arrow-left text-xl"></i>
                    </button>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Checkout</h1>
                        <p class="text-gray-600 flex items-center gap-2">
                            <i class="fas fa-credit-card text-primary"></i>
                            Complete your booking
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Payment Form -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Customer Information -->
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                            <i class="fas fa-user text-primary"></i>
                            Customer Information
                        </h2>
                        <form method="POST" id="checkout-form">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                                    <input type="text" name="first_name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                                    <input type="text" name="last_name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                    <input type="email" name="email" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                    <input type="tel" name="phone" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                                </div>
                            </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                            <i class="fas fa-credit-card text-primary"></i>
                            Payment Method
                        </h2>
                        
                        <div class="space-y-4">
                            <!-- Credit/Debit Card -->
                            <label class="cursor-pointer">
                                <input type="radio" name="payment_method" value="card" class="hidden peer" required>
                                <div class="p-4 border-2 border-gray-200 rounded-lg hover:border-primary peer-checked:border-primary peer-checked:bg-primary peer-checked:text-white transition-all">
                                    <div class="flex items-center gap-3">
                                        <i class="fas fa-credit-card text-lg"></i>
                                        <div>
                                            <div class="font-semibold">Credit/Debit Card</div>
                                            <div class="text-sm opacity-75">Visa, MasterCard, American Express</div>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <!-- Digital Wallet -->
                            <label class="cursor-pointer">
                                <input type="radio" name="payment_method" value="wallet" class="hidden peer">
                                <div class="p-4 border-2 border-gray-200 rounded-lg hover:border-primary peer-checked:border-primary peer-checked:bg-primary peer-checked:text-white transition-all">
                                    <div class="flex items-center gap-3">
                                        <i class="fas fa-mobile-alt text-lg"></i>
                                        <div>
                                            <div class="font-semibold">Digital Wallet</div>
                                            <div class="text-sm opacity-75">eZ Cash, PayPal, Google Pay</div>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <!-- Bank Transfer -->
                            <label class="cursor-pointer">
                                <input type="radio" name="payment_method" value="bank" class="hidden peer">
                                <div class="p-4 border-2 border-gray-200 rounded-lg hover:border-primary peer-checked:border-primary peer-checked:bg-primary peer-checked:text-white transition-all">
                                    <div class="flex items-center gap-3">
                                        <i class="fas fa-university text-lg"></i>
                                        <div>
                                            <div class="font-semibold">Bank Transfer</div>
                                            <div class="text-sm opacity-75">Direct bank transfer</div>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <!-- Card Details (shown when card is selected) -->
                        <div id="card-details" class="mt-6 p-4 bg-gray-50 rounded-lg hidden">
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Card Number</label>
                                    <input type="text" placeholder="1234 5678 9012 3456" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date</label>
                                        <input type="text" placeholder="MM/YY" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">CVV</label>
                                        <input type="text" placeholder="123" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Cardholder Name</label>
                                    <input type="text" placeholder="John Doe" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="bg-white rounded-lg shadow-lg p-6 h-fit">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-receipt text-primary"></i>
                        Order Summary
                    </h2>
                    
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
                            <span class="text-gray-600">Date & Time:</span>
                            <span class="font-medium"><?php echo date('M d', strtotime($booking_details['date'])) . ' • ' . $booking_details['time']; ?></span>
                        </div>
                        
                        <div class="border-t pt-3 mt-3">
                            <h4 class="font-medium text-gray-800 mb-2">Selected Seats:</h4>
                            <?php 
                            $seats_by_row = [];
                            foreach ($selected_seats as $seat) {
                                $seats_by_row[$seat['row']][] = $seat;
                            }
                            
                            foreach ($seats_by_row as $row => $seats): ?>
                            <div class="flex justify-between text-sm mb-1">
                                <span><?php echo implode(', ', array_column($seats, 'id')); ?></span>
                                <span>Rs. <?php echo number_format(array_sum(array_column($seats, 'price'))); ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="border-t pt-4 mt-4 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span>Subtotal:</span>
                            <span>Rs. <?php echo number_format($total_amount); ?></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span>Service Fee:</span>
                            <span>Rs. <?php echo number_format($service_fee); ?></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span>Tax (8%):</span>
                            <span>Rs. <?php echo number_format($tax_amount); ?></span>
                        </div>
                        <div class="border-t pt-2">
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-semibold text-gray-800">Total:</span>
                                <span class="text-xl font-bold text-primary">Rs. <?php echo number_format($final_amount); ?></span>
                            </div>
                        </div>
                    </div>

                    <button type="submit" form="checkout-form" class="w-full bg-primary hover:bg-purple-700 text-white font-semibold py-4 px-6 rounded-lg transition-colors mt-6 flex items-center justify-center gap-2">
                        <i class="fas fa-lock"></i>
                        Complete Payment
                        <i class="fas fa-arrow-right"></i>
                    </button>

                    <p class="text-xs text-gray-500 text-center mt-3">
                        <i class="fas fa-shield-alt text-primary"></i>
                        Your payment information is secure and encrypted
                    </p>
                        </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
            const cardDetails = document.getElementById('card-details');

            paymentMethods.forEach(method => {
                method.addEventListener('change', function() {
                    if (this.value === 'card') {
                        cardDetails.classList.remove('hidden');
                    } else {
                        cardDetails.classList.add('hidden');
                    }
                });
            });
        });
    </script>
</body>
</html>