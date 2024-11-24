<?php
session_start();
include 'admin/db_connect.php'; // Include the database connection

// Fetch the logged-in user's email
$logged_in_email = '';

if (isset($_SESSION['login_user_id'])) {
    $user_id = intval($_SESSION['login_user_id']); // Sanitize the user ID
    $query = "SELECT email FROM user_info WHERE user_id = ?";
    
    // Use a prepared statement for secure database querying
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $user_id); // Bind user_id as an integer
        $stmt->execute(); // Execute the statement
        $result = $stmt->get_result(); // Get the result set

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc(); // Fetch the user's data
            $logged_in_email = $user['email']; // Assign email to variable
        } else {
            $logged_in_email = 'No email found!'; // Handle no email case
        }

        $stmt->close(); // Close the statement
    } else {
        die("Error in prepared statement: " . $conn->error); // Debug if prepare fails
    }
} else {
    $logged_in_email = 'User not logged in!'; // Handle case if no user is logged in
}
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Initialize variables with empty strings
    $email = '';
    $order_number = '';
    $message = '';
    $image_path = null; 

    // Check if form fields are set and assign them
    if (isset($_POST['email'])) {
        $email = $conn->real_escape_string($_POST['email']);
    }
    if (isset($_POST['order_number'])) {
        $order_number = $conn->real_escape_string($_POST['order_number']);
    }
    if (isset($_POST['message'])) {
        $message = $conn->real_escape_string($_POST['message']);
    }

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $fileSize = $_FILES['image']['size'];
        $fileType = $_FILES['image']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $allowedfileExtensions = array('jpg', 'jpeg', 'png', 'gif');
        if (in_array($fileExtension, $allowedfileExtensions)) {
            // Directory where the file will be uploaded
            $uploadFileDir = 'uploads/';
            // Check if directory exists, if not, try to create it
            if (!is_dir($uploadFileDir)) {
                if (!mkdir($uploadFileDir, 0755, true)) {
                    echo "<script>
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Failed to create upload directory.',
                                confirmButtonText: 'OK'
                            });
                          </script>";
                    exit;
                }
            }

            $dest_path = $uploadFileDir . time() . '_' . $fileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $image_path = $dest_path;
            } else {
                echo "<script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'There was an error moving the uploaded file to the destination.',
                            confirmButtonText: 'OK'
                        });
                      </script>";
                exit;
            }
        } else {
            echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Only .jpg, .jpeg, .png, and .gif files are allowed.',
                        confirmButtonText: 'OK'
                    });
                  </script>";
            exit;
        }
    }

    // Insert the message into the database
    $sql = "INSERT INTO messages (email, order_number, message, photo_path) 
            VALUES ('$email', '$order_number', '$message', '$image_path')";
    
    if ($conn->query($sql)) {
        echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Message submitted successfully!',
                    confirmButtonText: 'OK'
                });
              </script>";
    } else {
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Error: " . $conn->error . "',
                    confirmButtonText: 'OK'
                });
              </script>";
    }
}

// Handle delete request
if (isset($_GET['delete_id'])) {
    $delete_id = $conn->real_escape_string($_GET['delete_id']);
    $delete_sql = "DELETE FROM messages WHERE id = '$delete_id'";
    
    if ($conn->query($delete_sql)) {
        echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Deleted!',
                    text: 'Message deleted successfully!',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'message.php'; // Refresh the page after deletion
                });
              </script>";
    } else {
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Error: " . $conn->error . "',
                    confirmButtonText: 'OK'
                });
              </script>";
    }
}

