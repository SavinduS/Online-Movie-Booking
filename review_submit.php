<?php
include 'database/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $rating = intval($_POST["rating"]);
    $comment = trim($_POST["comment"]);

    $initials = strtoupper(substr($name, 0, 1) . substr(strrchr($name, ' '), 1, 1));
    $review_date = date("Y-m-d");

    $stmt = $connection->prepare("INSERT INTO reviews (name, email, initials, rating, comment, review_date) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssiss", $name, $email, $initials, $rating, $comment, $review_date);
    $stmt->execute();

    header("Location: reviews.php");
    exit();
}
?>
