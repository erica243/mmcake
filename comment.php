<?php
// Include necessary files and start session
include 'admin/db_connect.php';
session_start();

// Check if `order_id` is provided in the URL
if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
    die("Order ID is required to leave a comment.");
}

// Fetch the `order_id` from the URL
$order_id = intval($_GET['order_id']);

// Query to fetch the email and order_number for the given order_id
$stmt = $conn->prepare("SELECT order_number, email FROM orders WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

// Check if the order exists
if (!$order) {
    die("Order not found.");
}

// Variables for order details
$order_number = htmlspecialchars($order['order_number']);
$email = htmlspecialchars($order['email']);

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comment = htmlspecialchars($_POST['comment']);
    $uploaded_file = $_FILES['photo'] ?? null;

    // Handle optional photo upload
    if ($uploaded_file && $uploaded_file['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        $file_name = basename($uploaded_file['name']);
        $target_path = $upload_dir . time() . '_' . $file_name;

        // Ensure upload directory exists
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Move uploaded file
        if (move_uploaded_file($uploaded_file['tmp_name'], $target_path)) {
            $photo_path = $target_path;
        } else {
            $photo_path = null; // Set to null if upload fails
        }
    } else {
        $photo_path = null; // No file uploaded
    }

    // Insert comment into the database
    $stmt = $conn->prepare("INSERT INTO messages (order_number, email, message, photo_path) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $order_number, $email, $comment, $photo_path);
    $stmt->execute();

    // Display success message
    $message = "Comment successfully submitted!";
}

// Fetch the comments and admin replies
$stmt = $conn->prepare("SELECT message, photo_path, admin_reply FROM messages WHERE order_number = ? ORDER BY created_at DESC");
$stmt->bind_param("s", $order_number);
$stmt->execute();
$comments = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave a Comment</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Leave a Comment for Order #<?php echo $order_number; ?></h2>
        <p>Email: <?php echo $email; ?></p>
        
        <?php if (!empty($message)): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="comment">Your Message:</label>
                <textarea id="comment" name="comment" class="form-control" rows="4" placeholder="Write your comment here..." required></textarea>
            </div>
            <div class="form-group">
                <label for="photo">Upload a Photo (optional):</label>
                <input type="file" id="photo" name="photo" class="form-control-file" accept="image/*">
            </div>
            <button type="submit" class="btn btn-success">Submit Comment</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </form>

        <!-- Display previous comments and admin replies -->
        <hr>
        <h3>Previous Comments:</h3>
        <?php while ($row = $comments->fetch_assoc()): ?>
            <div class="comment-box">
                <p><strong>You:</strong> <?php echo htmlspecialchars($row['message']); ?></p>
                
                <?php if (!empty($row['photo_path'])): ?>
                    <p><strong>Photo:</strong> <img src="<?php echo htmlspecialchars($row['photo_path']); ?>" alt="Uploaded Image" style="max-width: 100px;"></p>
                <?php endif; ?>
                
                <?php if (!empty($row['admin_reply'])): ?>
                    <div class="admin-reply mt-3">
                        <strong>Admin Reply:</strong>
                        <p><?php echo htmlspecialchars($row['admin_reply']); ?></p>
                    </div>
                <?php else: ?>
                    <p><em>No reply from admin yet.</em></p>
                <?php endif; ?>
            </div>
            <hr>
        <?php endwhile; ?>
    </div>
</body>
</html>
