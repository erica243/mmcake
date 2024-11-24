<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        #uni_modal .modal-dialog {
            max-width: 90%;
            width: auto;
        }
        #uni_modal .modal-body {
            overflow-x: auto;
        }
        #uni_modal .modal-footer {
            display: none;
        }
    </style>
</head>
<body>
<?php
include 'db_connect.php';
$orderId = $_GET['id'];

// Use prepared statements for security
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->bind_param("i", $orderId);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$orderStatus = $order['status']; // 1 for confirmed, 0 for not confirmed
$deliveryStatus = $order['delivery_status']; // Fetch delivery status

// Convert the order date to 'm-d-Y' format
$formatted_order_date = date("m-d-Y", strtotime($order['order_date']));

// Fetch order items
$stmt = $conn->prepare("SELECT o.qty, p.name, p.description, p.price, 
                                (o.qty * p.price) AS amount
                        FROM order_list o 
                        INNER JOIN product_list p ON o.product_id = p.id 
                        WHERE o.order_id = ?");
$stmt->bind_param("i", $orderId);
$stmt->execute();
$orderItems = $stmt->get_result();

// Fetch shipping information based on the order's address
$address = $order['address']; // Get the address from the order
$shippingStmt = $conn->prepare("SELECT shipping_amount FROM shipping_info WHERE address = ?");
$shippingStmt->bind_param("s", $address);
$shippingStmt->execute();
$shippingResult = $shippingStmt->get_result();
$shippingAmount = $shippingResult->fetch_assoc()['shipping_amount'] ?? 0;
?>

<div class="container-fluid mt-4">
    <h4>Order Details</h4>
    <table class="table table-bordered mt-4">
        <thead>
            <tr>
                <th>Order Date</th>
                <th>Order Number</th>
                <th>Customer Name</th>
                <th>Address</th>
                <th>Delivery Method</th>
                <th>Mode of Payment</th>
                <th>Qty</th>
                <th>Product</th>
                <th>Description</th>
                <th>Price</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $total = 0;
            while ($row = $orderItems->fetch_assoc()):
                $total += $row['amount'];
            ?>
            <tr>
                <td><?php echo $formatted_order_date; ?></td>
                <td><?php echo $order['order_number']; ?></td>
                <td><?php echo $order['name']; ?></td>
                <td><?php echo $order['address']; ?></td>
                <td><?php echo $order['delivery_method']; ?></td>
                <td><?php echo $order['payment_method']; ?></td>
                <td><?php echo $row['qty']; ?></td>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['description']; ?></td>
                <td><?php echo number_format($row['price'], 2); ?></td>
                <td><?php echo number_format($row['amount'], 2); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="10" class="text-right">Subtotal</th>
                <th><?php echo number_format($total, 2); ?></th>
            </tr>
            <tr>
                <th colspan="10" class="text-right">Shipping Amount</th>
                <th><?php echo number_format($shippingAmount, 2); ?></th>
            </tr>
            <tr>
                <th colspan="10" class="text-right">TOTAL</th>
                <th><?php echo number_format($total + $shippingAmount, 2); ?></th>
            </tr>
        </tfoot>
    </table>
    
    <div class="text-center mt-4">
      <!--  <button class="btn btn-primary" id="confirm" type="button" onclick="confirm_order()" <?php echo $orderStatus == 1 ? 'disabled' : '' ?>>Confirm</button>-->
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
       <!-- <button class="btn btn-success" type="button" onclick="print_receipt()">Print Receipt</button>-->
        <button class="btn btn-danger" type="button" id="delete_order" onclick="delete_order()">Delete Order</button>

         <!-- Delivery Status Dropdown -->
         <label for="delivery_status" class="mt-3">Update Delivery Status:</label>
<select id="delivery_status" class="form-control w-50 mx-auto mt-2" onchange="update_delivery_status()">
    <option value="pending" 
        <?php echo !isset($deliveryStatus) || $deliveryStatus == 'pending' ? 'selected' : 'disabled'; ?>>
        Pending
    </option>
    <option value="confirmed" 
        <?php echo $deliveryStatus == 'confirmed' ? 'selected' : (!isset($deliveryStatus) || $deliveryStatus == 'pending' ? '' : 'disabled'); ?>>
        Confirmed
    </option>
    <option value="preparing" 
        <?php echo $deliveryStatus == 'preparing' ? 'selected' : (in_array($deliveryStatus, ['ready', 'in_transit', 'delivered']) ? 'disabled' : ''); ?>>
        Preparing
    </option>
    <option value="ready" 
        <?php echo $deliveryStatus == 'ready' ? 'selected' : (in_array($deliveryStatus, ['in_transit', 'delivered']) ? 'disabled' : ''); ?>>
        Ready For Delivery
    </option>
    <option value="in_transit" 
        <?php echo $deliveryStatus == 'in_transit' ? 'selected' : ($deliveryStatus == 'delivered' ? 'disabled' : ''); ?>>
        In transit
    </option>
    <option value="delivered" 
        <?php echo $deliveryStatus == 'delivered' ? 'selected disabled' : ''; ?>>
        Delivered
    </option>
</select>


    </div>
</div>

<script>
    function confirm_order() {
        Swal.fire({
            title: 'Confirm Order',
            text: 'Are you sure you want to confirm this order?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, confirm!'
        }).then((result) => {
            if (result.isConfirmed) {
                start_load();
                $.ajax({
                    url: 'ajax.php?action=confirm_order',
                    method: 'POST',
                    data: { id: '<?php echo $_GET['id'] ?>' },
                    success: function(resp) {
                        if (resp == 1) {
                            Swal.fire('Confirmed!', 'Order has been successfully confirmed.', 'success').then(function() {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error!', 'Error confirming order: ' + resp, 'error');
                        }
                        end_load();
                    },
                    error: function() {
                        end_load();
                        Swal.fire('Error!', 'AJAX request failed.', 'error');
                    }
                });
            }
        });
    }
    
    function update_delivery_status() {
        var status = $('#delivery_status').val();
        var orderId = '<?php echo $_GET["id"]; ?>';

        Swal.fire({
            title: 'Update Delivery Status',
            text: 'Are you sure you want to update the delivery status?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, update it!'
        }).then((result) => {
            if (result.isConfirmed) {
                start_load();
                $.ajax({
                    url: 'ajax.php?action=update_delivery_status',
                    method: 'POST',
                    data: {
                        id: orderId,
                        status: status
                    },
                    success: function(resp) {
                        try {
                            var jsonResp = JSON.parse(resp); // Parse the JSON response

                            if (jsonResp.success) {
                                Swal.fire('Updated!', jsonResp.message, 'success').then(function() {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Error!', jsonResp.message, 'error');
                            }
                        } catch (e) {
                            Swal.fire('Error!', 'Unexpected response: ' + resp, 'error');
                        }
                        end_load();
                    },
                    error: function() {
                        end_load();
                        Swal.fire('Error!', 'AJAX request failed.', 'error');
                    }
                });
            }
        });
    }

    function print_receipt() {
        var receiptWindow = window.open('', '', 'height=600,width=800,location=no');
        var logoUrl = 'assets/img/logo.jpg'; // Full URL

        var orderNumber = '<?php echo $order["order_number"]; ?>';
        var orderDate = '<?php echo $formatted_order_date; ?>';
        var customerName = '<?php echo $order["name"]; ?>';
        var address = '<?php echo $order["address"]; ?>';
        var deliveryMethod = '<?php echo $order["delivery_method"]; ?>';
        var paymentMethod = '<?php echo $order["payment_method"]; ?>';
        var shippingAmount = '<?php echo number_format($shippingAmount, 2); ?>';

        var total = '<?php echo number_format($total + $shippingAmount, 2); ?>';

        // Add content to the window
        receiptWindow.document.write('<html><head><title>Receipt</title>');
        receiptWindow.document.write('<style>body { font-family: Arial, sans-serif; }</style></head><body>');
        receiptWindow.document.write('<div style="text-align: center;">');
        receiptWindow.document.write('<img src="' + logoUrl + '" style="width: 200px;"><br>');
        receiptWindow.document.write('<h2>Receipt</h2></div>');
        receiptWindow.document.write('<p>Order Number: ' + orderNumber + '</p>');
        receiptWindow.document.write('<p>Order Date: ' + orderDate + '</p>');
        receiptWindow.document.write('<p>Customer Name: ' + customerName + '</p>');
        receiptWindow.document.write('<p>Address: ' + address + '</p>');
        receiptWindow.document.write('<p>Delivery Method: ' + deliveryMethod + '</p>');
        receiptWindow.document.write('<p>Payment Method: ' + paymentMethod + '</p>');

        receiptWindow.document.write('<br><br>');
        receiptWindow.document.write('<table border="1" style="width: 100%; text-align: left; border-collapse: collapse;">');
        receiptWindow.document.write('<tr><th>Product</th><th>Description</th><th>Qty</th><th>Price</th><th>Amount</th></tr>');

        <?php 
        $orderItems->data_seek(0); // Reset pointer for order items
        while ($row = $orderItems->fetch_assoc()): ?>
            receiptWindow.document.write('<tr><td><?php echo $row["name"]; ?></td>');
            receiptWindow.document.write('<td><?php echo $row["description"]; ?></td>');
            receiptWindow.document.write('<td><?php echo $row["qty"]; ?></td>');
            receiptWindow.document.write('<td><?php echo number_format($row["price"], 2); ?></td>');
            receiptWindow.document.write('<td><?php echo number_format($row["amount"], 2); ?></td></tr>');
        <?php endwhile; ?>

        receiptWindow.document.write('</table>');
        receiptWindow.document.write('<br><br>');
        receiptWindow.document.write('<p>Shipping Amount: ' + shippingAmount + '</p>');
        receiptWindow.document.write('<h3>Total: ' + total + '</h3>');

        receiptWindow.document.write('</body></html>');
        receiptWindow.document.close();
        receiptWindow.print();
    }

    function delete_order() {
        Swal.fire({
            title: 'Delete Order',
            text: 'Are you sure you want to delete this order?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                start_load();
                $.ajax({
                    url: 'ajax.php?action=delete_order',
                    method: 'POST',
                    data: { id: '<?php echo $_GET['id'] ?>' },
                    success: function(resp) {
                        if (resp == 1) {
                            Swal.fire('Deleted!', 'Order has been successfully deleted.', 'success').then(function() {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error!', 'Error deleting order: ' + resp, 'error');
                        }
                        end_load();
                    },
                    error: function() {
                        end_load();
                        Swal.fire('Error!', 'AJAX request failed.', 'error');
                    }
                });
            }
        });
    }

    function start_load() {
        $('body').prepend('<div id="preloader2"></div>');
    }

    function end_load() {
        $('#preloader2').fadeOut('fast', function() {
            $(this).remove();
        });
    }
</script>

<div id="preloader2" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.8); z-index: 9999;">
    <div class="text-center" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
</div>
</body>
</html>
