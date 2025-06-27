<?php
// Add this to the TOP of your review_api.php file, right after session_start()

session_start();

// TEMPORARY DEBUG - Add this right after session_start()
error_log("=== SESSION DEBUG ===");
error_log("Session ID: " . session_id());
error_log("Session data: " . print_r($_SESSION, true));
error_log("Session path: " . session_save_path());
error_log("Session name: " . session_name());
error_log("Cookie params: " . print_r(session_get_cookie_params(), true));

// Include database connection
require_once 'database/db.php';

// Set content type to JSON
header('Content-Type: application/json');

// Get the action from URL parameter
$action = $_GET['action'] ?? '';

// Function to validate email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Function to sanitize input
function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

// Function to check if user is logged in
function isUserLoggedIn() {
    $result = isset($_SESSION['user_name']) && isset($_SESSION['user_email']) && 
              !empty($_SESSION['user_name']) && !empty($_SESSION['user_email']);
    
    // Debug logging
    error_log("isUserLoggedIn check:");
    error_log("user_name isset: " . (isset($_SESSION['user_name']) ? 'true' : 'false'));
    error_log("user_email isset: " . (isset($_SESSION['user_email']) ? 'true' : 'false'));
    error_log("user_name value: " . ($_SESSION['user_name'] ?? 'null'));
    error_log("user_email value: " . ($_SESSION['user_email'] ?? 'null'));
    error_log("Result: " . ($result ? 'true' : 'false'));
    
    return $result;
}

// Function to check if user owns the review
function userOwnsReview($reviewEmail) {
    if (!isUserLoggedIn()) {
        return false;
    }
    return $_SESSION['user_email'] === $reviewEmail;
}

// Function to get review owner by ID
function getReviewOwner($reviewId, $connection) {
    $stmt = $connection->prepare("SELECT email FROM reviews WHERE id = ?");
    $stmt->bind_param("i", $reviewId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['email'];
    }
    return null;
}

try {
    switch ($action) {
        case 'check_login':
            // Check login status with detailed logging
            $logged_in = isUserLoggedIn();
            
            error_log("check_login action:");
            error_log("Session ID in check_login: " . session_id());
            error_log("Logged in result: " . ($logged_in ? 'true' : 'false'));
            
            $response = [
                'success' => true,
                'logged_in' => $logged_in,
                'user_name' => $_SESSION['user_name'] ?? null,
                'user_email' => $_SESSION['user_email'] ?? null,
                'debug_session_id' => session_id(),
                'debug_session_data' => $_SESSION
            ];
            
            error_log("Response: " . json_encode($response));
            echo json_encode($response);
            break;
            
        case 'get_all':
            // Get all reviews with ownership information
            $sql = "SELECT * FROM reviews ORDER BY created_at DESC";
            $result = $connection->query($sql);
            
            $reviews = [];
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Add ownership flag for frontend
                    $row['is_owner'] = isUserLoggedIn() && ($_SESSION['user_email'] === $row['email']);
                    $reviews[] = $row;
                }
            }
            
            echo json_encode([
                'success' => true,
                'reviews' => $reviews,
                'user_logged_in' => isUserLoggedIn()
            ]);
            break;
            
        case 'get_single':
            // Get single review by ID
            $id = $_GET['id'] ?? 0;
            
            if (!$id || !is_numeric($id)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid review ID'
                ]);
                break;
            }
            
            $stmt = $connection->prepare("SELECT * FROM reviews WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $review = $result->fetch_assoc();
                // Add ownership flag
                $review['is_owner'] = isUserLoggedIn() && ($_SESSION['user_email'] === $review['email']);
                
                echo json_encode([
                    'success' => true,
                    'review' => $review,
                    'user_logged_in' => isUserLoggedIn()
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Review not found'
                ]);
            }
            break;
            
        
            
          // In your review_api.php, update the 'add' case to ensure proper date setting:

