<?php
include 'auth_check.php';
include 'database/db.php';


checkAdminAuth();


// Handle message
$message = $_SESSION['message'] ?? '';
$messageType = $_SESSION['message_type'] ?? '';
unset($_SESSION['message'], $_SESSION['message_type']);

// Handle booking actions
if ($_POST) {
    if (isset($_POST['action']) && isset($_POST['booking_id'])) {
        $booking_id = $_POST['booking_id'];
        $action = $_POST['action'];
        
        switch ($action) {
            case 'confirm':
                $sql = "UPDATE bookings SET status = 'Confirmed' WHERE booking_id = ?";
                $stmt = $connection->prepare($sql);
                $stmt->bind_param("s", $booking_id);
                if ($stmt->execute()) {
                    $_SESSION['message'] = "Booking confirmed successfully!";
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['message'] = "Error confirming booking!";
                    $_SESSION['message_type'] = "error";
                }
                break;
                
            case 'cancel':
                $sql = "UPDATE bookings SET status = 'Cancelled' WHERE booking_id = ?";
                $stmt = $connection->prepare($sql);
                $stmt->bind_param("s", $booking_id);
                if ($stmt->execute()) {
                    $_SESSION['message'] = "Booking cancelled successfully!";
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['message'] = "Error cancelling booking!";
                    $_SESSION['message_type'] = "error";
                }
                break;
                
            case 'delete':
                $sql = "DELETE FROM bookings WHERE booking_id = ?";
                $stmt = $connection->prepare($sql);
                $stmt->bind_param("s", $booking_id);
                if ($stmt->execute()) {
                    $_SESSION['message'] = "Booking deleted successfully!";
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['message'] = "Error deleting booking!";
                    $_SESSION['message_type'] = "error";
                }
                break;
        }
        
        header("Location: admin_bookings.php");
        exit();
    }
}

// Pagination and filtering
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? '';
$date_filter = $_GET['date'] ?? '';

// Build WHERE clause
$where_conditions = [];
$params = [];
$param_types = '';

if (!empty($search)) {
    $where_conditions[] = "(b.full_name LIKE ? OR b.email LIKE ? OR b.movie_title LIKE ? OR b.booking_id LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param, $search_param, $search_param]);
    $param_types .= 'ssss';
}

if (!empty($status_filter)) {
    $where_conditions[] = "b.status = ?";
    $params[] = $status_filter;
    $param_types .= 's';
}

if (!empty($date_filter)) {
    $where_conditions[] = "DATE(b.booking_date) = ?";
    $params[] = $date_filter;
    $param_types .= 's';
}

$where_clause = '';
if (!empty($where_conditions)) {
    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
}

// Get total count for pagination
$count_sql = "SELECT COUNT(*) as total FROM bookings b $where_clause";

$count_stmt = $connection->prepare($count_sql);
if (!empty($params)) {
    $count_stmt->bind_param($param_types, ...$params);
}
$count_stmt->execute();
$total_records = $count_stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_records / $per_page);

// Get bookings with pagination
$sql = "SELECT b.* FROM bookings b $where_clause ORDER BY b.booking_date DESC LIMIT ? OFFSET ?";

// Add LIMIT and OFFSET parameters
$params[] = $per_page;
$params[] = $offset;
$param_types .= 'ii';

$stmt = $connection->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($param_types, ...$params);
}
$stmt->execute();
$bookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get booking statistics
$stats = [
    'total_bookings' => 0,
    'confirmed_bookings' => 0,
    'pending_bookings' => 0,
    'cancelled_bookings' => 0,
    'total_revenue' => 0,
    'today_bookings' => 0
];

// Get statistics
$stats_queries = [
    'total_bookings' => "SELECT COUNT(*) as count FROM bookings",
    'confirmed_bookings' => "SELECT COUNT(*) as count FROM bookings WHERE status = 'Confirmed'",
    'pending_bookings' => "SELECT COUNT(*) as count FROM bookings WHERE status = 'Pending'",
    'cancelled_bookings' => "SELECT COUNT(*) as count FROM bookings WHERE status = 'Cancelled'",
    'today_bookings' => "SELECT COUNT(*) as count FROM bookings WHERE DATE(booking_date) = CURDATE()"
];

foreach ($stats_queries as $key => $query) {
    $result = $connection->query($query);
    if ($result) {
        $stats[$key] = $result->fetch_assoc()['count'];
    }
}

