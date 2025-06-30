<?php
include 'database/db.php';
include 'auth_check.php';

// Check if user is admin
checkAdminAuth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $title = $_POST['title'] ?? '';
    $category = $_POST['category'] ?? '';
    $rating = $_POST['rating'] ?? '';
    $status = $_POST['status'] ?? '';
    $release_date = $_POST['release_date'] ?? '';
    
    // Handle file upload
    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'images/film flyers/';
        
        // Create directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_info = pathinfo($_FILES['image']['name']);
        $extension = strtolower($file_info['extension']);
        
        // Validate file type
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($extension, $allowed_types)) {
            // Generate unique filename
            $filename = uniqid() . '.' . $extension;
            $target_path = $upload_dir . $filename;
            
            // Check file size (5MB max)
            if ($_FILES['image']['size'] <= 5 * 1024 * 1024) {
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                    $image_path = $target_path;
                } else {
                    $_SESSION['message'] = "Failed to upload image!";
                    $_SESSION['message_type'] = "error";
                    header("Location: admin_movies.php");
                    exit();
                }
            } else {
                $_SESSION['message'] = "Image file is too large! Maximum size is 5MB.";
                $_SESSION['message_type'] = "error";
                header("Location: admin_movies.php");
                exit();
            }
        } else {
            $_SESSION['message'] = "Invalid file type! Please upload JPG, PNG, GIF, or WebP files only.";
            $_SESSION['message_type'] = "error";
            header("Location: admin_movies.php");
            exit();
        }
    }
    
    // Validate required fields
    if (empty($title) || empty($category) || empty($rating) || empty($status) || empty($release_date)) {
        $_SESSION['message'] = "All fields are required!";
        $_SESSION['message_type'] = "error";
        header("Location: admin_movies.php");
        exit();
    }
    
    // Insert into database (using 'films' table as per your database structure)
    try {
        $sql = "INSERT INTO films (title, category, image, rating, status, release_date) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $connection->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("ssssss", $title, $category, $image_path, $rating, $status, $release_date);
            
            if ($stmt->execute()) {
                $_SESSION['message'] = "Movie added successfully!";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "Error adding movie: " . $stmt->error;
                $_SESSION['message_type'] = "error";
            }
            
            $stmt->close();
        } else {
            $_SESSION['message'] = "Database error: " . $connection->error;
            $_SESSION['message_type'] = "error";
        }
    } catch (Exception $e) {
        $_SESSION['message'] = "Error: " . $e->getMessage();
        $_SESSION['message_type'] = "error";
    }
} else {
    $_SESSION['message'] = "Invalid request method!";
    $_SESSION['message_type'] = "error";
}

// Redirect back to admin movies page
header("Location: admin_movies.php");
exit();
?>