case 'add':
    // Add new review - REQUIRES LOGIN
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode([
            'success' => false,
            'message' => 'Method not allowed'
        ]);
        break;
    }
    
    // Check if user is logged in
    if (!isUserLoggedIn()) {
        echo json_encode([
            'success' => false,
            'message' => 'You must be logged in to submit a review',
            'redirect' => 'Login_index.php'
        ]);
        break;
    }
    
    // Use session data for user info
    $data = [
        'name' => sanitizeInput($_POST['name'] ?? $_SESSION['user_name']), // Use form name if provided
        'email' => $_SESSION['user_email'],
        'rating' => $_POST['rating'] ?? '',
        'comment' => sanitizeInput($_POST['comment'] ?? '')
    ];
    
    // Validate review data
    $errors = [];
    
    if (empty($data['name']) || strlen($data['name']) < 2) {
        $errors[] = 'Name must be at least 2 characters long';
    }
    
    if (empty($data['rating']) || !in_array($data['rating'], ['1', '2', '3', '4', '5'])) {
        $errors[] = 'Please select a valid rating';
    }
    
    if (empty($data['comment']) || strlen($data['comment']) < 10) {
        $errors[] = 'Comment must be at least 10 characters long';
    }
    
    if (!empty($errors)) {
        echo json_encode([
            'success' => false,
            'message' => implode(', ', $errors)
        ]);
        break;
    }
    
    // Check if user has already submitted a review
    $stmt = $connection->prepare("SELECT id FROM reviews WHERE email = ?");
    $stmt->bind_param("s", $data['email']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'You have already submitted a review. You can edit your existing review.'
        ]);
        break;
    }
    
    // Insert new review with explicit timestamp
    $stmt = $connection->prepare("INSERT INTO reviews (name, email, rating, comment, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssis", $data['name'], $data['email'], $data['rating'], $data['comment']);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Review submitted successfully',
            'review_id' => $connection->insert_id
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error submitting review. Please try again.'
        ]);
    }
    break;
            
    case 'update':
    try {
        error_log("UPDATE DEBUG - Starting update process");
        
        // Update existing review - REQUIRES LOGIN AND OWNERSHIP
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'success' => false,
                'message' => 'Method not allowed'
            ]);
            break;
        }
        
        // Check if user is logged in
        if (!isUserLoggedIn()) {
            error_log("UPDATE DEBUG - User not logged in");
            echo json_encode([
                'success' => false,
                'message' => 'You must be logged in to edit a review',
                'redirect' => 'Login_index.php'
            ]);
            break;
        }
        
        $id = $_POST['id'] ?? 0;
        error_log("UPDATE DEBUG - Review ID: " . $id);
        
        if (!$id || !is_numeric($id)) {
            error_log("UPDATE DEBUG - Invalid review ID");
            echo json_encode([
                'success' => false,
                'message' => 'Invalid review ID'
            ]);
            break;
        }
        
        // Check if review exists and get owner
        $reviewOwner = getReviewOwner($id, $connection);
        error_log("UPDATE DEBUG - Review owner: " . ($reviewOwner ?? 'NULL'));
        
        if (!$reviewOwner) {
            error_log("UPDATE DEBUG - Review not found");
            echo json_encode([
                'success' => false,
                'message' => 'Review not found'
            ]);
            break;
        }
        
        // Check if user owns the review
        $currentUserEmail = $_SESSION['user_email'] ?? '';
        error_log("UPDATE DEBUG - Current user email: " . $currentUserEmail);
        error_log("UPDATE DEBUG - Review owner email: " . $reviewOwner);
        
        if ($currentUserEmail !== $reviewOwner) {
            error_log("UPDATE DEBUG - User doesn't own review");
            echo json_encode([
                'success' => false,
                'message' => 'You can only edit your own reviews'
            ]);
            break;
        }
        
        // Get form data including name
        $data = [
            'name' => sanitizeInput($_POST['name'] ?? ''),
            'rating' => $_POST['rating'] ?? '',
            'comment' => sanitizeInput($_POST['comment'] ?? '')
        ];
        
        error_log("UPDATE DEBUG - Data to update: " . print_r($data, true));
        
        // Validate data
        $errors = [];
        
        if (empty($data['name']) || strlen($data['name']) < 2) {
            $errors[] = 'Name must be at least 2 characters long';
        }
        
        if (empty($data['rating']) || !in_array($data['rating'], ['1', '2', '3', '4', '5'])) {
            $errors[] = 'Please select a valid rating';
        }
        
        if (empty($data['comment']) || strlen($data['comment']) < 10) {
            $errors[] = 'Comment must be at least 10 characters long';
        }
        
        if (!empty($errors)) {
            error_log("UPDATE DEBUG - Validation errors: " . implode(', ', $errors));
            echo json_encode([
                'success' => false,
                'message' => implode(', ', $errors)
            ]);
            break;
        }
        
        // Update review (name, rating and comment)
        error_log("UPDATE DEBUG - Preparing SQL update");
        $stmt = $connection->prepare("UPDATE reviews SET name = ?, rating = ?, comment = ? WHERE id = ? AND email = ?");

        if (!$stmt) {
            error_log("UPDATE DEBUG - Prepare failed: " . $connection->error);
            echo json_encode([
                'success' => false,
                'message' => 'Database prepare error'
            ]);
            break;
        }

        $stmt->bind_param("sisis", $data['name'], $data['rating'], $data['comment'], $id, $currentUserEmail);
        error_log("UPDATE DEBUG - Executing SQL with name: " . $data['name'] . ", rating: " . $data['rating'] . ", comment: " . $data['comment'] . ", id: " . $id . ", email: " . $currentUserEmail);
        
        if ($stmt->execute()) {
            $affected_rows = $stmt->affected_rows;
            error_log("UPDATE DEBUG - SQL executed, affected rows: " . $affected_rows);
            
            if ($affected_rows > 0) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Review updated successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'No changes made or review not found'
                ]);
            }
        } else {
            error_log("UPDATE DEBUG - Execute failed: " . $stmt->error);
            echo json_encode([
                'success' => false,
                'message' => 'Database execution error'
            ]);
        }
        
    } catch (Exception $e) {
        error_log("UPDATE DEBUG - Exception caught: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Update error: ' . $e->getMessage()
        ]);
    }
    break;
            
        case 'delete':
            // Delete review - REQUIRES LOGIN AND OWNERSHIP
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode([
                    'success' => false,
                    'message' => 'Method not allowed'
                ]);
                break;
            }
            
            // Check if user is logged in
            if (!isUserLoggedIn()) {
                echo json_encode([
                    'success' => false,
                    'message' => 'You must be logged in to delete a review',
                    'redirect' => 'Login_index.php'
                ]);
                break;
            }
            
            $id = $_POST['id'] ?? 0;
            
            if (!$id || !is_numeric($id)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid review ID'
                ]);
                break;
            }
            
            // Check if review exists and get owner
            $reviewOwner = getReviewOwner($id, $connection);
            if (!$reviewOwner) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Review not found'
                ]);
                break;
            }
            
            // Check if user owns the review
            if (!userOwnsReview($reviewOwner)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'You can only delete your own reviews'
                ]);
                break;
            }
            
            // Delete review
            $stmt = $connection->prepare("DELETE FROM reviews WHERE id = ? AND email = ?");
            $stmt->bind_param("is", $id, $_SESSION['user_email']);
            
            if ($stmt->execute() && $stmt->affected_rows > 0) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Review deleted successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error deleting review'
                ]);
            }
            break;
            
        case 'get_user_review':
            // Get current user's review if exists
            if (!isUserLoggedIn()) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Not logged in'
                ]);
                break;
            }
            
            $stmt = $connection->prepare("SELECT * FROM reviews WHERE email = ?");
            $stmt->bind_param("s", $_SESSION['user_email']);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $review = $result->fetch_assoc();
                echo json_encode([
                    'success' => true,
                    'has_review' => true,
                    'review' => $review
                ]);
            } else {
                echo json_encode([
                    'success' => true,
                    'has_review' => false
                ]);
            }
            break;
            
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Invalid action'
            ]);
            break;
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Server error occurred'
    ]);
    
    // Log the actual error for debugging (don't expose to user)
    error_log("Review API Error: " . $e->getMessage());
}

// Close database connection
if (isset($connection)) {
    $connection->close();
}
?>