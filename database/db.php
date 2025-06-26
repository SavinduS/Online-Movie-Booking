<?php
$connection = new mysqli("localhost", "root", "", "movie_booking");
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}
?>
