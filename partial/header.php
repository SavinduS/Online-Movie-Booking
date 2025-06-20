<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
     <link rel="icon" type="image/png" href="images\favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Swans Cinema | Reviews</title>
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
        .logo-savi {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: #6b46c1;
            font-weight: 700;
            font-size: 1.5rem;
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
            color:rgb(97, 59, 185);
            background: linear-gradient(135deg, rgba(107, 70, 193, 0.1), rgba(147, 51, 234, 0.1));
        }

        /* Login Button */
        .login-btn-savi {
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

        .login-btn-savi:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(107, 70, 193, 0.4);
            background: linear-gradient(135deg, #7c3aed, #a855f7);
        }

        .login-btn-savi:active {
            transform: translateY(-1px);
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

        /* Demo Content */
        .demo-content-savi {
            padding: 60px 20px;
            max-width: 1200px;
            margin: 0 auto;
            text-align: center;
        }

        .demo-content-savi h1 {
            color: #6b46c1;
            font-size: 2.5rem;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .demo-content-savi p {
            color: #4a5568;
            font-size: 1.1rem;
            line-height: 1.8;
            max-width: 600px;
            margin: 0 auto;
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

            .login-btn-savi {
                margin-top: 20px;
                justify-content: center;
                padding: 15px 30px;
            }

            .navbar-container-savi {
                padding: 0 15px;
            }

            .demo-content-savi h1 {
                font-size: 2rem;
            }

            .demo-content-savi p {
                font-size: 1rem;
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
            <?php $current_page = basename($_SERVER['PHP_SELF']); ?>

<nav class="navbar-savi">
    <div class="navbar-container-savi">

        <!-- Logo -->
        <div class="logo-savi">
            <i class="fas fa-film"></i>    Swans Cinema
        </div>

        <!-- Navigation Links -->
        <ul class="nav-menu-savi">
            <li class="nav-item-savi">
                <a href="index.php" class="nav-link-savi <?php if ($current_page == 'index.php') echo 'active-savi'; ?>">
                    <i class="fas fa-home"></i> Home
                </a>
            </li>
            <li class="nav-item-savi">
                <a href="now-showing.php" class="nav-link-savi <?php if ($current_page == 'now-showing.php') echo 'active-savi'; ?>">
                    <i class="fas fa-video"></i> Now Showing
                </a>
            </li>
            <li class="nav-item-savi">
                <a href="reviews.php" class="nav-link-savi <?php if ($current_page == 'reviews.php') echo 'active-savi'; ?>">
                    <i class="fas fa-star"></i> Reviews
                </a>
            </li>
            <li class="nav-item-savi">
                <a href="about.php" class="nav-link-savi <?php if ($current_page == 'about.php') echo 'active-savi'; ?>">
                    <i class="fas fa-info-circle"></i> About Us
                </a>
            </li>
        </ul>

        <!-- Login Button -->
        <div class="login-section-savi">
            <a href="login.php" class="login-btn-savi">
                <i class="fas fa-user"></i> Login
            </a>
        </div>
    </div>
</nav>

           

            <!-- Mobile Menu Toggle -->
            <label for="mobile-menu-savi" class="mobile-toggle-savi">
                <div class="hamburger-line-savi"></div>
                <div class="hamburger-line-savi"></div>
                <div class="hamburger-line-savi"></div>
            </label>
        </div>
    </nav>

    
</body>
</html>