<?php
session_start();
include 'database/db.php';

function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title']);
    $type = sanitize($_POST['type']);
    $price = floatval($_POST['price']);
    $show_time = $_POST['show_time'];
    $thumbnailPath = '';

    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] == UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['thumbnail']['tmp_name'];
        $fileName = basename($_FILES['thumbnail']['name']);
        $targetDir = 'images/film_flyers/';
        $targetPath = $targetDir . time() . '_' . $fileName;

        if (move_uploaded_file($fileTmp, $targetPath)) {
            $thumbnailPath = $targetPath;
        } else {
            $_SESSION['message'] = "Image upload failed!";
            $_SESSION['message_type'] = "error";
            header("Location: admin_movies.php");
            exit;
        }
    }

    $stmt = $connection->prepare("INSERT INTO movies (title, type, price, show_time, thumbnail) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdss", $title, $type, $price, $show_time, $thumbnailPath);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Movie added successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Failed to add movie!";
        $_SESSION['message_type'] = "error";
    }

    $stmt->close();
    header("Location: admin_movies.php");
}
?>
