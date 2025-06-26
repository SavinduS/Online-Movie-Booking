<?php
session_start();

// Sample data - replace with your database queries
$movie_title = $_GET['movie_title'] ?? "Sample Movie";

$film_halls = [
    1 => ['name' => 'Swans Cinema 3D Colombo', 'location' => 'Colombo'],
    2 => ['name' => 'Swans Cinema 3D Kandy', 'location' => 'Kandy'],
    3 => ['name' => 'Swans Cinema Galle', 'location' => 'Galle'],
    4 => ['name' => 'Swans Cinema Negombo', 'location' => 'Negombo']
];

$showtimes = ['10:00 AM', '1:00 PM', '4:00 PM', '7:00 PM', '10:00 PM'];

if ($_POST) {
    $_SESSION['booking_details'] = [
        'movie_id' => $movie_id,
        'movie_title' => $movie_title,
        'date' => $_POST['date'],
        'time' => $_POST['time'],
        'hall_id' => $_POST['hall_id'],
        'hall_name' => $film_halls[$_POST['hall_id']]['name']
    ];
    header('Location: seat_booking.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<?php include 'partial/header.php'; ?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Tickets - <?php echo htmlspecialchars($movie_title); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/booking.css">
</head>
<body>
    <div class="container">
        <div class="content-wrapper">
            <!-- Header -->
            <div class="header-card">
                <div class="header-content">
                    <button class="back-button" onclick="history.back()">
                        <i class="fas fa-arrow-left"></i>
                    </button>
                    <div class="header-text">
                        <h1 class="movie-title"><?php echo htmlspecialchars($movie_title); ?></h1>
                        <p class="subtitle">
                            <i class="fas fa-film"></i>
                            Select your preferred showtime and cinema
                        </p>
                    </div>
                </div>
            </div>

            <form method="POST" class="booking-form">
                <!-- Date Selection -->
                <div class="card">
                    <h2 class="section-title">
                        <i class="fas fa-calendar-alt"></i>
                        Select Date
                    </h2>
                    <div class="date-grid">
                        <?php
                        for ($i = 0; $i < 7; $i++) {
                            $date = date('Y-m-d', strtotime("+$i days"));
                            $display_date = date('M d', strtotime("+$i days"));
                            $day_name = date('D', strtotime("+$i days"));
                            $is_today = $i === 0;
                        ?>
                        <label class="date-option">
                            <input type="radio" name="date" value="<?php echo $date; ?>" required>
                            <div class="date-card">
                                <div class="day-name"><?php echo $day_name; ?></div>
                                <div class="date-display"><?php echo $display_date; ?></div>
                                <?php if ($is_today): ?>
                                <div class="today-label">Today</div>
                                <?php endif; ?>
                            </div>
                        </label>
                        <?php } ?>
                    </div>
                </div>

                <!-- Cinema Hall Selection -->
                <div class="card">
                    <h2 class="section-title">
                        <i class="fas fa-building"></i>
                        Select Cinema Hall
                    </h2>
                    <div class="hall-grid">
                        <?php foreach ($film_halls as $id => $hall): ?>
                        <label class="hall-option">
                            <input type="radio" name="hall_id" value="<?php echo $id; ?>" required>
                            <div class="hall-card">
                                <div class="hall-content">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <div class="hall-info">
                                        <div class="hall-name"><?php echo htmlspecialchars($hall['name']); ?></div>
                                        <div class="hall-location"><?php echo htmlspecialchars($hall['location']); ?></div>
                                    </div>
                                </div>
                            </div>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Time Selection -->
                <div class="card">
                    <h2 class="section-title">
                        <i class="fas fa-clock"></i>
                        Select Showtime
                    </h2>
                    <div class="time-grid">
                        <?php foreach ($showtimes as $time): ?>
                        <label class="time-option">
                            <input type="radio" name="time" value="<?php echo $time; ?>" required>
                            <div class="time-card">
                                <div class="time-display"><?php echo $time; ?></div>
                            </div>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Continue Button -->
                <div class="card">
                    <button type="submit" class="continue-button opacity-50 cursor-not-allowed" disabled>
                        <i class="fas fa-couch"></i>
                        Select Seats
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="js/booking.js"></script>

    <?php include 'partial/footer.php'; ?>
</body>
</html>