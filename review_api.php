

<?php
session_start();
// review_api.php - Main API handler for all CRUD operations
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// Include database connection
require_once 'database/db.php';

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

// Function to validate review data
function validateReviewData($data) {
    $errors = [];
    
    if (empty($data['name']) || strlen($data['name']) < 2) {
        $errors[] = 'Name must be at least 2 characters long';
    }
    
    if (empty($data['email']) || !isValidEmail($data['email'])) {
        $errors[] = 'Please provide a valid email address';
    }
    
    if (empty($data['rating']) || !in_array($data['rating'], ['1', '2', '3', '4', '5'])) {
        $errors[] = 'Please select a valid rating';
    }
    
    if (empty($data['comment']) || strlen($data['comment']) < 10) {
        $errors[] = 'Comment must be at least 10 characters long';
    }
    
    return $errors;
}

try {
    switch ($action) {
        case 'get_all':
            // Get all reviews
            $sql = "SELECT * FROM reviews ORDER BY created_at DESC";
            $result = $connection->query($sql);
            
            $reviews = [];
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $reviews[] = $row;
                }
            }
            
            echo json_encode([
                'success' => true,
                'reviews' => $reviews
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
                echo json_encode([
                    'success' => true,
                    'review' => $review
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Review not found'
                ]);
            }
            break;
            
        case 'add':
            // Add new review
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode([
                    'success' => false,
                    'message' => 'Method not allowed'
                ]);
                break;
            }
            
            session_start(); // Add at the top if not already done

            $data = [
                'name' => $_SESSION['user_name'] ?? '',
                'email' => $_SESSION['user_email'] ?? '',
                'rating' => $_POST['rating'] ?? '',
                'comment' => sanitizeInput($_POST['comment'] ?? '')
            ];
            // Check if user is logged in
            if (empty($data['name']) || empty($data['email'])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'You must be logged in to submit a review'
                ]);
                break;
            }

            
            // Validate data
            $errors = validateReviewData($data);
            if (!empty($errors)) {
                echo json_encode([
                    'success' => false,
                    'message' => implode(', ', $errors)
                ]);
                break;
            }
            
            // Check if email already exists (optional - remove if you want to allow multiple reviews per email)
            $stmt = $connection->prepare("SELECT id FROM reviews WHERE email = ?");
            $stmt->bind_param("s", $data['email']);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                echo json_encode([
                    'success' => false,
                    'message' => 'You have already submitted a review with this email address'
                ]);
                break;
            }
            
            // Insert new review
            $stmt = $connection->prepare("INSERT INTO reviews (name, email, rating, comment) VALUES (?, ?, ?, ?)");
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
                    'message' => 'Error submitting review: ' . $connection->error
                ]);
            }
            break;
            
        case 'update':
            // Update existing review
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode([
                    'success' => false,
                    'message' => 'Method not allowed'
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
            
            $data = [
                'name' => sanitizeInput($_POST['name'] ?? ''),
                'email' => sanitizeInput($_POST['email'] ?? ''),
                'rating' => $_POST['rating'] ?? '',
                'comment' => sanitizeInput($_POST['comment'] ?? '')
            ];
            
            // Validate data
            $errors = validateReviewData($data);
            if (!empty($errors)) {
                echo json_encode([
                    'success' => false,
                    'message' => implode(', ', $errors)
                ]);
                break;
            }
            
            // Check if review exists
            $stmt = $connection->prepare("SELECT id FROM reviews WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Review not found'
                ]);
                break;
            }
            
            // Update review
            $stmt = $connection->prepare("UPDATE reviews SET name = ?, email = ?, rating = ?, comment = ? WHERE id = ?");
            $stmt->bind_param("ssisi", $data['name'], $data['email'], $data['rating'], $data['comment'], $id);
            
            if ($stmt->execute()) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Review updated successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error updating review: ' . $connection->error
                ]);
            }
            break;
            
        case 'delete':
            // Delete review
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode([
                    'success' => false,
                    'message' => 'Method not allowed'
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
            
            // Check if review exists
            $stmt = $connection->prepare("SELECT id FROM reviews WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Review not found'
                ]);
                break;
            }
            
            // Delete review
            $stmt = $connection->prepare("DELETE FROM reviews WHERE id = ?");
            $stmt->bind_param("i", $id);
            
            if ($stmt->execute()) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Review deleted successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error deleting review: ' . $connection->error
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
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}

// Close database connection
if (isset($connection)) {
    $connection->close();
}
?>