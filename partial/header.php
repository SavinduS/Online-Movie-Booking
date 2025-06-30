<?php
// Start session at the beginning of every page
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get current page for active navigation highlighting
$current_page = basename($_SERVER['PHP_SELF']);

// Check if user is logged in
$is_logged_in = isset($_SESSION['user_id']) || isset($_SESSION['username']) || isset($_SESSION['logged_in']);
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="images/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            background: linear-gradient(135deg, #f8f6ff 0%, #e8e0ff 100%);
            min-height: 100vh;
            padding-top: 80px;
        }

        /* Navigation Styles */
        .navbar-savi {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(147, 112, 219, 0.1);
            z-index: 1000;
            transition: all 0.3s ease;
            border-bottom: 1px solid rgba(147, 112, 219, 0.1);
        }

        .navbar-container-savi {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 70px;
        }

        /* Logo Styles */
        .logo-savi i {
            margin-right: 8px;
            vertical-align: middle;
        }

        .logo-savi {
            display: flex;
            align-items: center;
            font-size: 28px;
            font-weight: 800;
            color: #6b46c1;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .logo-savi:hover {
            transform: translateY(-2px);
            color: #9333ea;
        }

        .logo-icon-savi {
            margin-right: 20px;
            font-size: 1.8rem;
            background: linear-gradient(135deg, #6b46c1, #9333ea);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Navigation Menu */
        .nav-menu-savi {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
            gap: 0;
        }

        .nav-item-savi {
            position: relative;
        }

        .nav-link-savi {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            text-decoration: none;
            color: #4a5568;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            border-radius: 8px;
            margin: 0 4px;
            position: relative;
            overflow: hidden;
        }

        .nav-link-savi::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(107, 70, 193, 0.1), rgba(147, 51, 234, 0.1));
            transition: left 0.3s ease;
            z-index: -1;
        }

        .nav-link-savi:hover::before {
            left: 0;
        }

        .nav-link-savi:hover {
            color: #6b46c1;
            transform: translateY(-2px);
        }

        .nav-link-savi i {
            margin-right: 8px;
            font-size: 1rem;
        }

        /* Active Link */
        .nav-link-savi.active-savi {
            color: rgb(97, 59, 185);
            background: linear-gradient(135deg, rgba(107, 70, 193, 0.1), rgba(147, 51, 234, 0.1));
        }

        /* User Actions Container */
        .user-actions-savi {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* Login/Logout Button */
        .login-btn-savi, .logout-btn-savi {
            background: linear-gradient(135deg, #6b46c1, #9333ea);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(107, 70, 193, 0.3);
        }

        /* Profile Button */
        .profile-btn-savi {
            background: linear-gradient(135deg, #059669, #10b981);
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(5, 150, 105, 0.3);
        }

        .login-btn-savi:hover, .logout-btn-savi:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(107, 70, 193, 0.4);
            background: linear-gradient(135deg, #7c3aed, #a855f7);
        }

        .profile-btn-savi:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(5, 150, 105, 0.4);
            background: linear-gradient(135deg, #047857, #059669);
        }

        .login-btn-savi:active, .logout-btn-savi:active, .profile-btn-savi:active {
            transform: translateY(-1px);
        }

        /* Logout button specific styling */
        .logout-btn-savi {
            background: linear-gradient(135deg, #dc2626, #ef4444);
            box-shadow: 0 4px 15px rgba(220, 38, 38, 0.3);
        }

        .logout-btn-savi:hover {
            background: linear-gradient(135deg, #b91c1c, #dc2626);
            box-shadow: 0 8px 25px rgba(220, 38, 38, 0.4);
        }

        /* Welcome text */
        .welcome-text-savi {
            color: #4a5568;
            font-size: 0.9rem;
            font-weight: 500;
            margin-right: 10px;
        }

        /* Mobile Menu Toggle */
        .mobile-toggle-savi {
            display: none;
            flex-direction: column;
            cursor: pointer;
            padding: 8px;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .mobile-toggle-savi:hover {
            background: rgba(107, 70, 193, 0.1);
        }

        .hamburger-line-savi {
            width: 25px;
            height: 3px;
            background: #6b46c1;
            margin: 3px 0;
            border-radius: 2px;
            transition: all 0.3s ease;
        }

        /* Mobile Menu Checkbox */
        .mobile-menu-checkbox-savi {
            display: none;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .mobile-toggle-savi {
                display: flex;
            }

            .nav-menu-savi {
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: rgba(255, 255, 255, 0.98);
                backdrop-filter: blur(10px);
                flex-direction: column;
                padding: 20px;
                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
                transform: translateY(-100%);
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s ease;
                border-top: 1px solid rgba(107, 70, 193, 0.1);
            }

            .mobile-menu-checkbox-savi:checked ~ .navbar-container-savi .nav-menu-savi {
                transform: translateY(0);
                opacity: 1;
                visibility: visible;
            }

            .mobile-menu-checkbox-savi:checked ~ .navbar-container-savi .mobile-toggle-savi .hamburger-line-savi:nth-child(1) {
                transform: rotate(45deg) translate(6px, 6px);
            }

            .mobile-menu-checkbox-savi:checked ~ .navbar-container-savi .mobile-toggle-savi .hamburger-line-savi:nth-child(2) {
                opacity: 0;
            }

            .mobile-menu-checkbox-savi:checked ~ .navbar-container-savi .mobile-toggle-savi .hamburger-line-savi:nth-child(3) {
                transform: rotate(-45deg) translate(6px, -6px);
            }

            .nav-item-savi {
                margin: 8px 0;
            }

            .nav-link-savi {
                padding: 15px 20px;
                border-radius: 12px;
                justify-content: center;
                font-size: 1rem;
            }

            .user-actions-savi {
                flex-direction: column;
                gap: 15px;
                margin-top: 20px;
                width: 100%;
            }

            .login-btn-savi, .logout-btn-savi, .profile-btn-savi {
                justify-content: center;
                padding: 15px 30px;
                width: 100%;
            }

            .welcome-text-savi {
                text-align: center;
                margin-right: 0;
                margin-bottom: 10px;
            }

            .navbar-container-savi {
                padding: 0 15px;
            }
        }

        @media (max-width: 480px) {
            .logo-savi {
                font-size: 1.3rem;
            }

            .logo-icon-savi {
                font-size: 1.5rem;
            }

            .navbar-container-savi {
                height: 60px;
            }

            body {
                padding-top: 60px;
            }
        }

        /* Smooth scroll behavior */
        html {
            scroll-behavior: smooth;
        }

        /* Focus styles for accessibility */
        .nav-link-savi:focus,
        .login-btn-savi:focus,
        .logout-btn-savi:focus,
        .profile-btn-savi:focus,
        .mobile-toggle-savi:focus {
            outline: 2px solid #6b46c1;
            outline-offset: 2px;
        }
    </style>
</head>
<body>
    <nav class="navbar-savi">
        <input type="checkbox" id="mobile-menu-savi" class="mobile-menu-checkbox-savi">
        <div class="navbar-container-savi">
            <!-- Logo -->
            <a href="index.php" class="logo-savi">
                <i class="fa-solid fa-video"></i> Swans Cinema
            </a>

            <!-- Navigation Links -->
            <ul class="nav-menu-savi">
                <li class="nav-item-savi">
                    <a href="index.php" class="nav-link-savi <?php if ($current_page == 'index.php') echo 'active-savi'; ?>">
                        <i class="fas fa-home"></i> Home
                    </a>
                </li>
                <li class="nav-item-savi">
                    <a href="now_showing.php" class="nav-link-savi <?php if ($current_page == 'now_showing.php') echo 'active-savi'; ?>">
                        <i class="fas fa-video"></i> Now Showing
                    </a>
                </li>
                <li class="nav-item-savi">
                    <a href="reviews.php" class="nav-link-savi <?php if ($current_page == 'reviews.php') echo 'active-savi'; ?>">
                        <i class="fas fa-star"></i> Reviews
                    </a>
                </li>
                <li class="nav-item-savi">
                    <a href="aboutus.php" class="nav-link-savi <?php if ($current_page == 'aboutus.php') echo 'active-savi'; ?>">
                        <i class="fas fa-info-circle"></i> About Us
                    </a>
                </li>

                <!-- User Actions for Mobile (shown in mobile menu) -->
                <li class="nav-item-savi mobile-user-actions" style="display: none;">
                    <?php if ($is_logged_in): ?>
                        <?php if (!empty($username)): ?>
                            <div class="welcome-text-savi">Welcome, <?php echo htmlspecialchars($username); ?>!</div>
                        <?php endif; ?>
                        <div class="user-actions-savi">
                            <a href="UserProfile.php" class="login-btn-savi">
                                <i class="fas fa-user-circle"></i> Profile
                            </a>
                            <a href="logout.php" class="logout-btn-savi">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </div>
                    <?php else: ?>
                        <a href="login_index.php" class="login-btn-savi">
                            <i class="fas fa-user"></i> Login
                        </a>
                    <?php endif; ?>
                </li>
            </ul>

            <!-- User Actions for Desktop -->
            <div class="user-actions-savi desktop-user-actions">
    <?php if ($is_logged_in): ?>
        <?php if (!empty($username)): ?>
            <span class="welcome-text-savi">Welcome, <?php echo htmlspecialchars($username); ?>!</span>
        <?php endif; ?>
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <a href="admin_dashboard.php" class="login-btn-savi">
                <i class="fas fa-user-circle"></i> Dashboard
            </a>
        <?php else: ?>
            <a href="UserProfile.php" class="login-btn-savi">
                <i class="fas fa-user-circle"></i> Profile
            </a>
        <?php endif; ?>
        <a href="logout.php" class="logout-btn-savi">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    <?php else: ?>
        <a href="login_index.php" class="login-btn-savi">
            <i class="fas fa-user"></i> Login
        </a>
    <?php endif; ?>
</div>


            <!-- Mobile Menu Toggle -->
            <label for="mobile-menu-savi" class="mobile-toggle-savi">
                <div class="hamburger-line-savi"></div>
                <div class="hamburger-line-savi"></div>
                <div class="hamburger-line-savi"></div>
            </label>
        </div>
    </nav>

    <style>
        /* Mobile-specific user actions styling */
        @media (max-width: 768px) {
            .desktop-user-actions {
                display: none;
            }
            
            .mobile-user-actions {
                display: block !important;
            }
        }

        @media (min-width: 769px) {
            .mobile-user-actions {
                display: none !important;
            }
        }
    </style>