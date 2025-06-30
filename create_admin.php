<?php
include 'database/db.php';

// Admin user details
$first_name = 'Admin';
$last_name = 'User';
$email = 'admin@movietheater.com';
$phone = '0701234567';
$password = 'admin123';
$role = 'admin';

// Hash the password properly
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

echo "<h2>Creating Admin User...</h2>";
echo "<p><strong>Email:</strong> $email</p>";
echo "<p><strong>Password:</strong> $password</p>";
echo "<p><strong>Hashed Password:</strong> $hashed_password</p>";

try {
    // Check if admin already exists
    $check_sql = "SELECT id, email FROM users WHERE email = ?";
    $check_stmt = $connection->prepare($check_sql);
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo "<p style='color: orange;'>⚠️ Admin user already exists! Updating password...</p>";
        
        // Update existing user
        $update_sql = "UPDATE users SET password = ?, role = ? WHERE email = ?";
        $update_stmt = $connection->prepare($update_sql);
        $update_stmt->bind_param("sss", $hashed_password, $role, $email);
        
        if ($update_stmt->execute()) {
            echo "<p style='color: green;'>✅ Admin user updated successfully!</p>";
        } else {
            echo "<p style='color: red;'>❌ Error updating admin user: " . $update_stmt->error . "</p>";
        }
        $update_stmt->close();
    } else {
        echo "<p style='color: blue;'>ℹ️ Creating new admin user...</p>";
        
        // Insert new admin user
        $insert_sql = "INSERT INTO users (first_name, last_name, email, phone, password, role, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())";
        $insert_stmt = $connection->prepare($insert_sql);
        $insert_stmt->bind_param("ssssss", $first_name, $last_name, $email, $phone, $hashed_password, $role);
        
        if ($insert_stmt->execute()) {
            echo "<p style='color: green;'>✅ Admin user created successfully!</p>";
        } else {
            echo "<p style='color: red;'>❌ Error creating admin user: " . $insert_stmt->error . "</p>";
        }
        $insert_stmt->close();
    }
    
    $check_stmt->close();
    
    // Verify the user was created/updated
    echo "<h3>Verification:</h3>";
    $verify_sql = "SELECT id, first_name, last_name, email, role, created_at FROM users WHERE email = ?";
    $verify_stmt = $connection->prepare($verify_sql);
    $verify_stmt->bind_param("s", $email);
    $verify_stmt->execute();
    $verify_result = $verify_stmt->get_result();
    
    if ($verify_row = $verify_result->fetch_assoc()) {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th style='padding: 5px;'>ID</th><th style='padding: 5px;'>Name</th><th style='padding: 5px;'>Email</th><th style='padding: 5px;'>Role</th><th style='padding: 5px;'>Created</th></tr>";
        echo "<tr>";
        echo "<td style='padding: 5px;'>" . $verify_row['id'] . "</td>";
        echo "<td style='padding: 5px;'>" . $verify_row['first_name'] . " " . $verify_row['last_name'] . "</td>";
        echo "<td style='padding: 5px;'>" . $verify_row['email'] . "</td>";
        echo "<td style='padding: 5px;'>" . $verify_row['role'] . "</td>";
        echo "<td style='padding: 5px;'>" . $verify_row['created_at'] . "</td>";
        echo "</tr>";
        echo "</table>";
        
        // Test password verification
        echo "<h3>Password Test:</h3>";
        if (password_verify($password, $hashed_password)) {
            echo "<p style='color: green;'>✅ Password verification works correctly!</p>";
        } else {
            echo "<p style='color: red;'>❌ Password verification failed!</p>";
        }
    }
    
    $verify_stmt->close();
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><strong>Now you can login with:</strong></p>";
echo "<p>Email: <code>admin@movietheater.com</code></p>";
echo "<p>Password: <code>admin123</code></p>";
echo "<p><a href='login_index.php'>Go to Login Page</a></p>";
echo "<p style='color: red; font-weight: bold;'>⚠️ DELETE THIS FILE AFTER RUNNING IT!</p>";
?>