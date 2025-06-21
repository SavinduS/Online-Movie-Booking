<?php
$conn = new mysqli("localhost", "root", "", "movie_booking");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, name, email, password FROM users ORDER BY id ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Users</title>
  <link rel="stylesheet" href="css/wish_css_users.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body id="wish-body">

  <div id="wish-wrapper">
    
    <!-- Header -->
    <header id="wish-header">
      <h1><i class="fas fa-users"></i> All Users</h1>
    </header>

    <!-- User Table -->
    <section id="wish-user-section">
      <table id="wish-user-table">
        <thead>
          <tr>
            <th>User ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Registered On</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= isset($row['created_at']) ? date("Y-m-d", strtotime($row['created_at'])) : "N/A" ?></td>
                <td>
                  <a href="delete_user.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure?')">
                    <i class="fas fa-trash-alt" style="color: red;"></i>
                  </a>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="5">No users found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </section>
  </div>

</body>
</html>
