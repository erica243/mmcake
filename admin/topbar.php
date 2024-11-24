<?php
// Include your database connection file
include 'db_connect.php';

// Check if the function already exists to avoid redeclaring it
if (!function_exists('get_new_orders_count')) {
    function get_new_orders_count() {
        global $conn;

        // Include conditions for empty or null delivery_status
        $sql = "SELECT COUNT(*) AS total 
                FROM orders 
                WHERE delivery_status = '0' OR delivery_status IS NULL OR delivery_status = ''";
        $result = $conn->query($sql);

        if ($result) {
            $row = $result->fetch_assoc();
            return $row['total'];
        } else {
            return 0; // In case of error
        }
    }
}

if (!function_exists('get_unread_messages_count')) {
    function get_unread_messages_count() {
        global $conn;

        // Fetch unread messages based on status
        $sql = "SELECT COUNT(*) AS total FROM messages WHERE status = 0";
        $result = $conn->query($sql);

        if ($result) {
            $row = $result->fetch_assoc();
            return $row['total'];
        } else {
            return 0; // In case of error
        }
    }
}

// Fetch new orders count
$newOrdersCount = get_new_orders_count();

// Fetch unread messages count
$unreadMessagesCount = get_unread_messages_count();
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    .logo {
      margin: auto;
      font-size: 20px;
      background: white;
      padding: 5px 11px;
      border-radius: 50%;
      color: #000000b3;
    }
    .notification-badge {
      position: absolute;
      top: -5px;
      right: -25px;
      background-color: red;
      color: white;
      padding: 5px 8px;
      border-radius: 50%;
      font-size: 12px;
    }
    .notification-icon {
      position: relative;
      cursor: pointer;
      margin-right: 50px;
    }
    .notification-link {
      display: flex;
      align-items: center;
      text-decoration: none;
    }
  </style>
</head>
<body>
  <nav class="navbar navbar-light bg-light fixed-top" style="padding: 0; height: 3.4em">
    <div class="container-fluid mt-2 mb-2">
      <div class="col-lg-12">
        <div class="col-md-1 float-left" style="display: flex;">
          <div class="logo"></div>
        </div>
        <div class="col-md-4 float-left" style="font-size: 30px;">
          <large style="font-family: 'Dancing Script', cursive !important;"><b><?php echo $_SESSION['setting_name']; ?></b></large>
        </div>
        <div class="col-md-2 float-right" style="display: flex; align-items: center;">
          <!-- Notification Icon and Badge for Orders -->
          <a href="index.php?page=orders" class="notification-link">
            <div class="notification-icon">
              <i class="fa fa-bell"></i>
              <span class="notification-badge"><?php echo $newOrdersCount; ?></span>
            </div>
          </a>
          <!-- Notification Icon and Badge for Messages -->
          <a href="index.php?page=message" class="notification-link">
            <div class="notification-icon">
              <i class="fa fa-envelope"></i>
              <span class="notification-badge"><?php echo $unreadMessagesCount; ?></span>
            </div>
          </a>
          <!-- Admin Profile Dropdown -->
          <div class="dropdown">
            <a class="text-dark dropdown-toggle" href="#" role="button" id="adminDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <?php echo $_SESSION['login_name']; ?>
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="adminDropdown">
             <!---- <a class="dropdown-item" href="admin_profile.php">Profile</a>---->
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="ajax.php?action=logout">Logout <i class="fa fa-sign-out-alt"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </nav>
</body>
</html>
