<?php
include 'database/db.php';
session_start();

// Handle message
$message = $_SESSION['message'] ?? '';
$messageType = $_SESSION['message_type'] ?? '';
unset($_SESSION['message'], $_SESSION['message_type']);

// Fetch dashboard statistics
$stats = [
    'total_movies' => 0,
    'total_users' => 0,
    'total_bookings' => 0,
    'total_revenue' => 0,
    'active_shows' => 0,
    'pending_bookings' => 0
];

// Get total movies (from films table)
$result = $connection->query("SELECT COUNT(*) as count FROM films");
if ($result) {
    $stats['total_movies'] = $result->fetch_assoc()['count'];
}

// Get total users
$result = $connection->query("SELECT COUNT(*) as count FROM users");
if ($result) {
    $stats['total_users'] = $result->fetch_assoc()['count'];
}

// Get total bookings
$result = $connection->query("SELECT COUNT(*) as count FROM bookings");
if ($result) {
    $stats['total_bookings'] = $result->fetch_assoc()['count'];
}

// Get total revenue from confirmed bookings
$result = $connection->query("SELECT SUM(total_amount) as revenue FROM bookings WHERE status = 'Confirmed'");
if ($result && $row = $result->fetch_assoc()) {
    $stats['total_revenue'] = $row['revenue'] ?? 0;
}

// Get active shows (films with status 'Now Showing' or 'Trending')
$result = $connection->query("SELECT COUNT(*) as count FROM films WHERE status IN ('Now Showing', 'Trending')");
if ($result) {
    $stats['active_shows'] = $result->fetch_assoc()['count'];
}

// Get pending bookings
$result = $connection->query("SELECT COUNT(*) as count FROM bookings WHERE status = 'Pending'");
if ($result) {
    $stats['pending_bookings'] = $result->fetch_assoc()['count'];
}

// Get recent activities (last 5 bookings)
$recent_bookings = [];
$sql = "SELECT b.*, u.first_name, u.last_name, u.email 
        FROM bookings b 
        LEFT JOIN users u ON b.email = u.email 
        ORDER BY b.booking_date DESC 
        LIMIT 5";
$result = $connection->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $recent_bookings[] = $row;
    }
}

// Get recent users (last 5 registered)
$recent_users = [];
$sql = "SELECT * FROM users ORDER BY created_at DESC LIMIT 5";
$result = $connection->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $recent_users[] = $row;
    }
}

// Get popular movies (most booked from films table)
$popular_movies = [];
$sql = "SELECT f.*, COUNT(b.booking_id) as booking_count 
        FROM films f 
        LEFT JOIN bookings b ON f.title = b.movie_title 
        GROUP BY f.id 
        ORDER BY booking_count DESC 
        LIMIT 5";
