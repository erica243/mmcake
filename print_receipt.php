<?php
session_start();
include('admin/db_connect.php');

// Check if user is logged in
if (!isset($_SESSION['login_user_id'])) {
    die("User not logged in.");
}

if (!isset($_GET['order_id'])) {
    die("Order ID not provided.");
}

$order_id = intval($_GET['order_id']);

// Fetch order details
$query = "SELECT o.order_number, o.order_date, o.delivery_method, o.payment_method, 
                 p.name AS product_name, ol.qty AS quantity, p.price 
          FROM orders o
          JOIN order_list ol ON o.id = ol.order_id
          JOIN product_list p ON ol.product_id = p.id
          WHERE o.id = ?";

$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("No order found.");
}

$order_details = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt - Order #<?php echo htmlspecialchars($order_details['order_number']); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table, th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Receipt for Order #<?php echo htmlspecialchars($order_details['order_number']); ?></h1>
    <p><strong>Order Date:</strong> <?php echo htmlspecialchars($order_details['order_date']); ?></p>
    <p><strong>Delivery Method:</strong> <?php echo htmlspecialchars($order_details['delivery_method']); ?></p>
    <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($order_details['payment_method']); ?></p>
    
    <table>
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php echo htmlspecialchars($order_details['product_name']); ?></td>
                <td><?php echo htmlspecialchars($order_details['quantity']); ?></td>
                <td><?php echo htmlspecialchars($order_details['price']); ?></td>
            </tr>
        </tbody>
    </table>

    <script>
        window.print();
    </script>
</body>
</html>
