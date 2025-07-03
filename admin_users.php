<?php
include 'database/db.php';
session_start();

// Handle message
$message = $_SESSION['message'] ?? '';
$messageType = $_SESSION['message_type'] ?? '';
unset($_SESSION['message'], $_SESSION['message_type']);

// Fetch users
$users = [];
$sql = "SELECT * FROM users ORDER BY id DESC";
$result = $connection->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/png" href="images/favicon.png">
  <title>Admin | Manage Users</title>
  <link rel="stylesheet" href="css/admin_users.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <div class="container">
      <a href="admin_dashboard.php" class="savi-back-btn">
  <i class="fas fa-arrow-left"></i> Back to Dashboard
</a>

    <h1>👥 User Management</h1>
    
    <?php if (!empty($message)): ?>
      <div class="alert <?= $messageType ?>"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    
    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Role</th>
            <th>Registered At</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($users)): ?>
            <tr>
              <td colspan="7" class="empty-state">
                <h3>No Users Found</h3>
                <p>There are no registered users in the system.</p>
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($users as $user): ?>
              <tr>
                <td>#<?= $user['id'] ?></td>
                <td><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= htmlspecialchars($user['phone']) ?></td>
                <td data-role="<?= strtolower($user['role']) ?>"><?= htmlspecialchars($user['role']) ?></td>
                <td><?= date("M d, Y", strtotime($user['created_at'])) ?></td>
                <td>
                  <a href="admin_delete_user.php?id=<?= $user['id'] ?>" 
                     onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')" 
                     class="delete-btn"
                     title="Delete User">
                    <i class="fas fa-trash"></i>
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
    
    <!-- User Stats Summary -->
    <div class="stats-summary">
      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-users"></i>
        </div>
        <div class="stat-info">
          <h3><?= count($users) ?></h3>
          <p>Total Users</p>
        </div>
      </div>
      
      <div class="stat-card">
        <div class="stat-icon admin">
          <i class="fas fa-user-shield"></i>
        </div>
        <div class="stat-info">
          <h3><?= count(array_filter($users, function($u) { return strtolower($u['role']) === 'admin'; })) ?></h3>
          <p>Administrators</p>
        </div>
      </div>
      
      <div class="stat-card">
        <div class="stat-icon user">
          <i class="fas fa-user"></i>
        </div>
        <div class="stat-info">
          <h3><?= count(array_filter($users, function($u) { return strtolower($u['role']) === 'user'; })) ?></h3>
          <p>Regular Users</p>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Add smooth animations
    document.addEventListener('DOMContentLoaded', function() {
      // Animate table rows
      const rows = document.querySelectorAll('tbody tr');
      rows.forEach((row, index) => {
        row.style.animationDelay = `${index * 0.1}s`;
      });
      
      // Enhanced delete confirmation
      const deleteButtons = document.querySelectorAll('.delete-btn');
      deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
          const userRow = this.closest('tr');
          const userName = userRow.cells[1].textContent;
          const userEmail = userRow.cells[2].textContent;
          
          const confirmed = confirm(
            `⚠️ Delete User Confirmation\n\n` +
            `Name: ${userName}\n` +
            `Email: ${userEmail}\n\n` +
            `This action cannot be undone. Are you sure?`
          );
          
          if (!confirmed) {
            e.preventDefault();
          }
        });
      });
    });
  </script>
</body>
</html>