// Handle reply submission
if (isset($_POST['reply']) && isset($_POST['message_id'])) {
    $message_id = $conn->real_escape_string($_POST['message_id']);
    $reply = $conn->real_escape_string($_POST['reply_message']);

    $reply_sql = "UPDATE messages SET admin_reply = ?, reply_date = NOW() WHERE id = ?";
    $stmt = $conn->prepare($reply_sql);
    $stmt->bind_param("si", $reply, $message_id);

    if ($stmt->execute()) {
        echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Replied!',
                    text: 'Reply sent successfully!',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'message.php'; // Refresh the page after replying
                });
              </script>";
    } else {
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Error: " . $conn->error . "',
                    confirmButtonText: 'OK'
                });
              </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Message</title>
    <link rel="stylesheet" href="path/to/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Font Awesome CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Styles */
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: auto;
            background-color: #ffffff;
            padding: 20px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #343a40;
            font-size: 24px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            font-weight: bold;
            color: #343a40;
            font-size: 16px;
        }

        .form-control {
            border-radius: 15px;
            border: 1px solid #193c5ea;
            font-size: 16px;
            padding: 10px;
        }

        .form-control.email, .form-control.order_number {
            width: 80%;
            max-width: 400px;
            box-sizing: border-box;
        }

        textarea.form-control {
            height: 100px;
        }

        .btn-primary {
            background-color: #1a75ff;
            border-color: #1a75ff;
            width: 100%;
            font-size: 16px;
            padding: 10px;
        }

        .btn-primary:hover {
            background-color: #ffc107;
            border-color: #ffc107;
        }

        .message-box {
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            background-color: #ffffff;
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.05);
            transition: background-color 0.3s, box-shadow 0.3s;
        }

        .message-box:hover {
            background-color: #f1f1f1;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        .message-box img {
            display: block;
            margin-top: 10px;
            border-radius: 5px;
            max-width: 100%;
            height: auto;
        }

        .message-box p {
            margin: 10px 0;
            font-size: 16px;
        }

        .message-box strong {
            color: #343a40;
            font-size: 16px;
        }

        .message-box small {
            color: #6c757d;
            font-size: 14px;
        }

        .admin-reply {
            border-left: 2px solid #007bff;
            padding-left: 10px;
            margin-left: 0;
            margin-top: 10px;
            font-style: italic;
            color: black;
        }

        .reply-form {
            margin-top: 20px;
            padding: 15px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            background-color: #f8f9fa;
        }

        .reply-form textarea {
            width: 100%;
            box-sizing: border-box;
        }

        .btn-back {
            display: inline-block;
            font-size: 18px;
            color: #007bff;
            margin-bottom: 20px;
            text-decoration: none;
        }

        .btn-back i {
            margin-right: 8px;
        }
    </style>
</head>
<body>
<div class="container">
    <a href="index.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back</a>

    <h2>Submit Your Message</h2>
    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control email" id="email" name="email" value="<?php echo htmlspecialchars($logged_in_email); ?>" readonly>
        </div>
       
        <div class="form-group">
            <label for="message">Message</label>
            <textarea class="form-control" id="message" name="message" required></textarea>
        </div>
        <div class="form-group">
            <label for="image">Upload Image (Optional)</label>
            <input type="file" class="form-control" id="image" name="image" accept="image/*">
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>

    <div class="container">
    <h2>Your Messages</h2>
    <?php
    // Ensure the user is logged in
    if (isset($_SESSION['login_user_id'])) {
        $user_id = $_SESSION['login_user_id']; // Get the logged-in user's email

        // Query to fetch messages specific to the logged-in user's email
        $sql = "SELECT * FROM messages WHERE user_id = '$user_id'";
        $result = $conn->query($sql);

        // Check if messages are found
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="message-box">';
                echo '<strong>Email:</strong> ' . htmlspecialchars($row['email']) . '<br>';
                echo '<strong>Message:</strong> <p>' . htmlspecialchars($row['message']) . '</p>';
                
                // Check if the message has an attached image
                if ($row['image_path']) {
                    echo '<img src="' . htmlspecialchars($row['image_path']) . '" alt="Uploaded Image">';
                }

                // Display admin reply if available
                if ($row['admin_reply']) {
                    echo '<div class="admin-reply">';
                    echo '<strong>Admin Reply:</strong> <p>' . htmlspecialchars($row['admin_reply']) . '</p>';
                    echo '<small>Reply Date: ' . htmlspecialchars($row['reply_date']) . '</small>';
                    echo '</div>';
                }

                // Delete message link (using message ID)
                echo '<a href="?delete_id=' . htmlspecialchars($row['id']) . '" class="btn btn-danger" onclick="return confirm(\'Are you sure you want to delete this message?\')">Delete</a>';
                echo '</div>';
            }
        } else {
            echo '<p>No messages found.</p>';
        }
    } else {
        echo '<p>You need to be logged in to view your messages.</p>';
    }
    ?>
</div>

</body>
</html>