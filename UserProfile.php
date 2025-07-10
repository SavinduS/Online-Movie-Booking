<?php
// All PHP logic must run before any HTML is sent to the browser.
// Start the session at the absolute beginning.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Requirement: If the session is not active or valid, redirect to the login page.
if (!isset($_SESSION['user_id'])) {
    header('Location: Login_index.php');
    exit();
}

// Include the database connection.
include 'database/db.php';

// Assign session variables for use.
$user_id = $_SESSION['user_id'];
$errors = [];
$successMessage = '';

// --- Handle all form submissions (POST requests) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update_profile':
                $firstName = trim($_POST['firstName'] ?? '');
                $lastName = trim($_POST['lastName'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $phone = trim($_POST['phone'] ?? '');

                // Validation
                if (empty($firstName)) $errors[] = "First name is required";
                if (empty($lastName)) $errors[] = "Last name is required";
                if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email address is required";
                if (empty($phone) || !preg_match('/^[0-9+\-\s()]+$/', $phone)) $errors[] = "Valid phone number is required";
                
                // Check if email already exists for other users
                $stmt = $connection->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
                $stmt->bind_param("si", $email, $user_id);
                $stmt->execute();
                if ($stmt->get_result()->num_rows > 0) {
                    $errors[] = "Email address is already in use";
                }
                $stmt->close();

                if (empty($errors)) {
                    $stmt = $connection->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ? WHERE id = ?");
                    $stmt->bind_param("ssssi", $firstName, $lastName, $email, $phone, $user_id);
                    if ($stmt->execute()) {
                        $successMessage = "Profile updated successfully!";
                    } else {
                        $errors[] = "Database error: " . $stmt->error;
                    }
                    $stmt->close();
                }
                break;

            case 'change_password':
                $currentPassword = $_POST['currentPassword'] ?? '';
                $newPassword = $_POST['newPassword'] ?? '';
                $confirmPassword = $_POST['confirmPassword'] ?? '';

                $stmt = $connection->prepare("SELECT password FROM users WHERE id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $user_pass = $stmt->get_result()->fetch_assoc();
                $stmt->close();

                if (!$user_pass || !password_verify($currentPassword, $user_pass['password'])) $errors[] = "Current password is incorrect";
                if (strlen($newPassword) < 8) $errors[] = "New password must be at least 8 characters long";
                if ($newPassword !== $confirmPassword) $errors[] = "New passwords do not match";

                if (empty($errors)) {
                    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                    $stmt = $connection->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $stmt->bind_param("si", $hashedPassword, $user_id);
                    if ($stmt->execute()) {
                        $successMessage = "Password changed successfully!";
                    } else {
                        $errors[] = "Database error: " . $stmt->error;
                    }
                    $stmt->close();
                }
                break;

            case 'delete_account':
                $confirmDelete = $_POST['confirmDelete'] ?? '';
                if ($confirmDelete === 'DELETE') {
                    $stmt = $connection->prepare("DELETE FROM users WHERE id = ?");
                    $stmt->bind_param("i", $user_id);
                    if ($stmt->execute()) {
                        session_destroy();
                        header('Location: Login_index.php?message=Account deleted successfully');
                        exit();
                    } else {
                        $errors[] = "Database error: " . $stmt->error;
                    }
                    $stmt->close();
                } else {
                    $errors[] = "Please type 'DELETE' to confirm account deletion";
                }
                break;
        }
    }
}

// --- Fetch data to display on the page ---

