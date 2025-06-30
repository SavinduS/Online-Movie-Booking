<?php
include 'database/db.php';
include 'auth_check.php';

// Check if user is admin
checkAdminAuth();

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    try {
        // First, get the image path to delete the file
        $image_query = "SELECT image FROM films WHERE id = ?";
        $image_stmt = $connection->prepare($image_query);
        $image_stmt->bind_param("i", $id);
        $image_stmt->execute();
        $image_result = $image_stmt->get_result();
        
        $image_path = '';
        if ($image_row = $image_result->fetch_assoc()) {
            $image_path = $image_row['image'];
        }
        $image_stmt->close();
        
        // Delete the movie from database
        $sql = "DELETE FROM films WHERE id = ?";
        $stmt = $connection->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("i", $id);
            
            if ($stmt->execute()) {
                // Delete the image file if it exists
                if (!empty($image_path) && file_exists($image_path)) {
                    unlink($image_path);
                }
                
                $_SESSION['message'] = "Movie deleted successfully!";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "Error deleting movie: " . $stmt->error;
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
    $_SESSION['message'] = "Invalid movie ID!";
    $_SESSION['message_type'] = "error";
}

// Redirect back to admin movies page
header("Location: admin_movies.php");
exit();
?>