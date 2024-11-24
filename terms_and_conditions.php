<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms and Conditions</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
    <style>
        body {
            background-color: #343a40; /* Dark background */
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .modal-dialog {
            max-width: 600px; /* Limit width */
        }
        .modal-content {
            border-radius: 8px;
        }
        .modal-header {
            background-color: #007bff;
            color: white;
            border-bottom: none;
            display: flex;
            justify-content: center;
        }
        .modal-header h5 {
            margin: 0;
        }
        .modal-body {
            color: #343a40;
        }
        .modal-footer {
            display: flex;
            justify-content: center;
            border-top: none;
        }
        .btn-accept {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none; /* Make sure it looks like a button */
        }
    </style>
</head>
<body>

<!-- Terms and Conditions Modal -->
<div class="modal fade" id="termsModal" tabindex="-1" role="dialog" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="termsModalLabel">Terms and Conditions</h5>
            </div>
            <div class="modal-body">
                <p><strong>Effective Date:</strong> [Insert Date]</p>

                <p>Welcome to the M&M Cake Ordering System! By using our website and services, you agree to the following terms and conditions. Please read them carefully.</p>

                <h5>1. Acceptance of Terms</h5>
                <p>By accessing or using the M&M Cake Ordering System, you agree to be bound by these Terms and Conditions. If you do not agree to these terms, please do not use our services.</p>

                <h5>2. Changes to Terms</h5>
                <p>We reserve the right to modify these Terms and Conditions at any time. Any changes will be effective immediately upon posting on our website. Your continued use of the service after any changes signifies your acceptance of the revised terms.</p>

                <h5>3. User Accounts</h5>
                <p>To place an order, you may need to create an account. You are responsible for maintaining the confidentiality of your account information and for all activities that occur under your account. Please notify us immediately of any unauthorized use of your account.</p>

                <h5>4. Orders and Payments</h5>
                <p>All orders are subject to availability. We strive to provide accurate product descriptions and prices; however, errors may occur. We reserve the right to cancel or refuse any order for any reason, including but not limited to product availability, errors in product or pricing information, or suspected fraudulent activity.</p>
                <p>Payment must be made at the time of order placement. We accept various payment methods, including credit/debit cards and other specified options.</p>

                <h5>5. Delivery and Pickup</h5>
                <p>Delivery options and fees will be provided during the checkout process. We will make every effort to deliver your order on time; however, we are not responsible for delays caused by circumstances beyond our control.</p>

                <h5>6. No Cancellations</h5>
                <p>All orders placed through the M&M Cake Ordering System are final and cannot be canceled once confirmed. Please ensure that your order details are correct before completing your purchase.</p>

                <h5>7. Refunds</h5>
                <p>Refunds will be issued at our discretion and only in cases where an error has occurred on our part. Please contact us for further assistance if you believe you are eligible for a refund.</p>

                <h5>8. User Conduct</h5>
                <p>You agree not to engage in any of the following prohibited activities:</p>
                <ul>
                    <li>Using the service for any unlawful purpose.</li>
                    <li>Impersonating any person or entity or misrepresenting your affiliation with any person or entity.</li>
                    <li>Transmitting any harmful, offensive, or disruptive content.</li>
                </ul>

                <h5>9. Intellectual Property</h5>
                <p>All content on the M&M Cake Ordering System, including text, graphics, logos, and software, is the property of M&M Cake Ordering System and is protected by copyright and other intellectual property laws. You may not reproduce, distribute, or create derivative works without our express written permission.</p>

                <h5>10. Limitation of Liability</h5>
                <p>To the fullest extent permitted by law, M&M Cake Ordering System shall not be liable for any indirect, incidental, special, or consequential damages arising out of or in connection with your use of the service.</p>

                <h5>11. Governing Law</h5>
                <p>These Terms and Conditions shall be governed by and construed in accordance with the laws of [insert jurisdiction]. Any disputes arising from these terms shall be resolved in the courts of [insert jurisdiction].</p>

                <h5>12. Contact Us</h5>
                <p>If you have any questions about these Terms and Conditions, please contact us at:</p>
                <p><strong>M&M Cake Ordering System</strong><br>
                [Insert Contact Information]<br>
                [Insert Email Address]<br>
                [Insert Phone Number]</p>
            </div>
            <div class="modal-footer">
                <button id="acceptBtn" class="btn btn-accept">
                    <i class=""></i> Accept
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script>
    $(document).ready(function() {
        $('#termsModal').modal({
            backdrop: 'static',
            keyboard: false,
            show: true
        });

        // Redirect to signup.php on Accept button click
        $('#acceptBtn').click(function() {
            window.location.href = 'index.php';
        });
    });
</script>
</body>
</html>
