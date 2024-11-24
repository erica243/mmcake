<?php
session_start();
include('admin/db_connect.php');

if (!isset($_SESSION['login_user_id'])) {
    die("User not logged in.");
}

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$user_id = $_SESSION['login_user_id'];

$query = "SELECT o.*, 
          DATE_FORMAT(o.order_date, '%M %d, %Y %h:%i %p') as formatted_order_date,
          DATE_FORMAT(o.status_updated_at, '%M %d, %Y %h:%i %p') as last_update,
          DATE_FORMAT(o.estimated_delivery, '%M %d, %Y') as delivery_date
          FROM orders o
          JOIN user_info u ON u.email = o.email
          WHERE o.id = ? AND u.user_id = ?";

$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    die("Order not found or access denied.");
}

$stages = [
    'pending' => [
        'title' => 'Order Pending',
        'description' => 'Your order is awaiting confirmation.',
        'icon' => 'clock',
        'matches' => ['pending', 'Pending']
    ],
    'confirmed' => [
        'title' => 'Order Confirmed',
        'description' => 'We have confirmed your order and started processing.',
        'icon' => 'check-circle',
        'matches' => ['confirmed', 'Confirmed']
    ],
    'preparing' => [
        'title' => 'Preparing',
        'description' => 'Your cake is being freshly baked and decorated.',
        'icon' => 'clock',
        'matches' => ['preparing', 'Preparing', 'in preparation', 'In Preparation']
    ],
    'ready' => [
        'title' => 'Ready for Delivery/Pickup',
        'description' => 'Your order is ready and waiting for delivery or pickup.',
        'icon' => 'package',
        'matches' => ['ready', 'Ready', 'ready for delivery', 'Ready for Delivery', 'ready for pickup', 'Ready for Pickup']
    ],
    'in_transit' => [
        'title' => 'In Transit',
        'description' => 'Your order is on its way to you.',
        'icon' => 'truck',
        'matches' => ['in_transit', 'in transit', 'In Transit', 'out for delivery', 'Out for Delivery']
    ],
    'delivered' => [
        'title' => 'Delivered',
        'description' => 'Your order has been delivered successfully.',
        'icon' => 'check-square',
        'matches' => ['delivered', 'Delivered', 'completed', 'Completed']
    ]
];

function normalizeStatus($status, $stages) {
    if (empty($status)) return 'pending';
    
    foreach ($stages as $key => $stage) {
        if (in_array($status, $stage['matches'], true)) {
            return $key;
        }
    }
    return 'pending';
}

$current_stage = normalizeStatus($order['delivery_status'], $stages);
$stages_array = array_keys($stages);
$current_index = array_search($current_stage, $stages_array);

$total_stages = count($stages) - 1;
$progress_percentage = ($current_index / $total_stages) * 100;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Order #<?php echo htmlspecialchars($order['order_number']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.css" rel="stylesheet">
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        .tracking-line {
            position: absolute;
            top: 24px;
            left: 24px;
            width: calc(100% - 48px);
            height: 2px;
            background-color: #E5E7EB;
            z-index: 0;
        }
        
        .tracking-line-progress {
            height: 100%;
            background-color: #3B82F6;
            transition: width 0.5s ease-in-out;
        }

        .stage-icon {
            z-index: 1;
            background-color: white;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="mb-6">
            <a href="my_orders.php" class="text-blue-600 hover:text-blue-800 flex items-center">
                <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to My Orders
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6 mb-6">
            <h1 class="text-xl sm:text-2xl font-bold mb-4">Order #<?php echo htmlspecialchars($order['order_number']); ?></h1>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 mb-6">
                <div>
                    <h2 class="text-gray-600 font-semibold mb-2">Order Information</h2>
                    <p><span class="font-semibold">Ordered:</span> <?php echo htmlspecialchars($order['formatted_order_date']); ?></p>
                    <p><span class="font-semibold">Last Updated:</span> <?php echo htmlspecialchars($order['last_update']); ?></p>
                    <?php if ($order['estimated_delivery']): ?>
                        <p><span class="font-semibold">Estimated Delivery:</span> <?php echo htmlspecialchars($order['delivery_date']); ?></p>
                    <?php endif; ?>
                </div>
                
                <div>
                    <h2 class="text-gray-600 font-semibold mb-2">Delivery Details</h2>
                    <p><span class="font-semibold">Delivery Method:</span> <?php echo htmlspecialchars($order['delivery_method']); ?></p>
                    <p><span class="font-semibold">Address:</span> <?php echo htmlspecialchars($order['address']); ?></p>
                    <p><span class="font-semibold">Contact:</span> <?php echo htmlspecialchars($order['mobile']); ?></p>
                </div>
            </div>

            <div class="relative mb-6">
                <div class="tracking-line">
                    <div class="tracking-line-progress" style="width: <?php echo $progress_percentage . '%'; ?>"></div>
                </div>

                <div class="flex justify-between sm:justify-around relative space-x-2 sm:space-x-0 overflow-x-auto">
                    <?php 
                    foreach ($stages as $stage_key => $stage): 
                        $stage_index = array_search($stage_key, $stages_array);
                        $is_completed = $stage_index <= $current_index;
                        $is_current = $stage_key === $current_stage;
                    ?>
                    <div class="flex flex-col items-center min-w-[80px] w-full sm:w-32">
                        <div class="stage-icon rounded-full p-2 <?php echo $is_completed ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-400'; ?> <?php echo $is_current ? 'ring-4 ring-blue-200' : ''; ?>">
                            <i data-feather="<?php echo $stage['icon']; ?>" class="w-6 h-6 <?php echo $is_completed ? 'text-white' : 'text-gray-500'; ?>"></i>
                        </div>
                        <div class="text-center mt-2">
                            <p class="font-semibold text-sm <?php echo $is_completed ? 'text-blue-600' : 'text-gray-400'; ?>"><?php echo htmlspecialchars($stage['title']); ?></p>
                            <p class="text-xs text-gray-500 mt-1"><?php echo htmlspecialchars($stage['description']); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php if ($order['tracking_notes']): ?>
            <div class="bg-gray-50 rounded-lg p-4 sm:p-6">
                <h2 class="text-gray-600 font-semibold mb-2">Tracking Notes</h2>
                <p class="text-gray-700"><?php echo nl2br(htmlspecialchars($order['tracking_notes'])); ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        feather.replace();
    </script>
</body>
</html>
