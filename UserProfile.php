<?php
session_start();
include 'database/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: signin.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$errors = [];
$successMessage = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update_profile':
                $firstName = trim($_POST['firstName'] ?? '');
                $lastName = trim($_POST['lastName'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $phone = trim($_POST['phone'] ?? '');
                
                // Validation
                if (empty($firstName)) {
                    $errors[] = "First name is required";
                }
                if (empty($lastName)) {
                    $errors[] = "Last name is required";
                }
                if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Valid email address is required";
                }
                if (empty($phone) || !preg_match('/^[0-9+\-\s()]+$/', $phone)) {
                    $errors[] = "Valid phone number is required";
                }
                
                // Check if email already exists for other users
                $stmt = $connection->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
                $stmt->bind_param("si", $email, $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
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
                
                // Get current password hash
                $stmt = $connection->prepare("SELECT password FROM users WHERE id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();
                $stmt->close();
                
                if (!password_verify($currentPassword, $user['password'])) {
                    $errors[] = "Current password is incorrect";
                }
                if (strlen($newPassword) < 8) {
                    $errors[] = "New password must be at least 8 characters long";
                }
                if ($newPassword !== $confirmPassword) {
                    $errors[] = "New passwords do not match";
                }
                
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
                        header('Location: signin.php?message=Account deleted successfully');
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

// Fetch user data
$stmt = $connection->prepare("SELECT first_name, last_name, email, phone FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Swans Cinema</title>
    <link rel="stylesheet" href="css/UserProfile.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
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
                    <i class="fas fa-user"></i>
                    Profile Info
                </button>
                <button class="sup-tab-btn" data-tab="password">
                    <i class="fas fa-lock"></i>
                    Change Password
                </button>
                <button class="sup-tab-btn" data-tab="settings">
                    <i class="fas fa-cog"></i>
                    Settings
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
                        <div class="sup-avatar">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <button class="sup-avatar-btn">
                            <i class="fas fa-camera"></i>
                        </button>
                    </div>

                    <form class="sup-form" method="POST" action="">
                        <input type="hidden" name="action" value="update_profile">
                        
                        <div class="sup-form-row">
                            <div class="sup-form-group">
                                <label class="sup-label">First Name</label>
                                <div class="sup-input-wrapper">
                                    <i class="fas fa-user sup-input-icon"></i>
                                    <input 
                                        type="text" 
                                        class="sup-form-input" 
                                        name="firstName" 
                                        value="<?php echo htmlspecialchars($user['first_name']); ?>"
                                        required
                                    >
                                </div>
                            </div>
                            
                            <div class="sup-form-group">
                                <label class="sup-label">Last Name</label>
                                <div class="sup-input-wrapper">
                                    <i class="fas fa-user sup-input-icon"></i>
                                    <input 
                                        type="text" 
                                        class="sup-form-input" 
                                        name="lastName" 
                                        value="<?php echo htmlspecialchars($user['last_name']); ?>"
                                        required
                                    >
                                </div>
                            </div>
                        </div>

                        <div class="sup-form-group">
                            <label class="sup-label">Email Address</label>
                            <div class="sup-input-wrapper">
                                <i class="fas fa-envelope sup-input-icon"></i>
                                <input 
                                    type="email" 
                                    class="sup-form-input" 
                                    name="email" 
                                    value="<?php echo htmlspecialchars($user['email']); ?>"
                                    required
                                >
                            </div>
                        </div>

                        <div class="sup-form-group">
                            <label class="sup-label">Phone Number</label>
                            <div class="sup-input-wrapper">
                                <i class="fas fa-phone sup-input-icon"></i>
                                <input 
                                    type="tel" 
                                    class="sup-form-input" 
                                    name="phone" 
                                    value="<?php echo htmlspecialchars($user['phone']); ?>"
                                    required
                                >
                            </div>
                        </div>

                        <button type="submit" class="sup-submit-btn">
                            <i class="fas fa-save sup-btn-icon"></i>
                            UPDATE PROFILE
                        </button>
                    </form>
                </div>
            </div>

            <!-- Password Tab -->
            <div class="sup-tab-content" id="sup-password-tab">
                <div class="sup-password-card">
                    <form class="sup-form" method="POST" action="">
                        <input type="hidden" name="action" value="change_password">
                        
                        <div class="sup-form-group">
                            <label class="sup-label">Current Password</label>
                            <div class="sup-input-wrapper">
                                <i class="fas fa-lock sup-input-icon"></i>
                                <input 
                                    type="password" 
                                    class="sup-form-input" 
                                    name="currentPassword" 
                                    required
                                >
                            </div>
                        </div>

                        <div class="sup-form-group">
                            <label class="sup-label">New Password</label>
                            <div class="sup-input-wrapper">
                                <i class="fas fa-lock sup-input-icon"></i>
                                <input 
                                    type="password" 
                                    class="sup-form-input" 
                                    name="newPassword" 
                                    id="sup-new-password"
                                    required
                                >
                                <button type="button" class="sup-password-toggle" id="sup-toggle-new-password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="sup-password-strength">
                                <div class="sup-strength-bar">
                                    <div class="sup-strength-fill" id="sup-strength-fill"></div>
                                </div>
                                <span class="sup-strength-text" id="sup-strength-text">Password strength</span>
                            </div>
                        </div>

                        <div class="sup-form-group">
                            <label class="sup-label">Confirm New Password</label>
                            <div class="sup-input-wrapper">
                                <i class="fas fa-lock sup-input-icon"></i>
                                <input 
                                    type="password" 
                                    class="sup-form-input" 
                                    name="confirmPassword" 
                                    id="sup-confirm-new-password"
                                    required
                                >
                                <button type="button" class="sup-password-toggle" id="sup-toggle-confirm-new-password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="sup-submit-btn">
                            <i class="fas fa-key sup-btn-icon"></i>
                            CHANGE PASSWORD
                        </button>
                    </form>
                </div>
            </div>

            <!-- Settings Tab -->
            <div class="sup-tab-content" id="sup-settings-tab">
                <div class="sup-settings-card">
                    <div class="sup-danger-zone">
                        <h3 class="sup-danger-title">
                            <i class="fas fa-exclamation-triangle"></i>
                            Danger Zone
                        </h3>
                        <p class="sup-danger-text">
                            Once you delete your account, there is no going back. Please be certain.
                        </p>
                        
                        <form class="sup-form" method="POST" action="" id="sup-delete-form">
                            <input type="hidden" name="action" value="delete_account">
                            
                            <div class="sup-form-group">
                                <label class="sup-label">Type "DELETE" to confirm</label>
                                <div class="sup-input-wrapper">
                                    <i class="fas fa-trash sup-input-icon"></i>
                                    <input 
                                        type="text" 
                                        class="sup-form-input" 
                                        name="confirmDelete" 
                                        placeholder="Type DELETE here"
                                        required
                                    >
                                </div>
                            </div>

                            <button type="submit" class="sup-danger-btn" id="sup-delete-btn">
                                <i class="fas fa-trash sup-btn-icon"></i>
                                DELETE ACCOUNT
                            </button>
                        </form>
                    </div>

                    <div class="sup-logout-section">
                        <a href="logout.php" class="sup-logout-btn">
                            <i class="fas fa-sign-out-alt sup-btn-icon"></i>
                            LOGOUT
                        </a>
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
                
                // Remove active class from all tabs and contents
                document.querySelectorAll('.sup-tab-btn').forEach(b => b.classList.remove('sup-active'));
                document.querySelectorAll('.sup-tab-content').forEach(c => c.classList.remove('sup-active'));
                
                // Add active class to clicked tab and corresponding content
                this.classList.add('sup-active');
                document.getElementById(`sup-${tabName}-tab`).classList.add('sup-active');
            });
        });

        // Password visibility toggle
        function togglePasswordVisibility(inputId, toggleId) {
            const passwordInput = document.getElementById(inputId);
            const toggleBtn = document.getElementById(toggleId);
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

        // Initialize password toggles
        if (document.getElementById('sup-toggle-new-password')) {
            togglePasswordVisibility('sup-new-password', 'sup-toggle-new-password');
            togglePasswordVisibility('sup-confirm-new-password', 'sup-toggle-confirm-new-password');
        }

        // Password strength checker
        function checkPasswordStrength(password) {
            let strength = 0;
            let text = '';
            
            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            
            switch (strength) {
                case 0:
                case 1:
                    text = 'Very Weak';
                    break;
                case 2:
                    text = 'Weak';
                    break;
                case 3:
                    text = 'Fair';
                    break;
                case 4:
                    text = 'Good';
                    break;
                case 5:
                    text = 'Strong';
                    break;
            }
            
            return { strength, text };
        }

        // Password strength indicator
        const newPasswordInput = document.getElementById('sup-new-password');
        if (newPasswordInput) {
            newPasswordInput.addEventListener('input', function() {
                const password = this.value;
                const result = checkPasswordStrength(password);
                const strengthFill = document.getElementById('sup-strength-fill');
                const strengthText = document.getElementById('sup-strength-text');
                
                strengthFill.style.width = (result.strength * 20) + '%';
                strengthText.textContent = result.text;
                
                // Set color based on strength
                strengthFill.className = 'sup-strength-fill sup-strength-' + result.strength;
            });
        }

        // Real-time password confirmation
        const confirmNewPasswordInput = document.getElementById('sup-confirm-new-password');
        if (confirmNewPasswordInput) {
            confirmNewPasswordInput.addEventListener('input', function() {
                const password = document.getElementById('sup-new-password').value;
                const confirmPassword = this.value;
                
                if (confirmPassword && password !== confirmPassword) {
                    this.setCustomValidity('Passwords do not match');
                } else {
                    this.setCustomValidity('');
                }
            });
        }

        // Delete account confirmation
        const deleteForm = document.getElementById('sup-delete-form');
        if (deleteForm) {
            deleteForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                if (confirm('Are you absolutely sure you want to delete your account? This action cannot be undone.')) {
                    this.submit();
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
</body>
</html>