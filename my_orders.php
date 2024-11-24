<?php
session_start();
include('admin/db_connect.php');

// Check if user is logged in
if (!isset($_SESSION['login_user_id'])) {
    die("User not logged in.");
}

$message = ''; // Variable to store success/error messages

// Handle order deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_order'])) {
    $order_id = intval($_POST['order_id']);

    // Delete the order and its related data
    $delete_query = "DELETE FROM orders WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_query);

    if (!$delete_stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $delete_stmt->bind_param("i", $order_id);

    // Execute and check for errors
    if (!$delete_stmt->execute()) {
        $message = "Error deleting order: " . $delete_stmt->error;
    } else {
        $message = "Order deleted successfully.";
    }
}

// Handle form submission for rating and feedback
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['rate_product'])) {
    $rating = intval($_POST['rating']);
    $feedback = isset($_POST['feedback']) ? trim($_POST['feedback']) : '';
    $product_id = intval($_POST['product_id']);
    $user_id = $_SESSION['login_user_id'];

    // Check if the user has already rated this product
    $check_query = "SELECT id FROM product_ratings WHERE user_id = ? AND product_id = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("ii", $user_id, $product_id);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        // Update the rating and feedback
        $update_query = "UPDATE product_ratings SET rating = ?, feedback = ? WHERE user_id = ? AND product_id = ?";
        $update_stmt = $conn->prepare($update_query);
        
        if (!$update_stmt) {
            die("Prepare failed: " . $conn->error);
        }
        
        $update_stmt->bind_param("isii", $rating, $feedback, $user_id, $product_id);
        
        // Execute and check for errors
        if (!$update_stmt->execute()) {
            $message = "Error updating rating and feedback: " . $update_stmt->error;
        } else {
            $message = "Thank you for updating your rating and feedback!";
        }
    } else {
        // Insert rating and feedback into the database
        $rating_query = "INSERT INTO product_ratings (user_id, product_id, rating, feedback) VALUES (?, ?, ?, ?)";
        $rating_stmt = $conn->prepare($rating_query);
        
        if (!$rating_stmt) {
            die("Prepare failed: " . $conn->error);
        }
        
        $rating_stmt->bind_param("iiis", $user_id, $product_id, $rating, $feedback);
        
        // Execute and check for errors
        if (!$rating_stmt->execute()) {
            $message = "Error inserting rating and feedback: " . $rating_stmt->error;
        } else {
            $message = "Thank you for rating the product and leaving feedback!";
        }
    }
}

// Fetch orders for the user
$user_id = $_SESSION['login_user_id'];
$query = "SELECT o.id, o.order_number, o.order_date, o.delivery_method, o.payment_method, 
                 p.id AS product_id, p.name AS product_name, ol.qty AS quantity, p.price, 
                 pr.rating, pr.feedback, o.delivery_status 
          FROM orders o
          JOIN order_list ol ON o.id = ol.order_id
          JOIN product_list p ON ol.product_id = p.id
          JOIN user_info u ON u.email = o.email
          LEFT JOIN product_ratings pr ON pr.user_id = u.user_id AND pr.product_id = p.id
          WHERE u.user_id = ?
          ORDER BY o.order_date DESC";

$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
   <style>
        /* Global styles */
        :root {
            --primary-color: #333;
            --secondary-color: #28a745;
            --danger-color: #dc3545;
            --background-color: #f4f4f4;
            --card-shadow: 0 2px 4px rgba(0,0,0,0.1);
            --border-color: #ddd;
            --text-color: #333;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            line-height: 1.6;
        }

        /* Header and Footer */
        header, footer {
            background-color: var(--primary-color);
            color: white;
            padding: 1rem;
            text-align: center;
        }

        main {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 1rem;
        }

        /* Back Button */
        .back-button {
            display: inline-block;
            padding: 0.5rem 1rem;
            background-color: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-bottom: 1rem;
            border: none;
            cursor: pointer;
        }

        .back-button:hover {
            opacity: 0.9;
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
        }

        table th,
        table td {
            padding: 1rem;
            text-align: left;
            border: 1px solid var(--border-color);
        }

        table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        /* Rating Stars */
        .star-rating {
            display: inline-flex;
            gap: 0.25rem;
            margin: 0.5rem 0;
        }

        .star-rating input[type="radio"] {
            display: none;
        }

        .star-rating label {
            font-size: 1.5rem;
            color: #d3d3d3;
            cursor: pointer;
        }

        .star-rating input[type="radio"]:checked ~ label {
            color: gold;
        }

        /* Buttons */
        button {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: opacity 0.2s;
        }

        button[type="submit"] {
            background-color: var(--secondary-color);
            color: white;
        }

        button[name="delete_order"] {
            background-color: var(--danger-color);
            color: white;
        }

        button:hover {
            opacity: 0.9;
        }

        /* Forms */
        textarea {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            margin: 0.5rem 0;
            resize: vertical;
        }

        .rated-message {
            color: var(--secondary-color);
            margin: 0.5rem 0;
        }

        .message {
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 4px;
            background-color: #f8f9fa;
            border: 1px solid var(--border-color);
        }

        /* Mobile Responsive Design */
        @media (max-width: 768px) {
            main {
                padding: 1rem;
                margin: 1rem;
            }

            /* Convert table to cards */
            table, thead, tbody, th, td, tr {
                display: block;
            }

            thead tr {
                position: absolute;
                top: -9999px;
                left: -9999px;
            }

            tr {
                margin-bottom: 1rem;
                border: 1px solid var(--border-color);
                border-radius: 8px;
                box-shadow: var(--card-shadow);
                background: white;
            }

            td {
                position: relative;
                padding: 1rem 1rem 1rem 50%;
                border: none;
                border-bottom: 1px solid var(--border-color);
                min-height: 2.5rem;
            }

            td:last-child {
                border-bottom: none;
            }

            td:before {
                position: absolute;
                left: 1rem;
                width: 45%;
                padding-right: 0.5rem;
                white-space: nowrap;
                font-weight: 600;
                content: attr(data-label);
            }

            /* Form elements in mobile view */
            form {
                padding: 0.5rem;
            }

            textarea {
                margin: 0.5rem 0;
            }

            button {
                width: 100%;
                margin: 0.25rem 0;
            }

            .star-rating {
                justify-content: center;
            }
        }

        /* Small mobile devices */
        @media (max-width: 480px) {
            main {
                padding: 0.5rem;
                margin: 0.5rem;
            }

            td {
                font-size: 0.9rem;
                padding: 0.75rem 0.75rem 0.75rem 45%;
            }

            td:before {
                font-size: 0.9rem;
            }

            .star-rating label {
                font-size: 1.25rem;
            }
            
        }
    </style>
