<?php
include 'auth_check.php';
include 'database/db.php';

// Check if user is admin
checkAdminAuth();

$page_title = "Admin Dashboard - Movie Theater";
include 'partial/header.php';

// Handle undefined session variables safely
$user_display_name = '';
if (isset($_SESSION['user_name'])) {
    $user_display_name = $_SESSION['user_name'];
} elseif (isset($_SESSION['first_name']) && isset($_SESSION['last_name'])) {
    $user_display_name = $_SESSION['first_name'] . ' ' . $_SESSION['last_name'];
} elseif (isset($_SESSION['first_name'])) {
    $user_display_name = $_SESSION['first_name'];
} else {
    $user_display_name = 'Admin User';
}

$user_email = isset($_SESSION['email']) ? $_SESSION['email'] : 'admin@example.com';

// Get dashboard statistics
$stats = [
    'total_movies' => 0,
    'total_users' => 0,
    'total_bookings' => 0,
    'total_revenue' => 0,
    'active_shows' => 0,
    'recent_users' => 0,
    'pending_bookings' => 0
];

try {
    // Get total movies
    $movies_result = $connection->query("SELECT COUNT(*) as count FROM films");
    if ($movies_result) {
        $stats['total_movies'] = $movies_result->fetch_assoc()['count'];
    }
    
    // Get active shows (Now Showing movies)
    $active_shows_result = $connection->query("SELECT COUNT(*) as count FROM films WHERE status = 'Now Showing'");
    if ($active_shows_result) {
        $stats['active_shows'] = $active_shows_result->fetch_assoc()['count'];
    }
    
    // Get total users (excluding admins)
    $users_result = $connection->query("SELECT COUNT(*) as count FROM users WHERE role = 'user'");
    if ($users_result) {
        $stats['total_users'] = $users_result->fetch_assoc()['count'];
    }
    
    // Get recent users (last 30 days)
    $recent_users_result = $connection->query("SELECT COUNT(*) as count FROM users WHERE role = 'user' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
    if ($recent_users_result) {
        $stats['recent_users'] = $recent_users_result->fetch_assoc()['count'];
    }
    
    // Get total bookings
    $bookings_result = $connection->query("SELECT COUNT(*) as count FROM bookings");
    if ($bookings_result) {
        $stats['total_bookings'] = $bookings_result->fetch_assoc()['count'];
    }
    
    // Get pending bookings
    $pending_bookings_result = $connection->query("SELECT COUNT(*) as count FROM bookings WHERE status = 'Pending'");
    if ($pending_bookings_result) {
        $stats['pending_bookings'] = $pending_bookings_result->fetch_assoc()['count'];
    }
    
    // Get total revenue (this month)
    $revenue_result = $connection->query("SELECT SUM(total_amount) as revenue FROM bookings WHERE status = 'Confirmed' AND MONTH(booking_date) = MONTH(NOW()) AND YEAR(booking_date) = YEAR(NOW())");
    if ($revenue_result && $row = $revenue_result->fetch_assoc()) {
        $stats['total_revenue'] = $row['revenue'] ?? 0;
    }
    
    // Get recent bookings
    $recent_bookings = [];
    $recent_bookings_result = $connection->query("SELECT b.*, u.first_name, u.last_name FROM bookings b LEFT JOIN users u ON b.email = u.email ORDER BY b.booking_date DESC LIMIT 5");
    if ($recent_bookings_result) {
        while ($row = $recent_bookings_result->fetch_assoc()) {
            $recent_bookings[] = $row;
        }
    }
    
    // Get popular movies
    $popular_movies = [];
    $popular_movies_result = $connection->query("
        SELECT f.*, COUNT(b.booking_id) as booking_count 
        FROM films f 
        LEFT JOIN bookings b ON f.title = b.movie_title 
        GROUP BY f.id 
        ORDER BY booking_count DESC 
        LIMIT 3
    ");
    if ($popular_movies_result) {
        while ($row = $popular_movies_result->fetch_assoc()) {
            $popular_movies[] = $row;
        }
    }
    
    // Get new users
    $new_users = [];
    $new_users_result = $connection->query("SELECT * FROM users WHERE role = 'user' ORDER BY created_at DESC LIMIT 3");
    if ($new_users_result) {
        while ($row = $new_users_result->fetch_assoc()) {
            $new_users[] = $row;
        }
    }
    
} catch (Exception $e) {
    // Handle error silently
}
?>

<link rel="stylesheet" href="css/admin_dashboard.css">

<div class="container">
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <h1><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h1>
        <p class="dashboard-subtitle">Movie Theater Management System</p>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <a href="admin_movies.php" class="action-btn movies">
            <i class="fas fa-film"></i>
            Manage Movies
        </a>
        <a href="admin_users.php" class="action-btn users">
            <i class="fas fa-users"></i>
            Manage Users
        </a>
        <a href="admin_bookings.php" class="action-btn bookings">
            <i class="fas fa-ticket-alt"></i>
            View Bookings
        </a>
        <a href="admin_reports.php" class="action-btn reports">
            <i class="fas fa-chart-bar"></i>
            Reports
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
                <span class="stat-change">+<?= $stats['recent_users'] ?> Recent</span>
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
                <span class="stat-change">This Month</span>
            </div>
        </div>
    </div>

    <!-- Dashboard Grid -->
    <div class="dashboard-grid">
        <!-- Recent Bookings -->
        <div class="dashboard-section">
            <div class="section-header">
                <h2><i class="fas fa-clock"></i> Recent Bookings</h2>
                <a href="admin_bookings.php" class="view-all">View All</a>
            </div>
            
            <?php if (empty($recent_bookings)): ?>
                <div class="empty-state">
                    <div class="empty-content">
                        <i class="fas fa-ticket-alt"></i>
                        <p>No recent bookings</p>
                    </div>
                </div>
            <?php else: ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Movie</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_bookings as $booking): ?>
                                <tr>
                                    <td>
                                        <div class="customer-info">
                                            <strong><?= htmlspecialchars($booking['full_name'] ?? 'N/A') ?></strong>
                                            <small><?= htmlspecialchars($booking['email']) ?></small>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($booking['movie_title']) ?></td>
                                    <td>$<?= number_format($booking['total_amount'], 2) ?></td>
                                    <td>
                                        <span class="status-badge <?= strtolower($booking['status']) ?>">
                                            <?= htmlspecialchars($booking['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Popular Movies -->
        <div class="dashboard-section">
            <div class="section-header">
                <h2><i class="fas fa-star"></i> Popular Movies</h2>
                <a href="admin_movies.php" class="view-all">Manage Movies</a>
            </div>
            
            <?php if (empty($popular_movies)): ?>
                <div class="empty-state">
                    <div class="empty-content">
                        <i class="fas fa-film"></i>
                        <p>No movies available</p>
                    </div>
                </div>
            <?php else: ?>
                <div class="movies-list">
                    <?php foreach ($popular_movies as $movie): ?>
                        <div class="movie-item">
                            <div class="movie-poster">
                                <?php if (!empty($movie['image']) && file_exists($movie['image'])): ?>
                                    <img src="<?= htmlspecialchars($movie['image']) ?>" alt="<?= htmlspecialchars($movie['title']) ?>">
                                <?php else: ?>
                                    <div class="no-poster">
                                        <i class="fas fa-film"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="movie-info">
                                <h4><?= htmlspecialchars($movie['title']) ?></h4>
                                <p class="movie-type"><?= htmlspecialchars($movie['category']) ?> • $<?= htmlspecialchars($movie['rating'] ?? '0.0') ?></p>
                                <p class="movie-bookings"><?= $movie['booking_count'] ?> bookings</p>
                                <p><?= date("M d, Y", strtotime($movie['release_date'])) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- New Users -->
        <div class="dashboard-section">
            <div class="section-header">
                <h2><i class="fas fa-user-plus"></i> New Users</h2>
                <a href="admin_users.php" class="view-all">Manage Users</a>
            </div>
            
            <?php if (empty($new_users)): ?>
                <div class="empty-state">
                    <div class="empty-content">
                        <i class="fas fa-users"></i>
                        <p>No new users</p>
                    </div>
                </div>
            <?php else: ?>
                <div class="users-list">
                    <?php foreach ($new_users as $user): ?>
                        <div class="user-item">
                            <div class="user-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="user-info">
                                <h4><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h4>
                                <p class="user-email"><?= htmlspecialchars($user['email']) ?></p>
                                <p class="user-role"><?= htmlspecialchars($user['role']) ?></p>
                                <p>Joined <?= date("M d, Y", strtotime($user['created_at'])) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    // Animation delays for stat cards
    document.addEventListener('DOMContentLoaded', function() {
        const statCards = document.querySelectorAll('.stat-card');
        const dashboardSections = document.querySelectorAll('.dashboard-section');
        
        statCards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.1}s`;
        });
        
        dashboardSections.forEach((section, index) => {
            section.style.animationDelay = `${(index + 4) * 0.1}s`;
        });
    });
</script>

<?php include 'partial/footer.php'; ?>