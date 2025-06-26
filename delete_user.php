<?php


// Database connection
$conn = new mysqli("localhost", "root", "12345", "movie_booking");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Delete user if ID is valid
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $userId = intval($_GET['id']);

    // Prevent admin from deleting their own account
    if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $userId) {
        echo "You cannot delete your own admin account.";
        exit();
    }

    // Delete the user
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);

    if ($stmt->execute()) {
        header("Location: admin_users.php");
        exit();
    } else {
        echo "Error deleting user: " . $stmt->error;
    }
} else {
    echo "Invalid request.";
}
?>
