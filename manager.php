<?php
// Start the session and check if manager is logged in
session_start();
if (!isset($_SESSION['manager_logged_in']) || $_SESSION['manager_logged_in'] !== true) {
    // Redirect to login page if not logged in
    header("Location: manager_login.php");
    exit();
}

// Include your database settings
require_once("settings.php");
require_once("fetch_orders.php");

// Initialize variables
$error_message = "";
$success_message = "";
$order_results = [];

// Connect to the database
$conn = new mysqli($host, $user, $pwd, $sql_db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Determine the current sorting order and set the arrow accordingly
$arrow_order_id = '';
$arrow_order_time = '';
$arrow_full_name = '';
$arrow_product_name = ''; // New for product name
$arrow_product_cost = '';
$arrow_order_cost = '';
$arrow_order_status = '';

// Check the current sort column and order
if (isset($_GET['sort'])) {
    $current_order = ($_GET['order'] === 'asc') ? 'desc' : 'asc';

    switch ($_GET['sort']) {
        case 'order_id':
            $arrow_order_id = ($_GET['order'] === 'asc') ? '&#9650;' : '&#9660;'; // Up or Down arrow
            break;
        case 'order_time':
            $arrow_order_time = ($_GET['order'] === 'asc') ? '&#9650;' : '&#9660;';
            break;
        case 'full_name':
            $arrow_full_name = ($_GET['order'] === 'asc') ? '&#9650;' : '&#9660;';
            break;
        case 'product_name': // New for product name
            $arrow_product_name = ($_GET['order'] === 'asc') ? '&#9650;' : '&#9660;';
            break;
        case 'product_cost':
            $arrow_product_cost = ($_GET['order'] === 'asc') ? '&#9650;' : '&#9660;';
            break;
        case 'order_cost':
            $arrow_order_cost = ($_GET['order'] === 'asc') ? '&#9650;' : '&#9660;';
            break;
        case 'order_status':
            $arrow_order_status = ($_GET['order'] === 'asc') ? '&#9650;' : '&#9660;';
            break;
    }
}

// Initialize criteria to empty values by default
$criteria = [
    'customer_name' => '',
    'product_name' => '',
    'status' => '',
    'sort_by' => ''
];

// Check if the form is submitted or if the page is loaded without any POST data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $criteria = [
        'customer_name' => isset($_POST['customer_name']) ? $_POST['customer_name'] : '',
        'product_name'  => isset($_POST['product_name']) ? $_POST['product_name'] : '',
        'status'        => isset($_POST['status']) ? $_POST['status'] : '',
        'sort_by'       => isset($_POST['sort_by']) ? $_POST['sort_by'] : ''
    ];
}

// Fetch orders based on criteria, or fetch all orders if no criteria are set
$order_results = fetch_orders($conn, $criteria);

$orders = []; // Initialize an empty array to store processed order data

