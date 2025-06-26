<?php
include 'database/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $type = $_POST['type'];
    $price = floatval($_POST['price']);
    $show_time = $_POST['show_time'];
    $thumbnail = $_POST['current_thumbnail'];

    // Handle new image if uploaded
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "images/film_flyers/";
        $fileName = basename($_FILES["thumbnail"]["name"]);
        $targetFile = $targetDir . uniqid() . "_" . $fileName;

        if (move_uploaded_file($_FILES["thumbnail"]["tmp_name"], $targetFile)) {
            if ($thumbnail && file_exists($thumbnail)) {
                unlink($thumbnail);
            }
            $thumbnail = $targetFile;
        }
    }

    $stmt = $connection->prepare("UPDATE movies SET title=?, type=?, price=?, show_time=?, thumbnail=? WHERE id=?");
    $stmt->bind_param("ssdssi", $title, $type, $price, $show_time, $thumbnail, $id);

    if ($stmt->execute()) {
        header("Location: admin_movies.php");
        exit();
    } else {
        echo "Error updating: " . $stmt->error;
    }
}
?>
