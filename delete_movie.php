<?php
include 'database/db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Delete thumbnail from server
    $getQuery = "SELECT thumbnail FROM movies WHERE id = ?";
    $getStmt = $connection->prepare($getQuery);
    $getStmt->bind_param("i", $id);
    $getStmt->execute();
    $getResult = $getStmt->get_result();
    $movie = $getResult->fetch_assoc();
    if ($movie && file_exists($movie['thumbnail'])) {
        unlink($movie['thumbnail']);
    }

    // Delete from database
    $query = "DELETE FROM movies WHERE id = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: admin_movies.php");
    exit();
}
?>
