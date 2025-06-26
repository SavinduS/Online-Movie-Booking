<?php
include 'database/db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $stmt = $connection->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "User deleted.";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Delete failed.";
        $_SESSION['message_type'] = "error";
    }

    $stmt->close();
}

header("Location: admin_users.php");
exit();
?>
