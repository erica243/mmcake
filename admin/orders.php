<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        .search-bar {
            margin-bottom: 20px;
        }
        .payment-proof img {
            max-width: 50px; /* Smaller thumbnail for table */
            height: auto;
            border: 1px solid #ddd;
            padding: 5px;
            cursor: pointer; /* Change cursor to pointer */
        }
        @media (max-width: 768px) {
            .table-responsive {
                overflow-x: auto;
            }
        }
        /* Style for the full-screen modal */
        .img-preview {
            max-width: 100%;
            max-height: 80vh;
            display: block;
            margin: auto;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <div class="search-bar">
                <input type="text" id="searchInput" class="form-control" placeholder="Search orders...">
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Order Number</th>
                        <th>Customer Name</th>
                        <th>Address</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Order Date</th>
                        <th>Delivery Method</th>
                        <th>Pick-up Date</th>
                        <th>Pick-up Time</th>
                        <th>Delivery Status</th> <!-- Changed to Delivery Status -->
                        <th>Proof of Payment</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="orderTableBody">
                    <?php 
                    include 'db_connect.php';
                    $qry = $conn->query("SELECT * FROM orders");
                    $i = 1;
                    while($row = $qry->fetch_assoc()): 
                    ?>
                    <tr>
                        <td><?php echo $i++ ?></td>
                        <td><?php echo $row['order_number'] ?></td>
                        <td><?php echo $row['name'] ?></td>
                        <td><?php echo $row['address'] ?></td>
                        <td><?php echo $row['email'] ?></td>
                        <td><?php echo $row['mobile'] ?></td>
                        <td><?php echo date('m-d-Y', strtotime($row['order_date'])); ?></td>
                        <td><?php echo $row['delivery_method'] ?></td>
                        <td><?php echo !empty($row['pickup_date']) ? date('m-d-Y', strtotime($row['pickup_date'])) : 'N/A'; ?></td>
                        <td><?php echo !empty($row['pickup_time']) ? $row['pickup_time'] : 'N/A'; ?></td>
                        <td class="text-center">
                            <?php
                            // Delivery status options
                            switch ($row['delivery_status']) {
                                case 'pending':
                                    echo '<span class="badge badge-warning">Pending</span>';
                                    break;
                                case 'confirmed':
                                    echo '<span class="badge badge-info">Confirmed</span>';
                                    break;
                                case 'preparing':
                                    echo '<span class="badge badge-primary">Preparing</span>';
                                    break;
                                case 'ready':
                                    echo '<span class="badge badge-success">Readyfordelivery</span>';
                                    break;
                                    case 'in_transit':
                                        echo '<span class="badge badge-success">In transit</span>';
                                        break;
                                case 'delivered':
                                    echo '<span class="badge badge-dark">Delivered</span>';
                                    break;
                                default:
                                    echo '<span class="badge badge-secondary">Unknown</span>';
                                    break;
                            }
                            ?>
                        </td>
                        <td class="text-center payment-proof">
                            <?php if (!empty($row['payment_proof'])): ?>
                                <img src="<?php echo $row['payment_proof']; ?>" alt="Proof of Payment" onclick="viewImage('<?php echo $row['payment_proof']; ?>')">
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary view_order" data-id="<?php echo $row['id'] ?>">View Order</button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Order Details Modal -->
<div class="modal fade" id="uniModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Order Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modalContent">
                <!-- Order details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Image Preview Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Proof of Payment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="fullImage" class="img-preview" src="" alt="Proof of Payment">
            </div>
        </div>
    </div>
</div>

<script>
    $(document).on('click', '.delete_order', function() {
    const orderId = $(this).attr('data-id');
    if (confirm('Are you sure you want to delete this order?')) {
        $.ajax({
            url: 'ajax.php', // Point to your AJAX handler
            type: 'POST',
            data: { action: 'delete_order', id: orderId },
            success: function(response) {
                if (response == 1) {
                    alert('Order deleted successfully.');
                    location.reload(); // Reload the page to see changes
                } else {
                    alert('Failed to delete order. Please try again.');
                }
            },
            error: function() {
                alert('An error occurred while deleting the order.');
            }
        });
    }
});

    $(document).ready(function(){
        $('.view_order').click(function(){
            uni_modal('Order', 'view_order.php?id=' + $(this).attr('data-id'));
        });

        $("#searchInput").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#orderTableBody tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });
    });

    function uni_modal(title, url) {
        $('#uniModal .modal-title').html(title);
        $('#modalContent').load(url, function() {
            $('#uniModal').modal('show');
        });
    }

    function viewImage(imageUrl) {
        $('#fullImage').attr('src', imageUrl);
        $('#imageModal').modal('show');
    }
    
</script>

</body>
</html>
