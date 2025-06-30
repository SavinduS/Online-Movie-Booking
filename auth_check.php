<?php
// auth_check.php - Admin authentication protection
// Include this file at the top of admin pages only

function checkAdminAuth() {
    // Start session if not already started
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    // Check if user is logged in
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
        // User is not logged in, redirect to login
        header("Location: login_index.php?error=please_login");
        exit();
    }
    
    // Check if user has admin role
    if ($_SESSION['role'] !== 'admin') {
        // User is not admin, show unauthorized message and redirect
        echo "<script>
                alert('Access Denied! You do not have permission to access this page.');
                window.location.href = 'login_index.php';
              </script>";
        exit();
    }
    
    // Optional: Check session timeout (24 hours)
    if (isset($_SESSION['last_activity'])) {
        $timeout = 24 * 60 * 60; // 24 hours in seconds
        if (time() - $_SESSION['last_activity'] > $timeout) {
            // Session expired
            session_destroy();
            echo "<script>
                    alert('Your session has expired. Please login again.');
                    window.location.href = 'login_index.php';
                  </script>";
            exit();
        }
    }
    
    // Update last activity time
    $_SESSION['last_activity'] = time();
    
    return true;
}

function getUserInfo() {
    // Return current user information with safe handling
    if (isset($_SESSION['user_id'])) {
        // Handle user name safely
        $user_name = '';
        if (isset($_SESSION['user_name'])) {
            $user_name = $_SESSION['user_name'];
        } elseif (isset($_SESSION['first_name']) && isset($_SESSION['last_name'])) {
            $user_name = $_SESSION['first_name'] . ' ' . $_SESSION['last_name'];
        } elseif (isset($_SESSION['first_name'])) {
            $user_name = $_SESSION['first_name'];
        } else {
            $user_name = 'User';
        }
        
        return [
            'id' => $_SESSION['user_id'],
            'name' => $user_name,
            'first_name' => $_SESSION['first_name'] ?? '',
            'last_name' => $_SESSION['last_name'] ?? '',
            'email' => $_SESSION['email'] ?? '',
            'role' => $_SESSION['role'] ?? ''
        ];
    }
    return null;
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}
?>