<?php
$connection = new mysqli("localhost", "root", 12345, "movie_booking");
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}
?>