// Get total revenue
$revenue_result = $connection->query("SELECT SUM(total_amount) as revenue FROM bookings WHERE status = 'Confirmed'");
if ($revenue_result && $row = $revenue_result->fetch_assoc()) {
    $stats['total_revenue'] = $row['revenue'] ?? 0;
}
?>

<?php
$page_title = "Manage Bookings - Movie Theater Management";
include 'partial/header.php';
?>
<link rel="stylesheet" href="css/admin_bookings.css">
    <div class="container">
        <!-- Header -->
        <div class="page-header">
            <div class="header-content">
                <div class="header-left">
                    <a href="admin_dashboard.php" class="back-btn">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div class="header-text">
                        <h1>🎫 Manage Bookings</h1>
                        <p class="page-subtitle">View and manage all movie bookings</p>
                    </div>
                </div>
                <div class="header-actions">
                    <button class="btn btn-primary" onclick="window.print()">
                        <i class="fas fa-print"></i>
                        Print Report
                    </button>
                    <button class="btn btn-secondary" onclick="exportBookings()">
                        <i class="fas fa-download"></i>
                        Export CSV
                    </button>
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
        <?php if (!empty($message)): ?>
            <div class="alert <?= $messageType ?>"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card total">
                <div class="stat-icon">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <div class="stat-info">
                    <h3><?= $stats['total_bookings'] ?></h3>
                    <p>Total Bookings</p>
                    <span class="stat-change">+<?= $stats['today_bookings'] ?> Today</span>
                </div>
            </div>

            <div class="stat-card confirmed">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <h3><?= $stats['confirmed_bookings'] ?></h3>
                    <p>Confirmed</p>
                    <span class="stat-change"><?= $stats['total_bookings'] > 0 ? round(($stats['confirmed_bookings'] / $stats['total_bookings']) * 100) : 0 ?>% of total</span>
                </div>
            </div>

            <div class="stat-card pending">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <h3><?= $stats['pending_bookings'] ?></h3>
                    <p>Pending</p>
                    <span class="stat-change">Needs Action</span>
                </div>
            </div>

            <div class="stat-card revenue">
                <div class="stat-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-info">
                    <h3>$<?= number_format($stats['total_revenue'], 2) ?></h3>
                    <p>Total Revenue</p>
                    <span class="stat-change">Confirmed Only</span>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="filters-section">
            <form method="GET" class="filters-form">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" placeholder="Search bookings..." value="<?= htmlspecialchars($search) ?>">
                </div>
                
                <div class="filter-group">
                    <select name="status">
                        <option value="">All Status</option>
                        <option value="Pending" <?= $status_filter === 'Pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="Confirmed" <?= $status_filter === 'Confirmed' ? 'selected' : '' ?>>Confirmed</option>
                        <option value="Cancelled" <?= $status_filter === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <input type="date" name="date" value="<?= htmlspecialchars($date_filter) ?>">
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i>
                    Filter
                </button>
                
                <a href="admin_bookings.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Clear
                </a>
            </form>
        </div>

        <!-- Bookings Table -->
        <div class="table-section">
            <div class="table-header">
                <h2><i class="fas fa-list"></i> Bookings List</h2>
                <div class="table-info">
                    Showing <?= $offset + 1 ?>-<?= min($offset + $per_page, $total_records) ?> of <?= $total_records ?> bookings
                </div>
            </div>
            
            <div class="table-container">
                <table class="bookings-table">
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Customer</th>
                            <th>Movie</th>
                            <th>Show Details</th>
                            <th>Seats</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Booking Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($bookings)): ?>
                            <tr>
                                <td colspan="9" class="empty-state">
                                    <div class="empty-content">
                                        <i class="fas fa-ticket-alt"></i>
                                        <h3>No bookings found</h3>
                                        <p>No bookings match your current filters</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($bookings as $booking): ?>
                                <tr>
                                    <td>
                                        <span class="booking-id"><?= htmlspecialchars($booking['booking_id']) ?></span>
                                    </td>
                                    <td>
                                        <div class="customer-info">
                                            <strong><?= htmlspecialchars($booking['full_name']) ?></strong>
                                            <small><?= htmlspecialchars($booking['email']) ?></small>
                                            <small><?= htmlspecialchars($booking['phone']) ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="movie-info">
                                            <strong><?= htmlspecialchars($booking['movie_title']) ?></strong>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="show-info">
                                            <span class="show-date"><?= date("M d, Y", strtotime($booking['show_date'])) ?></span>
                                            <span class="show-time"><?= date("g:i A", strtotime($booking['show_time'])) ?></span>
                                            <span class="hall-name"><?= htmlspecialchars($booking['hall_name']) ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="seats-info">
                                            <span class="seat-count"><?= $booking['number_of_tickets'] ?> tickets</span>
                                            <small><?= htmlspecialchars($booking['selected_seats']) ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <strong class="amount">$<?= number_format($booking['total_amount'], 2) ?></strong>
                                        <small><?= htmlspecialchars($booking['payment_method']) ?></small>
                                    </td>
                                    <td>
                                        <span class="status-badge <?= strtolower($booking['status']) ?>">
                                            <?= ucfirst($booking['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="booking-date"><?= date("M d, Y", strtotime($booking['booking_date'])) ?></span>
                                        <small><?= date("g:i A", strtotime($booking['booking_date'])) ?></small>
                                    </td>
                                    <td>
                                        <div class="actions">
                                            <?php if ($booking['status'] === 'Pending'): ?>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="booking_id" value="<?= $booking['booking_id'] ?>">
                                                    <input type="hidden" name="action" value="confirm">
                                                    <button type="submit" class="btn-action confirm" title="Confirm Booking">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="booking_id" value="<?= $booking['booking_id'] ?>">
                                                    <input type="hidden" name="action" value="cancel">
                                                    <button type="submit" class="btn-action cancel" title="Cancel Booking" onclick="return confirm('Are you sure you want to cancel this booking?')">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            
                                            <button class="btn-action view" onclick="viewBookingDetails('<?= $booking['booking_id'] ?>')" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="booking_id" value="<?= $booking['booking_id'] ?>">
                                                <input type="hidden" name="action" value="delete">
                                                <button type="submit" class="btn-action delete" title="Delete Booking" onclick="return confirm('Are you sure you want to delete this booking? This action cannot be undone.')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status_filter) ?>&date=<?= urlencode($date_filter) ?>" class="page-btn">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                <?php endif; ?>
                
                <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                    <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status_filter) ?>&date=<?= urlencode($date_filter) ?>" 
                       class="page-btn <?= $i === $page ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
                
                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status_filter) ?>&date=<?= urlencode($date_filter) ?>" class="page-btn">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Booking Details Modal -->
    <div id="bookingModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Booking Details</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body" id="bookingDetails">
                <!-- Booking details will be loaded here -->
            </div>
        </div>
    </div>

    <script>
        // Modal functions
        function viewBookingDetails(bookingId) {
            // Here you would typically make an AJAX call to get booking details
            // For now, showing a simple message
            document.getElementById('bookingDetails').innerHTML = `
                <div style="text-align: center; padding: 20px;">
                    <h3>Booking ID: ${bookingId}</h3>
                    <p>Detailed booking information would be displayed here.</p>
                    <p>This would typically be loaded via AJAX from the server.</p>
                </div>
            `;
            document.getElementById('bookingModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('bookingModal').style.display = 'none';
        }

        function exportBookings() {
            // Simple CSV export functionality
            let csv = 'Booking ID,Customer,Email,Phone,Movie,Hall,Show Date,Show Time,Seats,Tickets,Amount,Payment Method,Status,Booking Date\n';
            
            // Get current table data
            const rows = document.querySelectorAll('.bookings-table tbody tr');
            rows.forEach(row => {
                if (!row.querySelector('.empty-state')) {
                    const cells = row.querySelectorAll('td');
                    if (cells.length > 0) {
                        // Extract data from each cell - this is a simplified version
                        const bookingId = cells[0].textContent.trim();
                        const customer = cells[1].querySelector('strong').textContent;
                        const email = cells[1].querySelectorAll('small')[0].textContent;
                        const phone = cells[1].querySelectorAll('small')[1].textContent;
                        const movie = cells[2].textContent.trim();
                        const amount = cells[5].querySelector('.amount').textContent;
                        const status = cells[6].textContent.trim();
                        const bookingDate = cells[7].querySelector('.booking-date').textContent;
                        
                        csv += `"${bookingId}","${customer}","${email}","${phone}","${movie}","","","","","","${amount}","","${status}","${bookingDate}"\n`;
                    }
                }
            });
            
            // Download CSV
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'bookings_export.csv';
            a.click();
            window.URL.revokeObjectURL(url);
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('bookingModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }

        // Animation delays for stat cards
        document.addEventListener('DOMContentLoaded', function() {
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
            });
        });
    </script>

<?php include 'partial/footer.php'; ?>