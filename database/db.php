<?php
$connection = new mysqli("movie_booking", "root", "", "movie_booking");
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}
?>
