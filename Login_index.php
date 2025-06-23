<?php
session_start();
include  'database/db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $connection->query($sql);

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user["id"];
            $_SESSION['user_name'] = $user["name"];
            $_SESSION['role'] = $user["role"];

            if ($user["role"] == "admin") {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: UserProfile.php");
            }
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "No account found with this email!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Movie Booking</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href = "css/Login_index.css" rel = "stylesheet">

</head>
<body class="-sup-body-main">
    <div class="-sup-login-container">
        <div class="-sup-login-header">
            <div class="-sup-movie-icon">
                <i class="fas fa-film"></i>
            </div>
            <h2 class="-sup-login-title">Welcome Back</h2>
            <p class="-sup-login-subtitle">Sign in to your movie booking account</p>
        </div>

        <form method="POST" action="" autocomplete="off" class="-sup-form-main">
            <div class="-sup-input-group">
                <i class="fas fa-envelope -sup-input-icon"></i>
                <input type="email" name="email" class="-sup-input-field" placeholder="Enter your email address" required>
            </div>

            <div class="-sup-input-group">
                <i class="fas fa-lock -sup-input-icon"></i>
                <input type="password" name="password" class="-sup-input-field" placeholder="Enter your password" required>
            </div>

            <button type="submit" class="-sup-btn-login">
                <i class="fas fa-sign-in-alt" style="margin-right: 8px;"></i>
                Sign In
            </button>
        </form>

        <?php if ($error != "") { ?>
            <div class="-sup-error-message">
                <i class="fas fa-exclamation-triangle" style="margin-right: 8px;"></i>
                <?php echo $error; ?>
            </div>
        <?php } ?>

        <div class="-sup-register-link">
            <span class="-sup-register-text">Don't have an account?</span>
            <a href="CreateAccount.php" class="-sup-register-link-btn">Create Account</a>
        </div>
    </div>

    <script>
        // Add loading animation to button on form submit
        document.querySelector('.-sup-form-main').addEventListener('submit', function() {
            const btn = document.querySelector('.-sup-btn-login');
            btn.classList.add('loading');
        });

        // Add focus/blur effects to input fields
        document.querySelectorAll('.-sup-input-field').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.querySelector('.-sup-input-icon').style.color = '#667eea';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.querySelector('.-sup-input-icon').style.color = '#aaa';
            });
        });
    </script>
</body>
</html>