$result = $connection->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $popular_movies[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Movie Theater Management</title>
    <link rel="stylesheet" href="css/admin_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="dashboard-header">
            <h1>🎬 Admin Dashboard</h1>
            <p class="dashboard-subtitle">Movie Theater Management System</p>
        </div>

        <!-- Alert Messages -->
        <?php if (!empty($message)): ?>
            <div class="alert <?= $messageType ?>"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <a href="admin_movies.php" class="action-btn movies">
                <i class="fas fa-film"></i>
                <span>Manage Movies</span>
            </a>
            <a href="admin_users.php" class="action-btn users">
                <i class="fas fa-users"></i>
                <span>Manage Users</span>
            </a>
            <a href="admin_bookings.php" class="action-btn bookings">
                <i class="fas fa-ticket-alt"></i>
                <span>View Bookings</span>
            </a>
            <a href="admin_reports.php" class="action-btn reports">
                <i class="fas fa-chart-bar"></i>
                <span>Reports</span>
            </a>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card movies">
                <div class="stat-icon">
                    <i class="fas fa-film"></i>
                </div>
                <div class="stat-info">
                    <h3><?= $stats['total_movies'] ?></h3>
                    <p>Total Movies</p>
                    <span class="stat-change">+<?= $stats['active_shows'] ?> Active Shows</span>
                </div>
            </div>

            <div class="stat-card users">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3><?= $stats['total_users'] ?></h3>
                    <p>Registered Users</p>
                    <span class="stat-change">+<?= count($recent_users) ?> Recent</span>
                </div>
            </div>

            <div class="stat-card bookings">
                <div class="stat-icon">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <div class="stat-info">
                    <h3><?= $stats['total_bookings'] ?></h3>
                    <p>Total Bookings</p>
                    <span class="stat-change pending"><?= $stats['pending_bookings'] ?> Pending</span>
                </div>
            </div>

            <div class="stat-card revenue">
                <div class="stat-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-info">
                    <h3>$<?= number_format($stats['total_revenue'], 2) ?></h3>
                    <p>Total Revenue</p>
                    <span class="stat-change">From Confirmed Bookings</span>
                </div>
            </div>
        </div>

        <!-- Dashboard Content Grid -->
        <div class="dashboard-grid">
            <!-- Recent Bookings -->
            <div class="dashboard-section">
                <div class="section-header">
                    <h2><i class="fas fa-clock"></i> Recent Bookings</h2>
                    <a href="admin_bookings.php" class="view-all">View All</a>
                </div>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Movie</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recent_bookings)): ?>
                                <tr>
                                    <td colspan="5" class="empty-state">
                                        <div class="empty-content">
                                            <i class="fas fa-ticket-alt"></i>
                                            <p>No recent bookings found</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recent_bookings as $booking): ?>
                                    <tr>
                                        <td>
                                            <div class="customer-info">
                                                <strong><?= htmlspecialchars($booking['full_name']) ?></strong>
                                                <small><?= htmlspecialchars($booking['email']) ?></small>
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($booking['movie_title']) ?></td>
                                        <td><strong>$<?= number_format($booking['total_amount'], 2) ?></strong></td>
                                        <td>
                                            <span class="status-badge <?= strtolower($booking['status']) ?>">
                                                <?= ucfirst($booking['status']) ?>
                                            </span>
                                        </td>
                                        <td><?= date("M d, Y, g:i A", strtotime($booking['booking_date'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Popular Movies -->
            <div class="dashboard-section">
                <div class="section-header">
                    <h2><i class="fas fa-star"></i> Popular Movies</h2>
                    <a href="admin_movies.php" class="view-all">Manage Movies</a>
                </div>
                <div class="movies-list">
                    <?php if (empty($popular_movies)): ?>
                        <div class="empty-state">
                            <div class="empty-content">
                                <i class="fas fa-film"></i>
                                <p>No movies found</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($popular_movies as $movie): ?>
                            <div class="movie-item">
                                <div class="movie-poster">
                                    <?php if (!empty($movie['image']) && file_exists($movie['image'])): ?>
                                        <img src="<?= $movie['image'] ?>" alt="<?= htmlspecialchars($movie['title']) ?>">
                                    <?php else: ?>
                                        <div class="no-poster"><i class="fas fa-film"></i></div>
                                    <?php endif; ?>
                                </div>
                                <div class="movie-info">
                                    <h4><?= htmlspecialchars($movie['title']) ?></h4>
                                    <p class="movie-type"><?= htmlspecialchars($movie['category']) ?> • Rating: <?= $movie['rating'] ?></p>
                                    <p class="movie-bookings"><?= $movie['booking_count'] ?> bookings</p>
                                    <p class="movie-status"><?= htmlspecialchars($movie['status']) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Users -->
            <div class="dashboard-section">
                <div class="section-header">
                    <h2><i class="fas fa-user-plus"></i> New Users</h2>
                    <a href="admin_users.php" class="view-all">Manage Users</a>
                </div>
                <div class="users-list">
                    <?php if (empty($recent_users)): ?>
                        <div class="empty-state">
                            <div class="empty-content">
                                <i class="fas fa-users"></i>
                                <p>No recent users found</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($recent_users as $user): ?>
                            <div class="user-item">
                                <div class="user-avatar">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="user-info">
                                    <h4><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h4>
                                    <p class="user-email"><?= htmlspecialchars($user['email']) ?></p>
                                    <p class="user-role"><?= htmlspecialchars($user['role']) ?></p>
                                    <p class="user-date">Joined <?= date("M d, Y", strtotime($user['created_at'])) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- System Status -->
            <div class="dashboard-section system-status">
                <div class="section-header">
                    <h2><i class="fas fa-cogs"></i> System Status</h2>
                </div>
                <div class="status-grid">
                    <div class="status-item">
                        <div class="status-icon online">
                            <i class="fas fa-database"></i>
                        </div>
                        <div class="status-info">
                            <h4>Database</h4>
                            <p class="status-text online">Online</p>
                        </div>
                    </div>
                    <div class="status-item">
                        <div class="status-icon online">
                            <i class="fas fa-server"></i>
                        </div>
                        <div class="status-info">
                            <h4>Server</h4>
                            <p class="status-text online">Running</p>
                        </div>
                    </div>
                    <div class="status-item">
                        <div class="status-icon online">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="status-info">
                            <h4>Security</h4>
                            <p class="status-text online">Secure</p>
                        </div>
                    </div>
                    <div class="status-item">
                        <div class="status-icon warning">
                            <i class="fas fa-hdd"></i>
                        </div>
                        <div class="status-info">
                            <h4>Storage</h4>
                            <p class="status-text warning">75% Used</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Dashboard JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            // Animate statistics cards
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
            });

            // Animate dashboard sections
            const sections = document.querySelectorAll('.dashboard-section');
            sections.forEach((section, index) => {
                section.style.animationDelay = `${index * 0.15}s`;
            });

            // Auto-refresh dashboard every 5 minutes
            setInterval(function() {
                // You can add AJAX calls here to refresh specific sections
                console.log('Dashboard refreshed at:', new Date().toLocaleTimeString());
            }, 300000);

            // Add click events to action buttons
            const actionBtns = document.querySelectorAll('.action-btn');
            actionBtns.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    this.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        this.style.transform = '';
                    }, 150);
                });
            });
        });

        // Real-time clock
        function updateClock() {
            const now = new Date();
            const timeString = now.toLocaleTimeString();
            const dateString = now.toLocaleDateString();
            
            // You can add a clock element to display current time
            console.log(`${dateString} ${timeString}`);
        }

        setInterval(updateClock, 1000);
    </script>
</body>
</html>