<?php
session_start();
include 'admin/db_connect.php';

if (!isset($_SESSION['login_user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['login_user_id'];

// Fetch unread notifications for the user, including order confirmations or admin replies
$query = "
    SELECT n.message, n.created_at, n.type, o.delivery_status
    FROM notifications n
    LEFT JOIN orders o ON n.order_id = o.id
    WHERE n.user_id = ? AND n.is_read = 0 
    ORDER BY n.created_at DESC
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Notifications</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h3>Notifications</h3>
    <ul class="list-group">
        <?php if (count($notifications) > 0): ?>
            <?php foreach ($notifications as $notification): ?>
                <li class="list-group-item">
                    <p><?php echo htmlspecialchars($notification['message']); ?></p>
                    <small class="text-muted">Received: <?php echo $notification['created_at']; ?></small>
                    
                    <!-- Display specific icons or labels for types -->
                    <?php if ($notification['type'] == 'order_confirmation'): ?>
                        <span class="badge badge-success">Order Confirmed</span>
                    <?php elseif ($notification['type'] == 'admin_reply'): ?>
                        <span class="badge badge-info">Admin Replied</span>
                    <?php elseif ($notification['delivery_status'] == 'Confirmed'): ?>
                        <span class="badge badge-warning">Order Delivered</span>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li class="list-group-item">No new notifications.</li>
        <?php endif; ?>
    </ul>
</div>
</body>
</html>