while ($row = $order_results->fetch_assoc()) {
    $order_id = $row['order_id'];

    // Group orders by order_id
    if (!isset($orders[$order_id])) {
        $orders[$order_id] = [
            'order' => [
                'order_id' => $row['order_id'],
                'order_time' => $row['order_time'],
                'order_cost' => $row['order_cost'],
                'order_status' => $row['order_status'],
                'full_name' => $row['full_name']
            ],
            'products' => [] // Initialize empty product array
        ];
    }

    // Add product details to the products array for this order
    $orders[$order_id]['products'][] = [
        'product_name' => $row['product_name'],
        'product_cost' => $row['product_cost']
    ];
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Order Report</title>
    <link rel="stylesheet" href="styles/manager.css">
</head>

<body>

    <div class="container">
        <div class="header">
            <h1>Manager Order Report</h1>
            <div class="logout-container">
                <a href="logout.php" class="status-update">Logout</a>
            </div>
        </div>

        <?php if (isset($_GET['cancel_success']) && $_GET['cancel_success'] == 1): ?>
            <p class="success">Order successfully canceled.</p>
        <?php elseif (isset($_GET['error'])): ?>
            <?php if ($_GET['error'] == 'cannot_cancel'): ?>
                <p class="error">Only pending orders can be canceled.</p>
            <?php elseif ($_GET['error'] == 'order_not_found'): ?>
                <p class="error">Order not found.</p>
            <?php elseif ($_GET['error'] == 'delete_failed'): ?>
                <p class="error">Failed to cancel the order. Please try again.</p>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Display any messages -->
        <?php if (!empty($error_message)): ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <?php if (!empty($success_message)): ?>
            <p class="success"><?php echo $success_message; ?></p>
        <?php endif; ?>

        <!-- Search Form -->
        <form class="search-form" method="post" action="manager.php">
            <input type="text" name="customer_name" placeholder="Search by Customer Name">
            <input type="text" name="product_name" placeholder="Search by Product Name">
            <select name="status">
                <option value="">Select Status</option>
                <option value="PENDING">Pending</option>
                <option value="FULFILLED">Fulfilled</option>
                <option value="PAID">Paid</option>
                <option value="ARCHIVED">Archived</option>
            </select>
            <button type="submit">Search</button>
        </form>

        <!-- Order Table -->
        <table>
            <tr>
                <th><a href="?sort=order_id&order=<?php echo (isset($_GET['sort']) && $_GET['sort'] == 'order_id' && $_GET['order'] == 'asc') ? 'desc' : 'asc'; ?>">Order Number <?php echo $arrow_order_id; ?></a></th>
                <th><a href="?sort=order_time&order=<?php echo (isset($_GET['sort']) && $_GET['sort'] == 'order_time' && $_GET['order'] == 'asc') ? 'desc' : 'asc'; ?>">Order Date & Time <?php echo $arrow_order_time; ?></a></th>
                <th><a href="?sort=full_name&order=<?php echo (isset($_GET['sort']) && $_GET['sort'] == 'full_name' && $_GET['order'] == 'asc') ? 'desc' : 'asc'; ?>">Customer Name <?php echo $arrow_full_name; ?></a></th>
                <th><a href="?sort=product_name&order=<?php echo (isset($_GET['sort']) && $_GET['sort'] == 'product_name' && $_GET['order'] == 'asc') ? 'desc' : 'asc'; ?>">Product Name <?php echo $arrow_product_name; ?></a></th>
                <th><a href="?sort=product_cost&order=<?php echo (isset($_GET['sort']) && $_GET['sort'] == 'product_cost' && $_GET['order'] == 'asc') ? 'desc' : 'asc'; ?>">Product Cost <?php echo $arrow_product_cost; ?></a></th>
                <th><a href="?sort=order_cost&order=<?php echo (isset($_GET['sort']) && $_GET['sort'] == 'order_cost' && $_GET['order'] == 'asc') ? 'desc' : 'asc'; ?>">Total Cost <?php echo $arrow_order_cost; ?></a></th>
                <th><a href="?sort=order_status&order=<?php echo (isset($_GET['sort']) && $_GET['sort'] == 'order_status' && $_GET['order'] == 'asc') ? 'desc' : 'asc'; ?>">Order Status <?php echo $arrow_order_status; ?></a></th>
                <th>Update Status</th>
                <th>Cancel Order</th>
            </tr>

            <?php if (!empty($orders)): ?>
                <?php foreach ($orders as $order_data): ?>
                    <?php $row = $order_data['order']; ?>
                    <tr>
                        <!-- Display order details once per order -->
                        <td rowspan="<?php echo count($order_data['products']); ?>"><?php echo $row['order_id']; ?></td>
                        <td rowspan="<?php echo count($order_data['products']); ?>"><?php echo $row['order_time']; ?></td>
                        <td rowspan="<?php echo count($order_data['products']); ?>"><?php echo $row['full_name']; ?></td>

                        <!-- Display first product -->
                        <td><?php echo $order_data['products'][0]['product_name']; ?></td>
                        <td><?php echo $order_data['products'][0]['product_cost']; ?></td>
                        <td rowspan="<?php echo count($order_data['products']); ?>"><?php echo $row['order_cost']; ?></td>
                        <td rowspan="<?php echo count($order_data['products']); ?>"><?php echo $row['order_status']; ?></td>
                        <!-- Update Status Form -->
                        <td rowspan="<?php echo count($order_data['products']); ?>">
                            <form class="search-form" action="order_status.php" method="POST">
                                <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
                                <select name="order_status" class="status-select">
                                    <option value="PENDING" <?php if ($row['order_status'] == 'PENDING') echo 'selected'; ?>>Pending</option>
                                    <option value="FULFILLED" <?php if ($row['order_status'] == 'FULFILLED') echo 'selected'; ?>>Fulfilled</option>
                                    <option value="PAID" <?php if ($row['order_status'] == 'PAID') echo 'selected'; ?>>Paid</option>
                                    <option value="ARCHIVED" <?php if ($row['order_status'] == 'ARCHIVED') echo 'selected'; ?>>Archived</option>
                                </select>
                                <button type="submit" class="status-update">Update</button>
                            </form>
                        </td>
                        <!-- Cancel Order Link -->
                        <td rowspan="<?php echo count($order_data['products']); ?>">
                            <a href="cancel_order.php?order_id=<?php echo $row['order_id']; ?>" class="delete-order" onclick="return confirm('Are you sure you want to cancel this order?')">Cancel</a>
                        </td>
                    </tr>

                    <!-- Display remaining products -->
                    <?php for ($i = 1; $i < count($order_data['products']); $i++): ?>
                        <tr>
                            <td><?php echo $order_data['products'][$i]['product_name']; ?></td>
                            <td><?php echo $order_data['products'][$i]['product_cost']; ?></td>
                        </tr>
                    <?php endfor; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9">No orders found.</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>

</body>

</html>