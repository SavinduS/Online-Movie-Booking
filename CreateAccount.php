<?php

session_start();
include  'database/db.php';
 
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['firstName'] ?? '');
    $lastName = trim($_POST['lastName'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    
    $errors = [];
    
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
    
    if (empty($password) || strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long";
    }
    
    if ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match";    
    }
    

    //Data insert Part
    
    // If no errors, process registration (you would typically save to database here)
    if (empty($errors)) {
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Use imported $conn from db.php
    $stmt = $connection->prepare("INSERT INTO users (first_name, last_name, email, phone, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $firstName, $lastName, $email, $phone, $hashedPassword);
    
    if ($stmt->execute()) {
        $successMessage = "Account created successfully! You can now sign in.";
        $firstName = $lastName = $email = $phone = '';
    } else {
        $errors[] = "Database error: " . $stmt->error;
    }

    $stmt->close();
    $connection->close(); 
}

    




}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - Swans Cinema</title>
    <link rel="stylesheet" href="css/CreateAccount.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="sup-container">
        <div class="sup-form-wrapper">
            <div class="sup-header">
                <div class="sup-logo">
                    <i class="fas fa-film sup-logo-icon"></i>
                </div>
                <h1 class="sup-title">Create Account</h1>
                <p class="sup-subtitle">Join Swans Cinema for exclusive movie experiences</p>
            </div>

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

            <?php if (isset($successMessage)): ?>
                <div class="sup-success-container">
                    <i class="fas fa-check-circle sup-success-icon"></i>
                    <p class="sup-success-message"><?php echo htmlspecialchars($successMessage); ?></p>
                </div>
            <?php endif; ?>

            <form class="sup-form" method="POST" action="" id="sup-create-account-form">
                <div class="sup-form-row">
                    <div class="sup-form-group">
                        <div class="sup-input-wrapper">
                            <i class="fas fa-user sup-input-icon"></i>
                            <input 
                                type="text" 
                                class="sup-form-input" 
                                id="sup-first-name" 
                                name="firstName" 
                                placeholder="First Name"
                                value="<?php echo htmlspecialchars($firstName ?? ''); ?>"
                                required
                            >
                        </div>
                    </div>
                    
                    <div class="sup-form-group">
                        <div class="sup-input-wrapper">
                            <i class="fas fa-user sup-input-icon"></i>
                            <input 
                                type="text" 
                                class="sup-form-input" 
                                id="sup-last-name" 
                                name="lastName" 
                                placeholder="Last Name"
                                value="<?php echo htmlspecialchars($lastName ?? ''); ?>"
                                required
                            >
                        </div>
                    </div>
                </div>

                <div class="sup-form-group">
                    <div class="sup-input-wrapper">
                        <i class="fas fa-envelope sup-input-icon"></i>
                        <input 
                            type="email" 
                            class="sup-form-input" 
                            id="sup-email" 
                            name="email" 
                            placeholder="Enter your email address"
                            value="<?php echo htmlspecialchars($email ?? ''); ?>"
                            required
                        >
                    </div>
                </div>

                <div class="sup-form-group">
                    <div class="sup-input-wrapper">
                        <i class="fas fa-phone sup-input-icon"></i>
                        <input 
                            type="tel" 
                            class="sup-form-input" 
                            id="sup-phone" 
                            name="phone" 
                            placeholder="Enter your phone number"
                            value="<?php echo htmlspecialchars($phone ?? ''); ?>"
                            required
                        >
                    </div>
                </div>

                <div class="sup-form-group">
                    <div class="sup-input-wrapper">
                        <i class="fas fa-lock sup-input-icon"></i>
                        <input 
                            type="password" 
                            class="sup-form-input" 
                            id="sup-password" 
                            name="password" 
                            placeholder="Create a password"
                            required
                        >
                        <button type="button" class="sup-password-toggle" id="sup-toggle-password">
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
                    <div class="sup-input-wrapper">
                        <i class="fas fa-lock sup-input-icon"></i>
                        <input 
                            type="password" 
                            class="sup-form-input" 
                            id="sup-confirm-password" 
                            name="confirmPassword" 
                            placeholder="Confirm your password"
                            required
                        >
                        <button type="button" class="sup-password-toggle" id="sup-toggle-confirm-password">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="sup-form-group">
                    <label class="sup-checkbox-container">
                        <input type="checkbox" class="sup-checkbox" id="sup-terms" name="terms" required>
                        <span class="sup-checkmark"></span>
                        <span class="sup-checkbox-text">
                            I agree to the <a href="#" class="sup-link">Terms & Conditions</a> and <a href="#" class="sup-link">Privacy Policy</a>
                        </span>
                    </label>
                </div>

                <div class="sup-form-group">
                    <label class="sup-checkbox-container">
                        <input type="checkbox" class="sup-checkbox" id="sup-newsletter" name="newsletter">
                        <span class="sup-checkmark"></span>
                        <span class="sup-checkbox-text">
                            Subscribe to our newsletter for movie updates and special offers
                        </span>
                    </label>
                </div>

                <button type="submit" class="sup-submit-btn" id="sup-register-btn">
                    <i class="fas fa-user-plus sup-btn-icon"></i>
                    CREATE ACCOUNT
                </button>
            </form>

            <div class="sup-divider">
                <span class="sup-divider-text">Or sign up with</span>
            </div>

            <div class="sup-social-buttons">
                <button type="button" class="sup-social-btn sup-google-btn">
                    <i class="fab fa-google sup-social-icon"></i>
                    Google
                </button>
                <button type="button" class="sup-social-btn sup-facebook-btn">
                    <i class="fab fa-facebook-f sup-social-icon"></i>
                    Facebook
                </button>
            </div>

            <div class="sup-signin-link">
                Already have an account? <a href="signin.php" class="sup-link">Sign In</a>
            </div>
        </div>
    </div>

    <script>
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
        togglePasswordVisibility('sup-password', 'sup-toggle-password');
        togglePasswordVisibility('sup-confirm-password', 'sup-toggle-confirm-password');

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
        document.getElementById('sup-password').addEventListener('input', function() {
            const password = this.value;
            const result = checkPasswordStrength(password);
            const strengthFill = document.getElementById('sup-strength-fill');
            const strengthText = document.getElementById('sup-strength-text');
            
            strengthFill.style.width = (result.strength * 20) + '%';
            strengthText.textContent = result.text;
            
            // Set color based on strength
            strengthFill.className = 'sup-strength-fill sup-strength-' + result.strength;
        });

        // Form validation
        document.getElementById('sup-create-account-form').addEventListener('submit', function(e) {
            const password = document.getElementById('sup-password').value;
            const confirmPassword = document.getElementById('sup-confirm-password').value;
            const terms = document.getElementById('sup-terms').checked;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match!');
                return;
            }
            
            if (!terms) {
                e.preventDefault();
                alert('Please accept the Terms & Conditions to continue.');
                return;
            }
            
            if (password.length < 8) {    
                e.preventDefault();
                alert('Password must be at least 8 characters long.');
                return;
            }
        });

        // Real-time password confirmation
        document.getElementById('sup-confirm-password').addEventListener('input', function() {
            const password = document.getElementById('sup-password').value;
            const confirmPassword = this.value;
            
            if (confirmPassword && password !== confirmPassword) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>
</html>