// Fetch user data again to get the most up-to-date info after a potential POST request.
$stmt = $connection->prepare("SELECT first_name, last_name, email, phone FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// If the user was deleted from the DB but the session still exists, log them out.
if (!$user) {
    session_destroy();
    header('Location: Login_index.php');
    exit();
}

// Fetch bookings only for the 'user' role using their email.
$bookings = [];
if (isset($_SESSION['role']) && $_SESSION['role'] === 'user') {
    $userEmail = $user['email']; 
    $stmt = $connection->prepare("SELECT booking_id, full_name, email, phone, movie_title, hall_name, show_date, show_time, selected_seats, number_of_tickets, total_amount, payment_method, booking_date, status FROM movie_booking.bookings WHERE email = ? ORDER BY booking_date DESC");
    $stmt->bind_param("s", $userEmail); 
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
    $stmt->close();
}

// Now that all PHP processing is done, we start the HTML part of the page by including the header.
include("partial/header.php");
?>

<!--
    FIX: The <!DOCTYPE>, <html>, <head>, and opening <body> tags are REMOVED from this file.
    They are all provided by `header.php`. We just need to add the specific CSS for this page.
-->
<link rel="stylesheet" href="css/UserProfile.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<div class="sup-container">
    <div class="sup-profile-wrapper">
        <!-- Header Section -->
        <div class="sup-header">
            <div class="sup-logo">
                <i class="fas fa-film sup-logo-icon"></i>
            </div>
            <h1 class="sup-title">My Profile</h1>
            <p class="sup-subtitle">Manage your Swans Cinema account</p>
        </div>

        <!-- Navigation Tabs -->
        <div class="sup-tabs">
            <button class="sup-tab-btn sup-active" data-tab="profile">
                <i class="fas fa-user"></i> Profile Info
            </button>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'user'): ?>
            <button class="sup-tab-btn" data-tab="bookings">
                <i class="fas fa-ticket-alt"></i> My Bookings
            </button>
            <?php endif; ?>
            <button class="sup-tab-btn" data-tab="password">
                <i class="fas fa-lock"></i> Change Password
            </button>
            <button class="sup-tab-btn" data-tab="settings">
                <i class="fas fa-cog"></i> Settings
            </button>
        </div>

        <!-- Messages -->
        <?php if (!empty($errors)): ?>
            <div class="sup-error-container">
                <i class="fas fa-exclamation-triangle sup-error-icon"></i>
                <ul class="sup-error-list">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <?php if (!empty($successMessage)): ?>
            <div class="sup-success-container">
                <i class="fas fa-check-circle sup-success-icon"></i>
                <p class="sup-success-message"><?php echo htmlspecialchars($successMessage); ?></p>
            </div>
        <?php endif; ?>

        <!-- Profile Tab -->
        <div class="sup-tab-content sup-active" id="sup-profile-tab">
            <div class="sup-profile-card">
                <div class="sup-avatar-section">
                    <div class="sup-avatar"><i class="fas fa-user-circle"></i></div>
                    <button class="sup-avatar-btn"><i class="fas fa-camera"></i></button>
                </div>
                <form class="sup-form" method="POST" action="UserProfile.php">
                    <input type="hidden" name="action" value="update_profile">
                    <div class="sup-form-row">
                        <div class="sup-form-group">
                            <label class="sup-label">First Name</label>
                            <div class="sup-input-wrapper">
                                <i class="fas fa-user sup-input-icon"></i>
                                <input type="text" class="sup-form-input" name="firstName" value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>" required>
                            </div>
                        </div>
                        <div class="sup-form-group">
                            <label class="sup-label">Last Name</label>
                            <div class="sup-input-wrapper">
                                <i class="fas fa-user sup-input-icon"></i>
                                <input type="text" class="sup-form-input" name="lastName" value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="sup-form-group">
                        <label class="sup-label">Email Address</label>
                        <div class="sup-input-wrapper">
                            <i class="fas fa-envelope sup-input-icon"></i>
                            <input type="email" class="sup-form-input" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                        </div>
                    </div>
                    <div class="sup-form-group">
                        <label class="sup-label">Phone Number</label>
                        <div class="sup-input-wrapper">
                            <i class="fas fa-phone sup-input-icon"></i>
                            <input type="tel" class="sup-form-input" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" required>
                        </div>
                    </div>
                    <button type="submit" class="sup-submit-btn"><i class="fas fa-save sup-btn-icon"></i> UPDATE PROFILE</button>
                </form>
            </div>
        </div>
        
        <!-- Bookings Tab -->
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'user'): ?>
        <div class="sup-tab-content" id="sup-bookings-tab">
            <div class="sup-bookings-card" style="background-color: #E6E6FA; border-radius: 10px; padding: 20px; box-shadow: 0 0 10px rgba(147,112,219,0.2);">
                <div class="sup-bookings-header" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #9370DB; padding-bottom: 10px; margin-bottom: 20px;">
                    <h3 class="sup-bookings-title" style="font-size: 24px; color: #4B0082; display: flex; align-items: center; gap: 8px;"><i class="fas fa-ticket-alt"></i> My Booking History</h3>
                    <div class="sup-bookings-count" style="text-align: right;">
                        <span class="sup-count-badge" style="background-color: #9370DB; color: white; padding: 5px 10px; border-radius: 20px;"><?php echo count($bookings); ?></span>
                        <span class="sup-count-text" style="color: #4B0082; margin-left: 8px;">booking<?php echo count($bookings) !== 1 ? 's' : ''; ?> found</span>
                    </div>
                </div>
                <?php if (!empty($bookings)): ?>
                <div class="sup-booking-list" style="display: flex; flex-direction: column; gap: 20px;">
                    <?php foreach ($bookings as $booking): ?>
                    <div class="sup-booking-item" style="background-color: #fff; border: 1px solid #ccc; border-left: 5px solid #9370DB; padding: 15px; border-radius: 8px; display: flex; flex-wrap: wrap; justify-content: space-between; gap: 15px;">
                        <div class="sup-booking-left" style="display: flex; gap: 15px; flex-grow: 1;">
                            <div class="sup-movie-poster-small" style="font-size: 32px; color: #9370DB;"><i class="fas fa-film"></i></div>
                            <div class="sup-booking-info">
                                <h4 class="sup-movie-name" style="color: #4B0082; font-weight: 600;"><?php echo htmlspecialchars($booking['movie_title']); ?></h4>
                                <div class="sup-booking-details" style="color: #333; margin-top: 6px; font-size: 0.9em;">
                                    <p><i class="fas fa-door-open"></i> <?php echo htmlspecialchars($booking['hall_name']); ?></p>
                                    <p><i class="fas fa-calendar"></i> <?php echo date('M d, Y', strtotime($booking['show_date'])); ?> at <?php echo date('g:i A', strtotime($booking['show_time'])); ?></p>
                                    <p><i class="fas fa-chair"></i> <?php echo htmlspecialchars($booking['selected_seats']); ?> (<?php echo $booking['number_of_tickets']; ?> ticket<?php echo $booking['number_of_tickets'] > 1 ? 's' : ''; ?>)</p>
                                </div>
                                <div class="sup-booking-meta" style="font-size: 14px; color: #666; margin-top: 6px;">
                                    <span style="margin-right: 12px;"><i class="fas fa-barcode"></i> <?php echo htmlspecialchars($booking['booking_id']); ?></span>
                                    <span><i class="fas fa-money-bill"></i> Rs. <?php echo number_format($booking['total_amount'], 2); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="sup-booking-right" style="text-align: right; flex-shrink: 0;">
                            <div class="sup-booking-status-info" style="font-size: 14px;">
                                <span style="display: inline-block; padding: 6px 10px; border-radius: 15px; background-color: <?php echo strtolower($booking['status']) === 'confirmed' ? '#8FBC8F' : '#FF6B6B'; ?>; color: white; font-weight: 500;"><i class="fas <?php echo strtolower($booking['status']) === 'confirmed' ? 'fa-check-circle' : 'fa-times-circle'; ?>"></i> <?php echo ucfirst($booking['status']); ?></span><br>
                                <span style="color: #9370DB; font-weight: 500; margin-top: 4px; display: inline-block;"><i class="fas <?php echo strtolower($booking['payment_method']) === 'card' ? 'fa-credit-card' : 'fa-money-bill-alt'; ?>"></i> <?php echo ucfirst($booking['payment_method']); ?></span><br>
                                <span style="color: #666;">Booked: <?php echo date('M d, Y', strtotime($booking['booking_date'])); ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div style="text-align: center; padding: 40px;">
                    <p style="font-size: 16px; color: #666;">You haven't made any bookings yet.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Password Tab -->
        <div class="sup-tab-content" id="sup-password-tab">
            <div class="sup-password-card">
                <form class="sup-form" method="POST" action="UserProfile.php">
                    <input type="hidden" name="action" value="change_password">
                    <div class="sup-form-group">
                        <label class="sup-label">Current Password</label>
                        <div class="sup-input-wrapper">
                            <i class="fas fa-lock sup-input-icon"></i>
                            <input type="password" class="sup-form-input" name="currentPassword" required>
                        </div>
                    </div>
                    <div class="sup-form-group">
                        <label class="sup-label">New Password</label>
                        <div class="sup-input-wrapper">
                            <i class="fas fa-lock sup-input-icon"></i>
                            <input type="password" class="sup-form-input" name="newPassword" id="sup-new-password" required>
                            <button type="button" class="sup-password-toggle" id="sup-toggle-new-password"><i class="fas fa-eye"></i></button>
                        </div>
                        <div class="sup-password-strength">
                            <div class="sup-strength-bar"><div class="sup-strength-fill" id="sup-strength-fill"></div></div>
                            <span class="sup-strength-text" id="sup-strength-text">Password strength</span>
                        </div>
                    </div>
                    <div class="sup-form-group">
                        <label class="sup-label">Confirm New Password</label>
                        <div class="sup-input-wrapper">
                            <i class="fas fa-lock sup-input-icon"></i>
                            <input type="password" class="sup-form-input" name="confirmPassword" id="sup-confirm-new-password" required>
                            <button type="button" class="sup-password-toggle" id="sup-toggle-confirm-new-password"><i class="fas fa-eye"></i></button>
                        </div>
                    </div>
                    <button type="submit" class="sup-submit-btn"><i class="fas fa-key sup-btn-icon"></i> CHANGE PASSWORD</button>
                </form>
            </div>
        </div>

        <!-- Settings Tab -->
        <div class="sup-tab-content" id="sup-settings-tab">
            <div class="sup-settings-card">
                <div class="sup-danger-zone">
                    <h3 class="sup-danger-title"><i class="fas fa-exclamation-triangle"></i> Danger Zone</h3>
                    <p class="sup-danger-text">Once you delete your account, there is no going back. Please be certain.</p>
                    <form class="sup-form" method="POST" action="UserProfile.php" id="sup-delete-form">
                        <input type="hidden" name="action" value="delete_account">
                        <div class="sup-form-group">
                            <label class="sup-label">Type "DELETE" to confirm</label>
                            <div class="sup-input-wrapper">
                                <i class="fas fa-trash sup-input-icon"></i>
                                <input type="text" class="sup-form-input" name="confirmDelete" placeholder="Type DELETE here" required>
                            </div>
                        </div>
                        <button type="submit" class="sup-danger-btn" id="sup-delete-btn"><i class="fas fa-trash sup-btn-icon"></i> DELETE ACCOUNT</button>
                    </form>
                </div>
                <div class="sup-logout-section">
                    <a href="logout.php" class="sup-logout-btn"><i class="fas fa-sign-out-alt sup-btn-icon"></i> LOGOUT</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Tab functionality
    document.querySelectorAll('.sup-tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const tabName = this.dataset.tab;
            document.querySelectorAll('.sup-tab-btn').forEach(b => b.classList.remove('sup-active'));
            document.querySelectorAll('.sup-tab-content').forEach(c => c.classList.remove('sup-active'));
            this.classList.add('sup-active');
            const tabContent = document.getElementById(`sup-${tabName}-tab`);
            if (tabContent) {
                tabContent.classList.add('sup-active');
            }
        });
    });

    // Password visibility toggle
    function togglePasswordVisibility(inputId, toggleId) {
        const passwordInput = document.getElementById(inputId);
        const toggleBtn = document.getElementById(toggleId);
        if (!passwordInput || !toggleBtn) return;
        const icon = toggleBtn.querySelector('i');
        toggleBtn.addEventListener('click', function() {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    }
    togglePasswordVisibility('sup-new-password', 'sup-toggle-new-password');
    togglePasswordVisibility('sup-confirm-new-password', 'sup-toggle-confirm-new-password');

    // Password strength checker
    const newPasswordInput = document.getElementById('sup-new-password');
    if (newPasswordInput) {
        newPasswordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            
            const strengthFill = document.getElementById('sup-strength-fill');
            const strengthText = document.getElementById('sup-strength-text');
            let text = 'Very Weak';
            switch (strength) { case 2: text = 'Weak'; break; case 3: text = 'Fair'; break; case 4: text = 'Good'; break; case 5: text = 'Strong'; break; }
            strengthFill.style.width = (strength * 20) + '%';
            strengthFill.className = 'sup-strength-fill sup-strength-' + strength;
            strengthText.textContent = text;
        });
    }

    // Real-time password confirmation
    const confirmNewPasswordInput = document.getElementById('sup-confirm-new-password');
    if (confirmNewPasswordInput) {
        confirmNewPasswordInput.addEventListener('input', function() {
            this.setCustomValidity(this.value !== newPasswordInput.value ? 'Passwords do not match' : '');
        });
    }

    // Delete account confirmation
    const deleteForm = document.getElementById('sup-delete-form');
    if (deleteForm) {
        deleteForm.addEventListener('submit', function(e) {
            if (!confirm('Are you absolutely sure you want to delete your account? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    }

    // Auto-hide messages after 5 seconds
    setTimeout(() => {
        const messages = document.querySelectorAll('.sup-error-container, .sup-success-container');
        messages.forEach(msg => {
            msg.style.transition = 'opacity 0.5s ease';
            msg.style.opacity = '0';
            setTimeout(() => msg.remove(), 500);
        });
    }, 5000);
</script>

<?php
// Finally, we include the footer which closes the <body> and <html> tags.
include("partial/footer.php");
?>