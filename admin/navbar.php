<?php
// Assume you have a function to get the count of new/unread messages
// You would replace this with your actual method of fetching the count
$message_count = 0; // Replace with dynamic count from your database
?>

<style>
 /* Define the custom background color */
.b-lightblue {
    background-color: #4d94ff!important; /* Custom light blue background color */
    color: #000 !important; /* Text color */
}

#sidebar {
    height: 100vh; /* Full height */
    padding-top: 20px; /* Padding at the top */
}

#sidebar .sidebar-list a {
    color: #000 !important; /* Text color for links */
    display: block; /* Make links block level */
    padding: 10px 15px; /* Padding for links */
    text-decoration: none; /* Remove underline from links */
    position: relative; /* To position the badge */
}

#sidebar .sidebar-list a:hover {
    background-color: white !important; /* Darker background on hover */
}

#sidebar .sidebar-list a.active {
    background-color: #99d6ff!important; /* Active item background color */
    color: #fff !important; /* Active item text color */
}

.notification-badge {
    position: absolute;
    top: 5px;
    right: 10px;
    background-color: red;
    color: white;
    padding: 2px 6px;
    border-radius: 50%;
    font-size: 12px;
    font-weight: bold;
}
.delivery-form {
    margin-top: 20px; /* Space above the form */
    padding: 15px; /* Padding for the form */
    background-color: #fff; /* White background for the form */
    border-radius: 5px; /* Rounded corners */
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow */
}

.delivery-form input {
    width: 100%; /* Full width for inputs */
    padding: 10px; /* Padding for inputs */
    margin: 5px 0; /* Margin between inputs */
    border: 1px solid #ccc; /* Border for inputs */
    border-radius: 4px; /* Rounded corners */
}
</style>

<nav id="sidebar" class='mx-lt-5 b-lightblue'>
    <div class="sidebar-list">
        <a href="index.php?page=home" class="nav-item nav-home">
            <span class='icon-field'><i class="fa fa-home"></i></span> Home
        </a>
        <a href="index.php?page=orders" class="nav-item nav-orders">
            <span class='icon-field'><i class="fas fa-box"></i></span> Orders
        </a>
        <a href="index.php?page=menu" class="nav-item nav-menu">
            <span class='icon-field'><i class="fa fa-list"></i></span> Menu
        </a>
        <a href="index.php?page=categories" class="nav-item nav-categories">
            <span class='icon-field'><i class="fa fa-th-list"></i></span> Category List
        </a>
        <a href="index.php?page=reports" class="nav-item nav-reports">
            <span class='icon-field'><i class="fa fa-chart-line"></i></span> Reports
        </a>
        <a href="index.php?page=message" class="nav-item nav-message">
            <span class='icon-field'><i class="fa fa-envelope"></i></span> Messages
           
        </a>
        <a href="index.php?page=shipping" class="nav-item nav-message">
            <span class='icon-field'><i class="fa fa-envelope"></i></span> Fee
           
        </a>
        <?php if ($_SESSION['login_id'] == 1): ?>
            <a href="index.php?page=users" class="nav-item nav-users">
                <span class='icon-field'><i class="fa fa-users"></i></span> Users
            
        
            <a href="index.php?page=site_settings" class="nav-item nav-site_settings">
                <span class='icon-field'><i class="fa fa-cogs"></i></span> Site Settings
            </a>
        <?php endif; ?>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let page = '<?php echo $_GET['page'] ?? ''; ?>';  // Shortened syntax for PHP 7+
        if (page) {
            let activeItem = document.querySelector('.nav-' + page);
            if (activeItem) {
                activeItem.classList.add('active');
            }
        }
    });
</script>