</head>
<body>
    <header>
        <h1>My Orders</h1>
    </header>
    <main>
        <a href="index.php" class="back-button">Back to Home</a>

        <table>
            <thead>
                <tr>
                    <th>Order Number</th>
                    <th>Order Date</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Delivery Method</th>
                    <th>Payment Method</th>
                    <th>Delivery Status</th>
                    
                    <th>Actions</th>
                    <th>Rate this Product</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['order_number']); ?></td>
                    <td><?php echo htmlspecialchars($row['order_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                    <td><?php echo htmlspecialchars($row['price']); ?></td>
                    <td><?php echo htmlspecialchars($row['delivery_method']); ?></td>
                    <td><?php echo htmlspecialchars($row['payment_method']); ?></td>
                    <td><?php echo htmlspecialchars($row['delivery_status']); ?></td>
                    <td>
    <a href="track_order.php?order_id=<?php echo $row['id']; ?>" class="inline-block px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 text-center">
        Track Order
    </a>
</td>

                    <td>
                        <?php if (strcasecmp($row['delivery_status'], 'delivered') == 0): ?>
                           
                            <?php if ($row['rating'] > 0): ?>
                                <div class="rated-message">You have already rated this product.</div>
                            <?php else: ?>
                                <form method="POST" action="">
                                    <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                                    <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                    <label for="rating">Rate:</label>
                                    <div class="star-rating">
                                        <input type="radio" id="star5_<?php echo $row['id']; ?>" name="rating" value="5" <?php if ($row['rating'] == 5) echo 'checked'; ?> >
                                        <label for="star5_<?php echo $row['id']; ?>">★</label>
                                        <input type="radio" id="star4_<?php echo $row['id']; ?>" name="rating" value="4" <?php if ($row['rating'] == 4) echo 'checked'; ?>>
                                        <label for="star4_<?php echo $row['id']; ?>">★</label>
                                        <input type="radio" id="star3_<?php echo $row['id']; ?>" name="rating" value="3" <?php if ($row['rating'] == 3) echo 'checked'; ?>>
                                        <label for="star3_<?php echo $row['id']; ?>">★</label>
                                        <input type="radio" id="star2_<?php echo $row['id']; ?>" name="rating" value="2" <?php if ($row['rating'] == 2) echo 'checked'; ?>>
                                        <label for="star2_<?php echo $row['id']; ?>">★</label>
                                        <input type="radio" id="star1_<?php echo $row['id']; ?>" name="rating" value="1" <?php if ($row['rating'] == 1) echo 'checked'; ?>>
                                        <label for="star1_<?php echo $row['id']; ?>">★</label>
                                    </div>
                                    <textarea name="feedback" placeholder="Leave your feedback here..."></textarea><br>
                                    <button type="submit" name="rate_product">Submit Rating</button>
                                </form>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                    <td>
    <?php if (strcasecmp($row['delivery_status'], 'delivered') == 0): ?>
        <button class="print-button" onclick="printReceipt(<?php echo $row['id']; ?>)">Print Receipt</button>
    <?php endif; ?>
</td>
<td>  <?php if (strcasecmp($row['delivery_status'], '') == 0): ?>
<a href="comment.php?order_id=<?php echo $row['id']; ?>" class="inline-block px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 text-center">
        Message
    </a>
    <?php else: ?>
        <span class="text-muted">Message no longer available</span>
    <?php endif; ?>
    </td>  <td>
                        
                        <form method="POST">
                            <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="delete_order">Delete Order</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <?php if ($message): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
    </main>

    <script>
    function printReceipt(orderNumber) {
        const receiptContent = `
            <div style="text-align: center; font-family: Arial, sans-serif; line-height: 1.5;">
                <h1 style="margin-bottom: 20px;">M&M Cake Ordering</h1>
                <h3 style="margin-bottom: 20px;">Official Receipt</h3>
                <p><strong>Order Number:</strong> ${orderNumber}</p>
                <p><strong>Date:</strong> ${new Date().toLocaleDateString()}</p>
                <hr style="margin: 20px 0;">
                <p>Thank you for shopping with us!</p>
                <p>If you have any questions, feel free to contact us.</p>
                <hr style="margin: 20px 0;">
                <p style="font-size: 14px;">This receipt is auto-generated and valid for reference.</p>
            </div>
        `;

        const printWindow = window.open('', '_blank', 'width=800,height=600');
        printWindow.document.write(`
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Receipt</title>
            </head>
            <body onload="window.print();" style="padding: 20px;">
                ${receiptContent}
            </body>
            </html>
        `);
        printWindow.document.close();
    }
</script>

    <footer>
        <p>&copy; 2024 Cake Ordering System</p>
    </footer>
</body>
</html>
