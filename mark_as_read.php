<?php
session_start();
include 'admin/db_connect.php';

if (!isset($_SESSION['login_user_id']) || !isset($_GET['id'])) {
    header('Location: login.php');
    exit();
}

$notificationId = $_GET['id'];
$userId = $_SESSION['login_user_id'];

// Update the notification to mark it as read
$query = "UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $notificationId, $userId);

if ($stmt->execute()) {
    // Redirect back to the notifications page after marking as read
    header('Location: notifications.php');
} else {
    echo "Error marking notification as read.";
}

$stmt->close();
$conn->close();
?>
