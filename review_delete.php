<?php
include 'database/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST["id"];
    $email = $_POST["email"]; // for validation

    // Secure deletion
    $stmt = $connection->prepare("DELETE FROM reviews WHERE id = ? AND email = ?");
    $stmt->bind_param("is", $id, $email);
    $stmt->execute();

    header("Location: reviews.php");
    exit();
}
?>
