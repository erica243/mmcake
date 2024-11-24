<?php
// signup.php
session_start();
require_once('admin/db_connect.php');

// Fetch unique municipalities
$municipalities = [];
$query = $conn->query("SELECT DISTINCT municipality FROM shipping_info WHERE municipality IS NOT NULL ORDER BY municipality");
while($row = $query->fetch_assoc()) {
    $municipalities[] = $row['municipality'];
}

// Fetch all shipping info for client-side filtering
$shipping_info = [];
$query = $conn->query("SELECT address, municipality FROM shipping_info");
while($row = $query->fetch_assoc()) {
    $shipping_info[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container-fluid">
        <form id="signup-form" method="POST">
            <div class="form-group mb-3">
                <label for="first_name">First Name</label>
                <input type="text" class="form-control" id="first_name" name="first_name" required>
            </div>
            
            <div class="form-group mb-3">
                <label for="last_name">Last Name</label>
                <input type="text" class="form-control" id="last_name" name="last_name" required>
            </div>
            
            <div class="form-group mb-3">
                <label for="mobile">Contact</label>
                <div class="input-group">
                    <span class="input-group-text">+63</span>
                    <input type="tel" class="form-control" id="mobile" name="mobile" maxlength="10" required>
                </div>
                <small class="form-text text-muted">Enter 10-digit mobile number</small>
            </div>

            <div class="form-group mb-3">
                <label for="municipality">Municipality</label>
                <select class="form-control" id="municipality" name="municipality" required>
                    <option value="">Select Municipality</option>
                    <?php foreach($municipalities as $municipality): ?>
                        <option value="<?php echo htmlspecialchars($municipality); ?>">
                            <?php echo htmlspecialchars($municipality); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group mb-3">
                <label for="address">Address</label>
                <select class="form-control" id="address" name="address" required disabled>
                    <option value="">Select Municipality First</option>
                </select>
            </div>

            <!-- Add a new Street input field -->
            <div class="form-group mb-3">
                <label for="street">Street</label>
                <input type="text" class="form-control" id="street" name="street" required>
            </div>
            
            <div class="form-group mb-3">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            
            <div class="form-group mb-3">
                <label for="password">Password</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="password" name="password" required>
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                        <i class="fa fa-eye"></i>
                    </button>
                </div>
                <small class="form-text text-muted">
                    Password must be at least 8 characters long and include uppercase, lowercase, numbers, and symbols.
                </small>
            </div>
            
            <div class="form-check mb-3">
                <input type="checkbox" class="form-check-input" id="terms" required>
                <label class="form-check-label" for="terms">
                    I agree to the Terms and Conditions
                </label>
            </div>
            
            <button type="submit" class="btn btn-primary">Create Account</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
    $(document).ready(function() {
        // Store shipping info for client-side filtering
        const shippingInfo = <?php echo json_encode($shipping_info); ?>;

        // Municipality change handler
        $('#municipality').change(function() {
            const selectedMunicipality = $(this).val();
            const addressSelect = $('#address');
            
            // Clear and disable address select if no municipality is selected
            if (!selectedMunicipality) {
                addressSelect.html('<option value="">Select Municipality First</option>').prop('disabled', true);
                return;
            }

            // Filter addresses for selected municipality
            const filteredAddresses = shippingInfo.filter(info => info.municipality === selectedMunicipality);
            
            // Enable and populate address select
            addressSelect.prop('disabled', false);
            addressSelect.html('<option value="">Select Address</option>');
            
            filteredAddresses.forEach(info => {
                addressSelect.append(`<option value="${info.address}">${info.address}</option>`);
            });
        });

        // Password visibility toggle
        $('#togglePassword').click(function() {
            const password = $('#password');
            const icon = $(this).find('i');
            
            if (password.attr('type') === 'password') {
                password.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                password.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

     // Mobile number handling
     $('#mobile').on('input', function() {
            // Remove any non-digit characters
            let value = $(this).val().replace(/\D/g, '');
            
            // Ensure only 10 digits can be entered
            if (value.length > 10) {
                value = value.slice(0, 10);
            }
            
            $(this).val(value);
        });
        // Form validation and submission
        $('#signup-form').on('submit', function(e) {
            e.preventDefault();
            
            // Basic client-side validation for password
            const password = $('#password').val();
            if (password.length < 8) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Password',
                    text: 'Password must be at least 8 characters long'
                });
                return;
            }

            if (!/[A-Z]/.test(password) || !/[a-z]/.test(password) || 
                !/[0-9]/.test(password) || !/[^A-Za-z0-9]/.test(password)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Password',
                    text: 'Password must include uppercase, lowercase, numbers, and symbols'
                });
                return;
            }

          // Mobile number validation
          const mobile = $('#mobile').val();
            if (mobile.length !== 10) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Mobile Number',
                    text: 'Please enter a 10-digit mobile number'
                });
                return;
            }

            // Prepare form data with complete phone number
            let formData = $(this).serialize();
            formData = formData.replace('mobile=' + mobile, 'mobile=+63' + mobile);
            
            $.ajax({
                url: 'signup_action.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                beforeSend: function() {
                    Swal.fire({
                        title: 'Creating Account',
                        text: 'Please wait...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(function() {
                            window.location.href = 'email_otp.php';
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.message
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('XHR:', xhr);
                    console.error('Status:', status);
                    console.error('Error:', error);
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'An error occurred. Please try again later.'
                    });
                }
            });
        });
    });
    </script>
</body>
</html>
