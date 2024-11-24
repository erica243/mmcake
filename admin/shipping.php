<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shipping Amounts</title>
    <style>
        /* General form styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        form {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f7f7f7;
            color: #333;
            font-weight: bold;
        }
        input[type="text"], input[type="number"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            display: block;
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        .btn-update, .btn-delete {
            padding: 6px 10px;
            cursor: pointer;
            border: none;
            border-radius: 4px;
            color: white;
            margin-right: 5px;
            font-size: 14px;
        }
        .btn-update {
            background-color: #007bff;
        }
        .btn-delete {
            background-color: #d33;
        }

        /* Success and Error Messages */
        .success-message { text-align: center; color: green; margin: 20px 0; font-weight: bold; }
        .error-message { text-align: center; color: red; margin: 20px 0; font-weight: bold; }

        /* Mobile responsiveness */
        @media (max-width: 768px) {
            form { width: 100%; padding: 10px; }
            th, td { padding: 10px; font-size: 14px; }
        }
    </style>

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<h1>Set Shipping Amounts</h1>
<?php
include 'db_connect.php';

$success_message = "";
$error_message = "";
$submitted_shipping_amounts = [];

// Save or update shipping amount
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['delete_id'])) {
        // Handle delete request
        $delete_id = intval($_POST['delete_id']);
        if ($delete_id > 0) {
            $delete_query = "DELETE FROM shipping_info WHERE id = $delete_id";
            if ($conn->query($delete_query)) {
                $success_message = "Shipping amount deleted successfully!";
            } else {
                $error_message = "Error deleting shipping amount: " . $conn->error;
            }
        } else {
            $error_message = "Invalid ID for deletion.";
        }
    } else {
        // Handle save or update request
        $address = $conn->real_escape_string($_POST['address']);
        $municipality = $conn->real_escape_string($_POST['municipality']);
        $amount = floatval($_POST['shipping_amount']);

        $query = "INSERT INTO shipping_info (address, municipality, shipping_amount) VALUES ('$address', '$municipality', $amount)
                  ON DUPLICATE KEY UPDATE municipality = '$municipality', shipping_amount = $amount";

        if (!$conn->query($query)) {
            $error_message = "Error updating shipping amount for address: $address - " . $conn->error;
        } else {
            $submitted_shipping_amounts[$address] = $amount;
            $success_message = "Shipping amount saved successfully!";
        }
    }
}

// Fetch all existing shipping records
$query = "SELECT id, address, municipality, shipping_amount FROM shipping_info";
$result = $conn->query($query);

$all_shipping_amounts = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $all_shipping_amounts[$row['id']] = [
            'address' => $row['address'],
            'municipality' => $row['municipality'],
            'shipping_amount' => $row['shipping_amount']
        ];
    }
}
?>

<form id='shippingForm' action='' method='POST' onsubmit="return confirmSubmit()">
    <table>
        <tr>
            <th>Address</th>
            <th>Municipality</th>
            <th>Shipping Amount</th>
        </tr>
        <tr>
            <td><input type="text" id="address" name="address" placeholder="Enter address" required></td>
            <td><input type="text" id="municipality" name="municipality" placeholder="Enter municipality" required></td>
            <td><input type="number" id="shipping_amount" name="shipping_amount" placeholder="Enter amount" required></td>
        </tr>
    </table>
    <input type='submit' value='Save Shipping Amount'>
</form>

<?php if ($error_message): ?>
    <div class="error-message"><?php echo $error_message; ?></div>
<?php endif; ?>

<?php if ($success_message): ?>
    <div class="success-message"><?php echo $success_message; ?></div>
<?php endif; ?>

<?php if (!empty($all_shipping_amounts)): ?>
    <div class="results">
        <table>
            <tr>
                <th>Address</th>
                <th>Municipality</th>
                <th>Shipping Amount</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($all_shipping_amounts as $id => $info): ?>
                <tr>
                    <td><?php echo htmlspecialchars($info['address']); ?></td>
                    <td><?php echo htmlspecialchars($info['municipality']); ?></td>
                    <td><?php echo htmlspecialchars($info['shipping_amount']); ?></td>
                    <td>
                        <button class="btn-update" onclick="populateForm('<?php echo htmlspecialchars($info['address']); ?>', '<?php echo htmlspecialchars($info['municipality']); ?>', '<?php echo htmlspecialchars($info['shipping_amount']); ?>')">Update</button>
                        <form action="" method="POST" style="display:inline;">
                            <input type="hidden" name="delete_id" value="<?php echo $id; ?>">
                            <button type="button" class="btn-delete" onclick="confirmDelete(this.form)">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
<?php endif; ?>

<script>
function populateForm(address, municipality, shipping_amount) {
    document.getElementById('address').value = address;
    document.getElementById('municipality').value = municipality;
    document.getElementById('shipping_amount').value = shipping_amount;
}

function confirmDelete(form) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This will permanently delete the shipping amount!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit(); // Submit the form to delete the record
        }
    });
}

function confirmSubmit() {
    return Swal.fire({
        title: 'Confirm Submission',
        text: "Are you sure you want to save the shipping amount?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, save it!'
    }).then((result) => {
        return result.isConfirmed; // Return true to submit the form or false to cancel
    });
}
</script>

</body>
</html>
