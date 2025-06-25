<?php
session_start();

// Sample data - replace with your database queries
$movie_id = $_GET['movie_id'] ?? 1;
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
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Tickets - <?php echo htmlspecialchars($movie_title); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#b793d2'
                    },
                    fontFamily: {
                        'poppins': ['Poppins', 'sans-serif']
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 font-poppins">
    <div class="min-h-screen py-8">
        <div class="max-w-4xl mx-auto px-4">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <div class="flex items-center gap-4">
                    <button onclick="history.back()" class="text-primary hover:text-purple-700 transition-colors">
                        <i class="fas fa-arrow-left text-xl"></i>
                    </button>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800"><?php echo htmlspecialchars($movie_title); ?></h1>
                        <p class="text-gray-600 flex items-center gap-2">
                            <i class="fas fa-film text-primary"></i>
                            Select your preferred showtime and cinema
                        </p>
                    </div>
                </div>
            </div>

            <form method="POST" class="space-y-6">
                <!-- Date Selection -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-calendar-alt text-primary"></i>
                        Select Date
                    </h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3">
                        <?php
                        for ($i = 0; $i < 7; $i++) {
                            $date = date('Y-m-d', strtotime("+$i days"));
                            $display_date = date('M d', strtotime("+$i days"));
                            $day_name = date('D', strtotime("+$i days"));
                            $is_today = $i === 0;
                        ?>
                        <label class="cursor-pointer">
                            <input type="radio" name="date" value="<?php echo $date; ?>" class="hidden peer" required>
                            <div class="p-3 border-2 border-gray-200 rounded-lg text-center hover:border-primary peer-checked:border-primary peer-checked:bg-primary peer-checked:text-white transition-all">
                                <div class="text-sm font-medium"><?php echo $day_name; ?></div>
                                <div class="text-xs"><?php echo $display_date; ?></div>
                                <?php if ($is_today): ?>
                                <div class="text-xs text-primary peer-checked:text-white">Today</div>
                                <?php endif; ?>
                            </div>
                        </label>
                        <?php } ?>
                    </div>
                </div>

                <!-- Cinema Hall Selection -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-building text-primary"></i>
                        Select Cinema Hall
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php foreach ($film_halls as $id => $hall): ?>
                        <label class="cursor-pointer">
                            <input type="radio" name="hall_id" value="<?php echo $id; ?>" class="hidden peer" required>
                            <div class="p-4 border-2 border-gray-200 rounded-lg hover:border-primary peer-checked:border-primary peer-checked:bg-primary peer-checked:text-white transition-all">
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-map-marker-alt text-lg"></i>
                                    <div>
                                        <div class="font-semibold"><?php echo htmlspecialchars($hall['name']); ?></div>
                                        <div class="text-sm opacity-75"><?php echo htmlspecialchars($hall['location']); ?></div>
                                    </div>
                                </div>
                            </div>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                

                <!-- Time Selection -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-clock text-primary"></i>
                        Select Showtime
                    </h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
                        <?php foreach ($showtimes as $time): ?>
                        <label class="cursor-pointer">
                            <input type="radio" name="time" value="<?php echo $time; ?>" class="hidden peer" required>
                            <div class="p-3 border-2 border-gray-200 rounded-lg text-center hover:border-primary peer-checked:border-primary peer-checked:bg-primary peer-checked:text-white transition-all">
                                <div class="font-semibold"><?php echo $time; ?></div>
                            </div>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Continue Button -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <button type="submit" class="w-full bg-primary hover:bg-purple-700 text-white font-semibold py-4 px-6 rounded-lg transition-colors flex items-center justify-center gap-2">
                        <i class="fas fa-couch"></i>
                        Select Seats
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Add some interactivity
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const submitBtn = form.querySelector('button[type="submit"]');
            
            form.addEventListener('change', function() {
                const formData = new FormData(form);
                const isComplete = formData.get('date') && formData.get('hall_id')&& formData.get('time');
                
                if (isComplete) {
                    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    submitBtn.disabled = false;
                } else {
                    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    submitBtn.disabled = true;
                }
            });
            
            // Initially disable button
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            submitBtn.disabled = true;
        });
    </script>
</body>
</html>