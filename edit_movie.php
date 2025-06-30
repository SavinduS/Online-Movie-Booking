<?php
include 'database/db.php';
include 'auth_check.php';

// Check if user is admin
checkAdminAuth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $id = $_POST['id'] ?? '';
    $title = $_POST['title'] ?? '';
    $category = $_POST['category'] ?? '';
    $rating = $_POST['rating'] ?? '';
    $status = $_POST['status'] ?? '';
    $release_date = $_POST['release_date'] ?? '';
    
    // Validate required fields
    if (empty($id) || empty($title) || empty($category) || empty($rating) || empty($status) || empty($release_date)) {
        $_SESSION['message'] = "All fields are required!";
        $_SESSION['message_type'] = "error";
        header("Location: admin_movies.php");
        exit();
    }
    
    // Handle file upload (optional for edit)
    $image_path = null;
    $update_image = false;
    
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
                    $update_image = true;
                    
                    // Delete old image if it exists
                    $old_image_query = "SELECT image FROM films WHERE id = ?";
                    $old_stmt = $connection->prepare($old_image_query);
                    $old_stmt->bind_param("i", $id);
                    $old_stmt->execute();
                    $old_result = $old_stmt->get_result();
                    
                    if ($old_row = $old_result->fetch_assoc()) {
                        $old_image = $old_row['image'];
                        if (!empty($old_image) && file_exists($old_image)) {
                            unlink($old_image);
                        }
                    }
                    $old_stmt->close();
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
    
    // Update database
    try {
        if ($update_image) {
            // Update with new image
            $sql = "UPDATE films SET title = ?, category = ?, image = ?, rating = ?, status = ?, release_date = ? WHERE id = ?";
            $stmt = $connection->prepare($sql);
            $stmt->bind_param("ssssssi", $title, $category, $image_path, $rating, $status, $release_date, $id);
        } else {
            // Update without changing image
            $sql = "UPDATE films SET title = ?, category = ?, rating = ?, status = ?, release_date = ? WHERE id = ?";
            $stmt = $connection->prepare($sql);
            $stmt->bind_param("sssssi", $title, $category, $rating, $status, $release_date, $id);
        }
        
        if ($stmt) {
            if ($stmt->execute()) {
                $_SESSION['message'] = "Movie updated successfully!";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "Error updating movie: " . $stmt->error;
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